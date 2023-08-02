@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados do Local', 'rota' => 'cadaux-locais'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col col-9 mb-3">
            <h4>Local ID {{ $local->id }}</h4>
        </div>
        <div class="col col-3 text-end mb-3">
            <a class="btn btn-secondary" href="{{ route('cadaux-locais-edit',$local->id) }}"><i class="fas fa-edit"></i> Editar</a>
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Departamento:</strong>
            {{ $local->departamento->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Nome:</strong>
            {{ $local->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Tipo:</strong>
            {{ $local->tipo }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>CEP:</strong>
            {{ $local->cep }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Logradouro:</strong>
            {{ $local->logradouro }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Número:</strong>
            {{ $local->numero }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Bairro:</strong>
            {{ $local->bairro }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Cidade:</strong>
            {{ $local->cidade }}
        </div>
    </div>
</div>
@endsection
