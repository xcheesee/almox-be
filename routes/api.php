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
Route::post('alterar_senha', [App\Http\Controllers\Auth\AuthController::class, 'alterar_senha']);

Route::group(['middleware' => ['auth:sanctum']], function() {
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


    //listagens para criar combos/filtros (dependem de departamento/autorização)
    Route::get('tipo_items', [App\Http\Controllers\TipoItemController::class, 'index']);
    Route::get('locais', [App\Http\Controllers\LocalController::class, 'index']);
    Route::get('departamentos', [App\Http\Controllers\DepartamentoController::class, 'index']);

    //CRUDs
    Route::get('entradas', [App\Http\Controllers\EntradaController::class, 'index']);
    Route::post('entrada', [App\Http\Controllers\EntradaController::class, 'store']);
    Route::post('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'update']);
    Route::delete('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'destroy']);


    Route::get('inventarios', [App\Http\Controllers\InventarioController::class, 'index']);
    Route::get('items_acabando', [App\Http\Controllers\InventarioController::class, 'items_acabando']);

    Route::get('ordem_servicos', [App\Http\Controllers\OrdemServicoController::class, 'index']);
    Route::post('ordem_servico', [App\Http\Controllers\OrdemServicoController::class, 'store']);
    Route::get('ordem_servico/{id}', [App\Http\Controllers\OrdemServicoController::class, 'show']);
    Route::post('ordem_servico/{id}', [App\Http\Controllers\OrdemServicoController::class, 'update']);
    Route::delete('ordem_servico/{id}', [App\Http\Controllers\OrdemServicoController::class, 'destroy']);
    Route::get('ordem_servico/{id}/items', [App\Http\Controllers\OrdemServicoController::class, 'items_ordem']);
    Route::get('ordem_servico/{id}/profissionais', [App\Http\Controllers\OrdemServicoController::class, 'profissionais_ordem']);
    Route::post('ordem_servico/{id}/baixa', [App\Http\Controllers\OrdemServicoController::class, 'baixa']);

});

Route::get('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'show']);
Route::get('entrada/{id}/items', [App\Http\Controllers\EntradaItemController::class, 'items_entrada']);

Route::post('inventario', [App\Http\Controllers\InventarioController::class, 'store']);
Route::get('inventario/{id}', [App\Http\Controllers\InventarioController::class, 'show']);
Route::put('inventario/{id}', [App\Http\Controllers\InventarioController::class, 'update']);
Route::delete('inventario/{id}', [App\Http\Controllers\InventarioController::class, 'destroy']);

Route::get('ordem_servico/{id}/baixa_json', [App\Http\Controllers\OrdemServicoController::class, 'baixa_json']);
Route::get('ordem_servico/{id}/baixa_pdf', [App\Http\Controllers\OrdemServicoController::class, 'baixa_pdf']);

//listagens para criar combos/filtros
Route::get('items/tipo/{id}', [App\Http\Controllers\ItemController::class, 'item_por_tipo']);
Route::get('medidas', [App\Http\Controllers\MedidaController::class, 'index']);
Route::get('profissionais', [App\Http\Controllers\ProfissionalController::class, 'profissionais_local']);
Route::get('base/items', [App\Http\Controllers\InventarioController::class, 'items_local']);
