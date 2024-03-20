@extends('layouts.base')

@section('cabecalho')
    Menu Inicial
@endsection

@section('conteudo')

@include('layouts.mensagem', ['mensagem' => $mensagem])
<div class="row d-flex justify-content-center mt-3 containerTabela">
	@can('inventario')
    <div class="row d-flex justify-content-center m-3" style="height: 160px;">
        @can('entrada')
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('entradas') }}'" class="btn btn-primary"><i class="fas fa-sign-in-alt fa-5x"></i><br>Entrada de Materiais</button>
        </div>
        @endcan
        @can('inventario')
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('inventario') }}'" class="btn btn-primary"><i class="fas fa-warehouse fa-5x"></i><br>Inventário</button>
        </div>
        @endcan
        @can('ordem_servico')
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('ordem_servicos') }}'" class="btn btn-primary"><i class="far fa-list-alt fa-5x"></i><br>Ordem de Serviço</button>
        </div>
        @endcan
    </div>
    @endcan

    @if(auth()->user()->can('saida') || auth()->user()->can('transferencia') || auth()->user()->can('ocorrencia'))
    <div class="row d-flex justify-content-center m-2" style="height: 160px;">
        @can('saida')
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('cadaux') }}'" class="btn btn-primary"><i class="fas fa-sign-out-alt fa-5x"></i><br>Saída de Materiais</button>
        </div>
        @endcan
        @can('transferencia')
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('cadaux') }}'" class="btn btn-primary"><i class="fas fa-exchange-alt fa-5x"></i><br>Transferências</button>
        </div>
        @endcan
        @can('ocorrencia')
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('cadaux') }}'" class="btn btn-primary"><i class="far fa-comment-alt fa-5x"></i><br>Ocorrências</button>
        </div>
        @endcan
    </div>
    @endif

    @hasrole(['admin','gestao_dgpu'])
    <div class="row d-flex justify-content-center m-2" style="height: 160px;">
        @can('cadaux-list')
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('cadaux') }}'" class="btn btn-primary"><i class="fas fa-database fa-5x"></i><br>Cadastros auxiliares</button>
        </div>
        @endcan
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('historico') }}'" class="btn btn-primary"><i class="fas fa-book-open fa-5x"></i><br>Histórico</button>
        </div>
        @can('relatorio-none')
        {{-- proposital para não exibir ainda o dashboard --}}
        <div class="col d-grid gap-2">
            <button onclick="location.href='{{ route('chart') }}'" class="btn btn-primary"><i class="fa-solid fa-chart-pie fa-5x"></i><br>Dashboard</button>
        </div>
        @endcan
    </div>
    @endhasrole
    @hasrole('admin')
    <div class="row d-flex justify-content-center m-2" style="height: 120px;">
        <div class="col d-grid gap-2">
            @can('user-list')
                <button onclick="location.href='{{ route('users.index') }}'" class="btn btn-secondary"><i class="fas fa-users-cog fa-3x"></i><br>Usuários</button>
            @endcan
        </div>
        <div class="col d-grid gap-2">
            @can('role-list')
                <button onclick="location.href='{{ route('roles.index') }}'" class="btn btn-secondary"><i class="fas fa-id-card fa-3x"></i><br>Perfis de Usuários</button>
            @endcan
          </div>
        <div class="col d-grid gap-2">
            @can('permission-list')
                <button onclick="location.href='{{ route('permissions.index') }}'" class="btn btn-secondary"><i class="fas fa-key fa-3x"></i><br>Permissões</button>
            @endcan
        </div>
    </div>
    @endhasrole
</div>
@endsection
