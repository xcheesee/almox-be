<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('cadastrar', [App\Http\Controllers\Auth\AuthController::class, 'cadastrar']);
Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    /**
     * Exibe dados do usuário logado de acordo com o Token enviado
     * @authenticated
     *
     * @header Authorization Bearer 5|02KLXZaRYzgJybyy2rMTRKXKIOuuE3EylnT7JQVv
     *
     * @response 200 {
     *     "id": 1,
     *     "name": "Admin NDTIC",
     *     "email": "tisvma@prefeitura.sp.gov.br",
     *     "email_verified_at": null,
     *     "ativo": 1,
     *     "created_at": "2022-03-23T19:06:48.000000Z",
     *     "updated_at": "2022-03-23T19:06:48.000000Z"
     * }
     *
     * @response 401 {
     *     "message":"Unauthenticated."
     * }
     */
    Route::get('perfil', function(Request $request) {
        return auth()->user();
    });//return $request->user();
});

Route::get('entradas', [App\Http\Controllers\EntradaController::class, 'index']);
Route::post('entrada', [App\Http\Controllers\EntradaController::class, 'store']);
Route::get('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'show']);
Route::post('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'update']);
Route::delete('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'destroy']);
Route::get('entrada/{id}/items', [App\Http\Controllers\EntradaItemController::class, 'items_entrada']);

Route::get('inventario', [App\Http\Controllers\InventarioController::class, 'index']);
Route::post('inventario', [App\Http\Controllers\InventarioController::class, 'store']);
Route::get('inventario/{id}', [App\Http\Controllers\InventarioController::class, 'show']);
Route::put('inventario/{id}', [App\Http\Controllers\InventarioController::class, 'update']);
Route::delete('inventario/{id}', [App\Http\Controllers\InventarioController::class, 'destroy']);

Route::get('ordem_servico', [App\Http\Controllers\OrdemServicoController::class, 'index']);
Route::post('ordem_servico', [App\Http\Controllers\OrdemServicoController::class, 'store']);
Route::get('ordem_servico/{id}', [App\Http\Controllers\OrdemServicoController::class, 'show']);
Route::put('ordem_servico/{id}', [App\Http\Controllers\OrdemServicoController::class, 'update']);
Route::delete('ordem_servico/{id}', [App\Http\Controllers\OrdemServicoController::class, 'destroy']);
