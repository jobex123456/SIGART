<?php

namespace App\Http\Controllers;

use App\Access;
use \App\Presentation;
use App\Product;
use Illuminate\Http\Request;

class PresentationController extends Controller
{
    protected $_moduleDB = 'presentation';
    protected $_page = 14;

    public function dashboard( Request $request ){
        $breadcrumb = [
            [
                'name' => 'Productos',
                'url' => route( 'products.index' )
            ],
            [
                'name' => 'Presentación',
                'url' => '#'
            ]
        ];

        $permiso = Access::sideBar( $this->_page );
        return view('mintos.content', [
            "menu"          => $this->_page,
            'sidebar'       => $permiso,
            "moduleDB"      => $this->_moduleDB,
            'breadcrumb'    => $breadcrumb,
            'product'       => $request->id
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $num_per_page = 20;
        $response = Presentation::where('presentation.status', '!=', 2)
            ->where('products.status', '!=', 2 )
            ->where('unity.status', '!=', 2 )
            ->where('products_id', $request->id )
            ->join( 'products', 'products.id', 'presentation.products_id')
            ->join( 'unity', 'unity.id', 'presentation.unity_id')
            ->join( 'categories', 'categories.id', '=', 'products.category_id' )
            ->select(
                'presentation.id',
                'presentation.products_id',
                'presentation.sku',
                'presentation.unity_id',
                'presentation.equivalence',
                'categories.name as category',
                'unity.name as unity_name'
            )
            ->selectRaw(
                'concat( categories.name, \' \', products.name, \' \', presentation.description ) as name'
            )
            ->orderBy('id', 'asc')
            ->paginate($num_per_page);

        return response()->json(
            [
                'pagination' => [
                    'total' => $response->total(),
                    'current_page' => $response->currentPage(),
                    'per_page' => $response->perPage(),
                    'last_page' => $response->lastPage(),
                    'from' => $response->firstItem(),
                    'to' => $response->lastItem()
                ],
                'records' => $response
            ]
        );
    }

    public function select( Request $request ){
        $presentation = Presentation::where('status', '=', 1)
            ->where('products_id', $request->id )
            ->select('id', 'products_id', 'unity_id', 'description', 'equivalence')
            ->orderBy('id', 'asc')
            ->get();

        $newArray = [];
        foreach( $presentation as $idx => $pres ){
            $newArray[] = [
                'id' => $idx,
                'description' => $pres->description,
                'unity' => $pres->unity_id,
                'equivalence' => $pres->equivalence,
                'delete' => 0,
                'idTable'=> $pres->id
            ];
        }

        return response()->json([
            'presentation' => $newArray
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request )
    {
        $presentation = Presentation::findOrFail( $request->id );
        $presentation->status = 2;
        $presentation->save();

        return response()->json([
            'status' => true
        ]);
    }

    public function search( Request $request ) {

        $response = [
            'status'    => false,
            'msg'       => '',
            'data'      => []
        ];

        $search = $request->search;

        if( ! empty( $search ) &&  strlen( $search ) >= 4 ) {

            $data = Presentation::where( 'status', '!=', 2 )
                ->with( 'unity:id,name' )
                ->with( 'product:id,name' )
                ->with( 'product.category:id,name')
                ->where( function ( $sq ) use( $search ) {
                    $sq->where( 'presentation.sku', 'like', '%' . $search . '%' )
                        ->orWhere( 'presentation.description', 'like', '%' . $search . '%' )
                        ->orWhereHas( 'product', function( $sq2 ) use( $search ) {
                            $sq2->where( 'name', 'like', '%' . $search . '%' )
                                ->where( 'status' , '!=', '2');
                        })
                        ->orWhereHas( 'unity', function( $sq3 ) use( $search ) {
                            $sq3->where( 'name', 'like', '%' . $search . '%')
                                ->where( 'status' , '!=', '2');
                        })
                        ->orWhereHas( 'product.category', function( $sq4 ) use( $search ) {
                            $sq4->where( 'name', 'like', '%' . $search . '%')
                                ->where( 'status' , '!=', '2');
                        });
                })
                ->get();

            foreach ( $data as $idx => $item ) {
                $row            = new \stdClass();
                $row->id        = $item->id;
                $row->sku       = $item->sku;
                $row->slug      = $item->slug;
                $row->name      = $item->description;
                $row->unity     = $item->unity->name;
                $row->product   = $item->product->name;
                $row->category  = $item->product->category->name;

                $response['data'][] = $row;
            }

            if( count( $response['data'] ) > 0 ) {
                $response['status'] = true;
                $response['msg'] = 'OK';
            } else {
                $response['msg'] = 'No se encontraron coincidencias.';
            }

        } else {
            $response['msg'] = 'Ingrese parametros de busqueda';
        }

        return response()->json( $response );
    }
}
