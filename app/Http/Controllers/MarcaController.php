<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
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
        return response()->json($this->marca->all(), 200);
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
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');
        //$marca = $this->marca->create($request->all());
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca, 201);
        //dd($request->nome);
        //dd($request->get('nome'));
        //dd($request->imagem);
        //dd($request->file('imagem'));
        //dd($request->all());
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
        //dd($request->nome);
        //dd($request->file('imagem'));
        if($marca===null){
            return response()
            ->json(['error'=>'Atualização indisponível, recurso pesquisado inexistente!'], 404);
        }
        // Validação dos parametros nome e imagem
        // levando em conta se o método é PUT ou PATCH
        //-------------------------------------------
        // Como o Laravel tem uma restrição de uso quando o formulario de envio
        // usa o form-data para envio de arquivos a linha abaixo foi alterada.
        // devemos enviar a requisição pelo método POST e incluir a variável
        // _method com a definição se é PUT ou PATCH
        if($request->_method === 'PATCH'){
        //if($request.method() === 'PATCH'){
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
        // exclusão da imagem antiga se um novo existir
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
        }
        // carregamento dos valores na variavel $marca
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');
        // Execução da ação propriamente dita
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        //$marca->update($request->all());
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
        Storage::disk('public')->delete($marca->imagem);
        $marca->delete($id);
        return response()->json(['msg'=>'A marca foi removida com sucesso!'], 200);;
    }
}
