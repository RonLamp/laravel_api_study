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
    public function index(Request $request)
    {
        $modelos = array();
        if($request->has('atributos_marca')){
            $atributos_marca = $request->atributos_marca;
            $modelo = $this->modelo->with('marca:id,'.$atributos_marca);
        } else{
            $modelo = $this->modelo; //->with('marca');
        }
        
        if($request->has('filtro')){
            // aqui temos um filtro simples
            // $cond = explode(':',$request->filtro);
            // $modelo = $modelo->where($cond[0],$cond[1],$cond[2]);
            // exemplos de filtros:
            // 1) nome:like:Ford%   2) numero_portas:>=:4 
            // 3) abs:=:true      4) nome:=:Ford KA 1.0
            // para termos multiplos filtros, cada filtro deve ser separados
            // pelo caracter ;
            // exemplo de multifiltros:
            // nome:like:Ford%;abs:=:1
            $filtros = explode(';',$request->filtro);
            foreach($filtros as $key => $condicao){
                $c = explode(':',$condicao);
                $modelo = $modelo->where($c[0],$c[1],$c[2]);
            }
        }

        if($request->has('atributos')){
            $atributos = $request->atributos;
            //$modelo = $this->modelo->select('id','nome','imagem')->get();
            //----------------------------------------
            // GET {{URL}}/api/modelo/?atributos=id,nome,imagem
            // $modelo = $this->modelo->selectRaw($atributos)->get();
            //----------------------------------------
            // GET {{URL}}/api/modelo/?atributos=id,nome,imagem,marca_id
            // $modelo = $this->modelo->selectRaw($atributos)->with('marca')->get();
            //----------------------------------------
            // GET {{URL}}/api/modelo/?atributos=id,nome,marca_id&atributos_marca=id,nome,imagem
            $modelo = $modelo->selectRaw($atributos)->get();
        } else {
            $modelo = $modelo->get();
        }
        return response()->json($modelo, 200);
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
        $modelo = $this->modelo->wiyh('marca')->find($id);
        //$modelo = $this->modelo->find($id);
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
            $imagem = $request->file('imagem');
            $imagemUrn = $imagem->store('imagens/modelos', 'public');
            $modelo->imagem = $imagemUrn;
        }
        $newRequest = $request->all();
        unset($newRequest['imagem']);
        //unset($newRequest['_method']);
        $new = array_replace($modelo->getAttributes(),$newRequest);
        $modelo->update($new);
        //---------  Outra alternativa  ------------------
        // preencher o objeto $marca com os dados do $request
        // $modelo->fill($request->all());
        // como o méthodo fill() só preenche com os campos existentes, o
        // campo '_method' não precisará ser unseted (), nem o campo 'imagem'
        // que será alterado posteriormente
        // $modelo->imagem = imagemUrn;
        // e por fim utilizar o methodo save() do proprio objeto, desde que 
        // o id do objeto esteja no próprio objeto.
        // caso contrario, sem id, o comando save() criará um novo registro.
        // $modelo->save();
        // return response()->json($modelo, 200);
        return response()->json($new, 200);
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
