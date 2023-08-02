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

Route::prefix(env('APP_FOLDER', 'almoxarifado'))->group(function () { //considerando que o projeto estará em subdiretório em homol/prod

    //Custom Login
    Route::get('/entrar', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('entrar');
    Route::post('/entrar', [App\Http\Controllers\Auth\LoginController::class, 'entrar']);
    Route::get('/trocasenha', [App\Http\Controllers\UserController::class, 'trocasenha'])->name('trocasenha')->middleware('autenticador');
    Route::post('/trocasenha', [App\Http\Controllers\UserController::class, 'alterarsenha'])->middleware('autenticador');
    // Route::get('/trocasenha', [App\Http\Controllers\Auth\RegisterController::class, 'criar'])->middleware('autenticador');
    // Route::post('/registrar', [App\Http\Controllers\Auth\RegisterController::class, 'create'])->middleware('autenticador');
    Route::get('/sair', function () {
        Auth::logout();
        return redirect('/'.env('APP_FOLDER', 'almoxarifado'));
    })->name('sair');

    Route::get('/', [App\Http\Controllers\HomeController::class, 'welcome'])->name('welcome');

    Route::group(['middleware' => ['autenticador']], function() {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/grafico', [App\Http\Controllers\ChartController::class, 'index'])->name('chart');

        //inventário
        Route::get('/inventario', [App\Http\Controllers\InventarioController::class, 'index_web'])->name('inventario');
        Route::post('/inventario_alerta/{id}', [App\Http\Controllers\InventarioController::class, 'salvar_alerta'])->name('inventario_alerta');

        //Entrada
        Route::get('/entradas', [App\Http\Controllers\EntradaController::class, 'index_web'])->name('entradas');
        Route::get('/entradas/criar', [App\Http\Controllers\EntradaController::class, 'create'])->name('entradas-create');
        Route::post('/entradas/criar', [App\Http\Controllers\EntradaController::class, 'store'])->name('entradas-store');
        Route::get('/entradas/{id}/ver', [App\Http\Controllers\EntradaController::class, 'show'])->name('entradas-show');
        Route::get('/entradas/{id}/editar', [App\Http\Controllers\EntradaController::class, 'edit'])->name('entradas-edit');
        Route::post('/entradas/{id}/editar', [App\Http\Controllers\EntradaController::class, 'update'])->name('entradas-update');

        //Ordem de Serviço
        Route::get('/ordem_servicos', [App\Http\Controllers\OrdemServicoController::class, 'index_web'])->name('ordem_servicos');
        Route::get('/ordem_servicos/criar', [App\Http\Controllers\OrdemServicoController::class, 'create'])->name('ordem_servicos-create');
        Route::post('/ordem_servicos/criar', [App\Http\Controllers\OrdemServicoController::class, 'store'])->name('ordem_servicos-store');
        Route::get('/ordem_servicos/{id}/ver', [App\Http\Controllers\OrdemServicoController::class, 'show'])->name('ordem_servicos-show');
        Route::get('/ordem_servicos/{id}/editar', [App\Http\Controllers\OrdemServicoController::class, 'edit'])->name('ordem_servicos-edit');
        Route::post('/ordem_servicos/{id}/editar', [App\Http\Controllers\OrdemServicoController::class, 'update'])->name('ordem_servicos-update');

        //Saída
        Route::get('/saidas', [App\Http\Controllers\SaidaController::class, 'index_web'])->name('saidas');
        Route::get('/saidas/criar', [App\Http\Controllers\OrdemServicoController::class, 'create'])->name('saidas-create');
        Route::post('/saidas/criar', [App\Http\Controllers\OrdemServicoController::class, 'store'])->name('saidas-store');
        Route::get('/saidas/{id}/ver', [App\Http\Controllers\OrdemServicoController::class, 'show'])->name('saidas-show');
        Route::get('/saidas/{id}/editar', [App\Http\Controllers\OrdemServicoController::class, 'edit'])->name('saidas-edit');
        Route::post('/saidas/{id}/editar', [App\Http\Controllers\OrdemServicoController::class, 'update'])->name('saidas-update');

        //Transferência
        Route::get('/transferencias', [App\Http\Controllers\TransferenciaMateriaisController::class, 'index_web'])->name('transferencias');
        Route::get('/transferencias/criar', [App\Http\Controllers\TransferenciaMateriaisController::class, 'create'])->name('transferencias-create');
        Route::post('/transferencias/criar', [App\Http\Controllers\TransferenciaMateriaisController::class, 'store'])->name('transferencias-store');
        Route::get('/transferencias/{id}/ver', [App\Http\Controllers\TransferenciaMateriaisController::class, 'show'])->name('transferencias-show');
        Route::get('/transferencias/{id}/editar', [App\Http\Controllers\TransferenciaMateriaisController::class, 'edit'])->name('transferencias-edit');
        Route::post('/transferencias/{id}/editar', [App\Http\Controllers\TransferenciaMateriaisController::class, 'update'])->name('transferencias-update');

        //Ocorrência
        Route::get('/ocorrencias', [App\Http\Controllers\OcorrenciasController::class, 'index_web'])->name('ocorrencias');
        Route::get('/ocorrencias/criar', [App\Http\Controllers\OcorrenciasController::class, 'create'])->name('ocorrencias-create');
        Route::post('/ocorrencias/criar', [App\Http\Controllers\OcorrenciasController::class, 'store'])->name('ocorrencias-store');
        Route::get('/ocorrencias/{id}/ver', [App\Http\Controllers\OcorrenciasController::class, 'show'])->name('ocorrencias-show');
        Route::get('/ocorrencias/{id}/editar', [App\Http\Controllers\OcorrenciasController::class, 'edit'])->name('ocorrencias-edit');
        Route::post('/ocorrencias/{id}/editar', [App\Http\Controllers\OcorrenciasController::class, 'update'])->name('ocorrencias-update');

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
        Route::get('/locais/{id}/filtrar', [App\Http\Controllers\LocalController::class, 'filtrar_dpt'])->name('cadaux-locais-filtrar_dpt');
        Route::get('/profissionais', [App\Http\Controllers\ProfissionalController::class, 'index'])->name('cadaux-profissionais');
        Route::get('/profissionais/criar', [App\Http\Controllers\ProfissionalController::class, 'create'])->name('cadaux-profissionais-create');
        Route::post('/profissionais/criar', [App\Http\Controllers\ProfissionalController::class, 'store'])->name('cadaux-profissionais-store');
        Route::get('/profissionais/{id}/ver', [App\Http\Controllers\ProfissionalController::class, 'show'])->name('cadaux-profissionais-show');
        Route::get('/profissionais/{id}/editar', [App\Http\Controllers\ProfissionalController::class, 'edit'])->name('cadaux-profissionais-edit');
        Route::post('/profissionais/{id}/editar', [App\Http\Controllers\ProfissionalController::class, 'update'])->name('cadaux-profissionais-update');
        Route::get('/responsaveis_emails', [App\Http\Controllers\ResponsaveisEmailController::class, 'index'])->name('cadaux-responsaveis_emails');
        Route::get('/responsaveis_emails/criar', [App\Http\Controllers\ResponsaveisEmailController::class, 'create'])->name('cadaux-responsaveis_emails-create');
        Route::post('/responsaveis_emails/criar', [App\Http\Controllers\ResponsaveisEmailController::class, 'store'])->name('cadaux-responsaveis_emails-store');
        Route::get('/responsaveis_emails/{id}/editar', [App\Http\Controllers\ResponsaveisEmailController::class, 'edit'])->name('cadaux-responsaveis_emails-edit');
        Route::post('/responsaveis_emails/{id}/editar', [App\Http\Controllers\ResponsaveisEmailController::class, 'update'])->name('cadaux-responsaveis_emails-update');

        //Gestão de Usuários e Permissões
        Route::resource('users', App\Http\Controllers\UserController::class)->middleware('permission:user-list');
        Route::resource('roles', App\Http\Controllers\RoleController::class)->middleware('permission:role-list');
        Route::resource('permissions', App\Http\Controllers\PermissionController::class)->middleware('permission:permission-list');
    });
});
