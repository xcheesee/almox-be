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
Route::put('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'update']);
Route::delete('entrada/{id}', [App\Http\Controllers\EntradaController::class, 'destroy']);
