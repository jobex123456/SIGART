<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Access;
use App\Unity;

class UnityController extends Controller
{
    protected $_moduleDB = 'unity';

    public function index(Request $request){
        if(!$request->ajax()) return redirect('/');
        $num_per_page = 20;
        $search = $request->buscar;
        if($search != ""){
            $unity = Unity::where('status', '<>', 2)
                ->where('name', 'like', '%'.$search.'%')
                ->orderBy('name', 'asc')
                ->paginate($num_per_page);
        }else{
            $unity = Unity::where('status', '<>', 2)
                ->orderBy('name', 'asc')
                ->paginate($num_per_page);
        }

        return [
            'pagination' => [
                'total' => $unity->total(),
                'current_page' => $unity->currentPage(),
                'per_page' => $unity->perPage(),
                'last_page' => $unity->lastPage(),
                'from' => $unity->firstItem(),
                'to' => $unity->lastItem()
            ],
            'records' => $unity
        ];
    }

    public function dashboard(){
        $permiso = Access::sideBar();
        return view('modules/pages', [
            "menu" => 11,
            'sidebar' => $permiso,
            "moduleDB" => $this->_moduleDB
        ]);
    }

    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');
        $unity = new Unity();
        $unity->name = $request->nombre;
        $unity->root = 0;
        $unity->status = 1;
        $unity->save();
        $this->logAdmin("Registro una nueva Unidad:",$unity);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $unity = Unity::findOrFail($request->id);
        $unity->name = $request->nombre;
        $unity->save();
        $this->logAdmin("Actualizó los datos de la unidad:",$unity);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $unity = Unity::findOrFail($request->id);
        $unity->status = 0;
        $unity->save();
        $this->logAdmin("Desactivó la unidad:".$unity->id);
    }

    public function activate(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $unity = Unity::findOrFail($request->id);
        $unity->status = 1;
        $unity->save();
        $this->logAdmin("Activó la unidad:".$unity->id);
    }

    public function delete(Request $request){
        if(!$request->ajax()) return redirect('/');
        $unity = Unity::findOrFail($request->id);
        $unity->status = 2;
        $unity->save();
        $this->logAdmin("Dió de baja la unidad:".$unity->id);
    }

    public function select(Request $request){
        if(!$request->ajax()) return redirect('/');

        $unitis = Unity::where('status', 1)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return ['unitis' => $unitis];
    }
}
