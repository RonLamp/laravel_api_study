<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *  @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->modelo->all(), 200);
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
        $request->validate($this->modelo->rules(), $this->modelo->feedback());
        // Execução da ação propriamente dita
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');
        //$modelo = $this->modelo->create($request->all());
        $modelo = $this->modelo->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'marca_id'=> $request->marca_id, 
            'numero_portas'=> $request->numero_portas, 
            'lugares'=> $request->lugares,
            'air_bag'=> $request->air_bag,
            'abs'=> $request->abs
        ]);
        return response()->json($modelo, 201);
    }

    /** 
     * Mostrando um recurso específico em função do id
     * Display the specified resource.
     * @param Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo===null){
            return response()
            ->json(['error'=>'Recurso pesquisado inexistente!'], 404);
        }
        return response()->json($modelo, 200);
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
        $modelo = $this->modelo->find($id);
        if($modelo===null){
            return response()
            ->json(['error'=>'Atualização indisponível, recurso pesquisado inexistente!'], 404);
        }
        // verifica as rules cfe o model atraves de Dynamic Rules
        $dinamycRules = array();
        foreach($modelo->rules() as $input => $regra){
            if(array_key_exists($input, $request->all())){
                $dinamycRules[$input] = $regra;
            }
        }
        $request->validate($dinamycRules, $modelo->feedback());

        // exclusão da imagem antiga se um novo existir
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
            // carregamento dos valores na variavel $modelo
            //$imagem = $request->file('imagem');
            $request->imagem = $request
                        ->file('imagem')
                        ->store('imagens/modelos', 'public');
        } 
         // else { $imagem_urn = $modelo->imagem;
        // }
        //dd($request->lugares);
        // Execução da ação propriamente dita
        //$modelo->update($new)
        // $modelo->update([
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn,
        //     'marca_id'=> $id, // $request->marca_id, 
        //     'numero_portas'=> $request->numero_portas, 
        //     'lugares'=> $request->lugares,
        //     'air_bag'=> $request->air_bag,
        //     'abs'=> $request->abs
        // ]);
        //$new = $request->all();
        //$old = $modelo->getAttributes();
        //dd(array_replace($old,$new));
        $new = array_replace($modelo->getAttributes(),$request->all());
        unset($new['_method']);
        $modelo->update($new);
        //$modelo->update($request->all());
        //return response()->json($modelo, 200);
        return response()->json($request->all(), 200);
    }

    /**
     * Deleta ou destroy um recurso específico.
     * Remove the specified resource from storage.
     * @param Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo===null){
            return response()
            ->json(['error'=>'Recurso já não existente!'], 404);
        }
        Storage::disk('public')->delete($modelo->imagem);
        $modelo->delete($id);
        return response()->json(['msg'=>'O modelo foi removido com sucesso!'], 200);;
    }
}
