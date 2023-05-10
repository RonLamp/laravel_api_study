<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aqui é onde voce pode registrar API routes para a sua aplicação.
| Estas routes são loaded pelo RouteServiceProvider e todas elas
| serão marcadas no "api" middleware group. Make something great!
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('/', function () {
//     return ['msg'=>'Chegamos até aqui!!!'];
// });
// Route::prefix('v1')->middleware('jwt.auth')->group(function(){
//     Route::apiResource('cliente','App\Http\Controllers\ClienteController');
//     Route::apiResource('carro','App\Http\Controllers\CarroController');
//     Route::apiResource('locacao','App\Http\Controllers\LocacaoController');
//     Route::apiResource('marca','App\Http\Controllers\MarcaController');
//     Route::apiResource('modelo','App\Http\Controllers\ModeloController');
// });
Route::middleware('jwt.auth')->group(function(){
    Route::apiResource('cliente','App\Http\Controllers\ClienteController');
    Route::apiResource('carro','App\Http\Controllers\CarroController');
    Route::apiResource('locacao','App\Http\Controllers\LocacaoController');
    Route::apiResource('marca','App\Http\Controllers\MarcaController');
    Route::apiResource('modelo','App\Http\Controllers\ModeloController');

    Route::post('me', 'App\Http\Controllers\AuthController@me');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
});

Route::post('login', 'App\Http\Controllers\AuthController@login');


