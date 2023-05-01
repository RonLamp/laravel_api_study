<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marcas = Marca::all();
        return $marcas;
    }

        /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $marca = Marca::create($request->all());
        return $marca;
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        $marcas = Marca::find($marca);
        return $marcas;
    }

       /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        print_r($request->all()); // os dados atualizados
        echo '<hr>';
        print_r($marca->getAttributes());  //os dados antigos
        // $marca = Marca::update()
        return 'chegamos até aqui update()';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        //
    }
}
