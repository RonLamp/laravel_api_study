<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = [
        'marca_id', 
        'nome','imagem',
        'numero_portas', 
        'lugares','air_bag','abs'
    ];

    public function rules(){
        return [
                'marcda_id'=>'exists:marcas,id',
                'nome'=>'required|unique:modelos,nome,'.$this->id.'|min:3',
                'imagem'=>'required|file|mimes:png,jpg,jpeg',
                'numero_portas'=>'required|integer|digits_between:1,5',  // 1,2,3,4,5 
                'lugares'=>'required|integer|digits_between:1,10', 
                'air_bag'=>'required|boolean', // false, true, 0, 1, "0" ou "1"
                'abs'=>'required|boolean'
            ];
        /* Os parametros da pesquisa unique são:
            1) tabela onde será feita a pesquisa,
            2) nome da coluna que será feita a pesquisa e
            3) id do registro que será desconsiderado na pesquisa
        */
    }

    public function feedback(){
        return  [
                'required' => 'O campo :attribute é obrigatório.',
                'nome.unique' => 'O nome do modelo já existe.',
                'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',
                'imagem.file' => 'O campo imagem deve conter um arquivo válido.',
                'imagem.mime' => 'O arquivo deve ser do tipo: png, jpg ou jpeg.'
            ];
    }

    public function marca(){
        // Um modelo pertence a uma marca.
        return $this->belogsTo('App\Models\Marca');
    }
}
