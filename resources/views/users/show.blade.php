@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados do Usuário', 'rota' => 'users.index'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col">
            <h4>Usuário ID {{ $user->id }}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Nome:</strong>
            {{ $user->name }}
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Email:</strong>
            {{ $user->email }}
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Senha:</strong>
            ********
        </div>
    </div>
</div>
@endsection
