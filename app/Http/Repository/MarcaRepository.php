<?php
namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;

class MarcaRepository {

     public function __construct(Model $model){
          $this->model = $model;

     }
}