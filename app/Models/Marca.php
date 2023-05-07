<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome','imagem'];

    public function rules(){
        return [
                'nome'=>'required|unique:marcas,nome,'.$this->id.'|min:3',
                'imagem'=>'required|file|mimes:png,jpg,jpeg'
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
                'nome.unique' => 'O nome da marca já existe.',
                'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',
                'imagem.file' => 'O campo imagem deve conter um arquivo válido.',
                'imagem.mime' => 'O arquivo deve ser do tipo: png, jpg ou jpeg.'
            ];
    }

    public function modelos(){
        // Uma marca possui muitos modelos.
        return $this->hasMany('App\Models\Modelo');
    }
}
