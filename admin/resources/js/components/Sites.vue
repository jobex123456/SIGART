<template>

    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Sedes</h3>
                    <div class="tile-body">
                        <form class="row">
                            <div class="form-group col-md-6">
                                <input class="form-control" v-model="buscar" type="text" placeholder="Buscar" @keyup="listar(1, buscar)">
                            </div>
                            <div class="form-group col-md-3 align-self-end">
                                <button class="btn btn-primary" type="button" @click="listar(1, buscar)">
                                    <i class="fa fa-fw fa-lg fa-search"></i>Buscar
                                </button>
                            </div>
                            <div class="form-group col-md-3 align-self-end">
                                <button class="btn btn-success" type="button" @click="abrirModal('registrar')">
                                    <i class="fa fa-fw fa-lg fa-plus"></i>Nuevo Sede
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Listado de iconos</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Nombre</th>
                                <th>Direccion</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="dato in arreglo" :key="dato.id">
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" @click="abrirModal('actualizar', dato)">
                                        <i class="fa fa-edit"></i>
                                    </button> &nbsp;
                                    <button type="button" class="btn btn-danger btn-sm" @click="eliminar(dato.id)">
                                        <i class="fa fa-trash-o"></i>
                                    </button> &nbsp;
                                    <template v-if="dato.status == 1">
                                        <button type="button" class="btn btn-success btn-sm" @click="desactivar(dato.id)">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button type="button" class="btn btn-warning btn-sm" @click="activar(dato.id)">
                                            <i class="fa fa-ban"></i>
                                        </button>
                                    </template>
                                </td>
                                <td v-text="dato.name"></td>
                                <td v-text="dato.address"></td>
                                <td>
                                    <div v-if="dato.status">
                                        <span class="badge badge-success">Activo</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Desactivado</span>
                                    </div>
                                </td>
                                
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page-1, buscar)">Ant.</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, buscar)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page+1, buscar)">Sig.</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <b-modal id="modalPrevent" size="lg" ref="modal" title="Registro de iconos" @ok="processForm">
            <form @submit.stop.prevent="cerrarModal">
                <div class="form-group row">
                    <label class="col-md-3 form-control-label" for="text-input">Nombre <span class="text-danger">(*)</span></label>
                    <div class="col-md-9">
                        <input type="text" v-model="nombre" name="nombre" v-validate="'required'" class="form-control" placeholder="Nombre Sede" :class="{'is-invalid': errors.has('nombre')}">
                        <span v-show="errors.has('nombre')" class="text-danger">{{ errors.first('nombre') }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 form-control-label" for="text-input">Dirección <span class="text-danger">(*)</span></label>
                    <div class="col-md-9">
                        <input type="text" v-model="direccion" name="direccion" v-validate="'required'" class="form-control" placeholder="Dirección" :class="{'is-invalid': errors.has('direccion')}">
                        <span v-show="errors.has('direccion')" class="text-danger">{{ errors.first('direccion') }}</span>
                    </div>
                </div>
            </form>
        </b-modal>
    </div>
</template>
<script>
import vSelect  from 'vue-select';
export default {
    name: 'sites-adm',
    data(){
        return{
            url: '/sites',
            id: 0,
            action: 'registrar',
            nombre: "",
            direccion: "",
            arreglo: [],
            modalTitulo: '',
            modal: 0,
            tipoAccion: 0,
            pagination : {
                'total' : 0,
                'current_page' : 0,
                'per_page' : 0,
                'last_page' : 0,
                'from' : 0,
                'to' : 0,
            },
            offset : 3,
            criterio : 'nombre',
            buscar : ''
        }
    },
    computed:{
        isActived: function(){
            return this.pagination.current_page;
        },
        //Calcula los elementos de la paginación
        pagesNumber: function() {
            if(!this.pagination.to) {
                return [];
            }
            
            var from = this.pagination.current_page - this.offset; 
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2); 
            if(to >= this.pagination.last_page){
                to = this.pagination.last_page;
            }  

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;             

        }
    },
    components:{
        vSelect
    },
    methods:{
        update_side_bar(idSideBar, datos = {}){
            this.$emit('update_side_bar', idSideBar, datos);
        },
        listar(page,buscar){
            var me = this;
            var url= me.url+'?page=' + page + '&buscar='+ buscar;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arreglo = respuesta.records.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listar(page,buscar);
        },
        abrirModal(action, data=[]){
            switch(action){
                case 'registrar':
                    this.modal = 1;
                    this.tipoAccion = 1;
                    this.id = 0;
                    this.direccion = '';
                    this.modalTitulo = 'Registrar Sede';
                    this.nombre = '';
                    this.$refs.modal.show();
                break;
                case 'actualizar':
                    this.modal = 1;
                    this.tipoAccion = 2;
                    this.id = data.id;
                    this.direccion = data.address;
                    this.modalTitulo = 'Actualizar Sede - '+data.name;
                    this.nombre = data.name;
                    this.action = 'actualizar';
                    this.$refs.modal.show();
                break;
            }
        },
        cerrarModal(){
            this.modal = 0;
            this.modalTitulo = '';
            this.nombre = '';
             this.$nextTick(() => {
                // Wrapped in $nextTick to ensure DOM is rendered before closing
                this.$refs.modal.hide();
            })
        },
         processForm(evt){
            evt.preventDefault();
            switch(this.action){
                case 'registrar':
                    this.registrar();
                    break;
                case 'actualizar':
                    this.actualizar();
                    break;
            }
        },
        registrar(){
            this.$validator.validateAll().then((result) => {
                if (result) {
                    let me = this;
                    axios.post( me.url + '/register',{
                        'nombre': this.nombre,
                        'address': this.direccion
                    }).then(function (response) {
                        me.cerrarModal();
                        me.listar(1,'');
                    }).catch(function (error) {
                         console.log(error);
                    });
                }
            });
        },
        actualizar(){
            this.$validator.validateAll().then((result) => {
                if (result) {
                    let me = this;
                    axios.put( me.url + '/update',{
                        'id': this.id,
                        'nombre': this.nombre,
                        'address': this.direccion
                    }).then(function (response) {
                        me.cerrarModal(); 
                        me.listar(1,'');
                    }).catch(function (error) {
                        console.log(error);
                    });
                }
            });
        },
        activar(id){
            swal({
                 title: "Activar Sede!",
                text: "Esta seguro de activar esta Sede?",
                icon: "success",
                button: "Activar"
            }).then((result) => {
                if (result) {
                    let me = this;
                    axios.put( me.url + '/activate',{
                        'id': id
                    }).then(function (response) {
                        me.listar(1,'');
                        swal(
                        'Activado!',
                        'El registro ha sido activado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                }
            })
        },
        desactivar(id){
            swal({
                title: "Desactivar Sede!",
                text: "Esta seguro de desactivar este Sede?",
                icon: "warning",
                button: "Desactivar",
            }).then((result) => {
                if (result) {
                    let me = this;
                    axios.put(me.url + '/deactivate',{
                        'id': id
                    }).then(function (response) {
                        me.listar(1,'');
                        swal(
                        'Desactivado!',
                        'El registro ha sido desactivado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                }
            })
        },
        eliminar(id){
            swal({
                title: "Eliminar!",
                text: "Esta seguro de eliminar este Sede?",
                icon: "danger",
                button: "Eliminar"
            }).then((result) => {
                if (result) {
                    let me = this;
                    axios.put( me.url + '/delete',{
                        'id': id
                    }).then(function (response) {
                        me.listar(1,'');
                        swal(
                        'Eliminado!',
                        'El registro ha sido eliminado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                }
            })
        }
    },
    mounted() {
        this.listar(1,this.buscar);
    }
}
</script>