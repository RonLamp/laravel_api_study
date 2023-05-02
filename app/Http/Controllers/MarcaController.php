<?php
namespace App\Http\Controllers;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function __construct(Marca $marca){
        $this->marca = $marca;
    }

    /**
     * Mostrando a lista de recursos
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marcas = $this->marca->all();
        return response()->json($marcas, 200);
    }

    /**
     * Armazenando um novo recurso
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validação dos parametros nome e imagem
        $request->validate($this->marca->rules(), $this->marca->feedback());
        // Execução da ação propriamente dita
        $marca = $this->marca->create($request->all());
        return response()->json($marca, 201);
    }
    
    /** 
     * Mostrando um recurso específico em função do id
     * Display the specified resource.
     * @param Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->find($id);
        if($marca===null){
            return response()
            ->json(['error'=>'Recurso pesquisado inexistente!'], 404);
        }
        return response()->json($marca, 200);
    }

    /**
     * Atualizando um recurso específico em função do id
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request  $request
     * @param Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Localizando o resource (registro)
        $marca = $this->marca->find($id);
        if($marca===null){
            return response()
            ->json(['error'=>'Atualização indisponível, recurso pesquisado inexistente!'], 404);
        }
        // Validação dos parametros nome e imagem
        //    levando em conta se o método é PUT ou PATCH
        if($request.method() === 'PATCH'){
            $dinamycRules = array();
            // percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $regra){
                // coletar somente as regras aplicaveis
                if(array_key_exists($input, $request->all())){
                    $dinamycRules[$input] = $regra;
                }
            }
            $request->validate($dinamycRules, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }
        // Execução da ação propriamente dita
        $marca->update($request->all());
        return response()->json($marca, 200);
    }

    /**
     * Deleta ou destroy um recurso específico.
     * Remove the specified resource from storage.
     * @param Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if($marca===null){
            return response()
            ->json(['error'=>'Recurso já não existente!'], 404);
        }
        $marca->delete($id);
        return response()->json(['msg'=>'A marca foi removida com sucesso!'], 200);;
    }
}
