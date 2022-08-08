@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados da Permissão', 'rota' => 'permissions.index'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col">
            <h4>Permissão ID {{ $permission->id }}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Nome:</strong>
            {{ $permission->name }}
        </div>
    </div>
</div>
@endsection
