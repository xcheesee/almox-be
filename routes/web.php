<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix(env('APP_FOLDER', ''))->group(function () { //considerando que o projeto estará em subdiretório em homol/prod

    //Custom Login
    Route::get('/entrar', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('entrar');
    Route::post('/entrar', [App\Http\Controllers\Auth\LoginController::class, 'entrar']);
    Route::get('/trocasenha', [App\Http\Controllers\UserController::class, 'trocasenha'])->name('trocasenha')->middleware('autenticador');
    Route::post('/trocasenha', [App\Http\Controllers\UserController::class, 'alterarsenha'])->middleware('autenticador');
    // Route::get('/trocasenha', [App\Http\Controllers\Auth\RegisterController::class, 'criar'])->middleware('autenticador');
    // Route::post('/registrar', [App\Http\Controllers\Auth\RegisterController::class, 'create'])->middleware('autenticador');
    Route::get('/sair', function () {
        Auth::logout();
        return redirect('/'.env('APP_FOLDER', 'contratos'));
    })->name('sair');

    Route::get('/', [App\Http\Controllers\HomeController::class, 'welcome'])->name('welcome');

    Route::group(['middleware' => ['autenticador']], function() {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/grafico', [App\Http\Controllers\ChartController::class, 'index'])->name('chart');

        //Cadastros auxiliares
        Route::get('/cadaux', [App\Http\Controllers\HomeController::class, 'cadaux'])->name('cadaux');
        Route::get('/medidas', [App\Http\Controllers\MedidaController::class, 'index'])->name('cadaux-medidas');
        Route::post('/medidas', [App\Http\Controllers\MedidaController::class, 'store'])->name('cadaux-medidas-store');
        Route::post('/medidas/{id}', [App\Http\Controllers\MedidaController::class, 'update'])->name('cadaux-medidas-update');
        Route::get('/tipo_items', [App\Http\Controllers\TipoItemController::class, 'index'])->name('cadaux-tipo_items');
        Route::post('/tipo_items', [App\Http\Controllers\TipoItemController::class, 'store'])->name('cadaux-tipo_items-store');
        Route::post('/tipo_items/{id}', [App\Http\Controllers\TipoItemController::class, 'update'])->name('cadaux-tipo_items-update');
        Route::get('/departamentos', [App\Http\Controllers\DepartamentoController::class, 'index'])->name('cadaux-departamentos'); //->middleware('permission:cadaux')
        Route::get('/departamentos/criar', [App\Http\Controllers\DepartamentoController::class, 'create'])->name('cadaux-departamentos-create');
        Route::post('/departamentos/criar', [App\Http\Controllers\DepartamentoController::class, 'store'])->name('cadaux-departamentos-store');
        Route::get('/departamentos/{id}/ver', [App\Http\Controllers\DepartamentoController::class, 'show'])->name('cadaux-departamentos-show');
        Route::get('/departamentos/{id}/editar', [App\Http\Controllers\DepartamentoController::class, 'edit'])->name('cadaux-departamentos-edit');
        Route::post('/departamentos/{id}/editar', [App\Http\Controllers\DepartamentoController::class, 'update'])->name('cadaux-departamentos-update');
        Route::get('/items', [App\Http\Controllers\ItemController::class, 'index'])->name('cadaux-items'); //->middleware('permission:cadaux')
        Route::get('/items/criar', [App\Http\Controllers\ItemController::class, 'create'])->name('cadaux-items-create');
        Route::post('/items/criar', [App\Http\Controllers\ItemController::class, 'store'])->name('cadaux-items-store');
        Route::get('/items/{id}/ver', [App\Http\Controllers\ItemController::class, 'show'])->name('cadaux-items-show');
        Route::get('/items/{id}/editar', [App\Http\Controllers\ItemController::class, 'edit'])->name('cadaux-items-edit');
        Route::post('/items/{id}/editar', [App\Http\Controllers\ItemController::class, 'update'])->name('cadaux-items-update');
        Route::get('/locais', [App\Http\Controllers\LocalController::class, 'index'])->name('cadaux-locais'); //->middleware('permission:cadaux')
        Route::get('/locais/criar', [App\Http\Controllers\LocalController::class, 'create'])->name('cadaux-locais-create');
        Route::post('/locais/criar', [App\Http\Controllers\LocalController::class, 'store'])->name('cadaux-locais-store');
        Route::get('/locais/{id}/ver', [App\Http\Controllers\LocalController::class, 'show'])->name('cadaux-locais-show');
        Route::get('/locais/{id}/editar', [App\Http\Controllers\LocalController::class, 'edit'])->name('cadaux-locais-edit');
        Route::post('/locais/{id}/editar', [App\Http\Controllers\LocalController::class, 'update'])->name('cadaux-locais-update');

        //Gestão de Usuários e Permissões
        Route::resource('users', App\Http\Controllers\UserController::class)->middleware('permission:user-list');
        Route::resource('roles', App\Http\Controllers\RoleController::class)->middleware('permission:role-list');
        Route::resource('permissions', App\Http\Controllers\PermissionController::class)->middleware('permission:permission-list');
    });
});
