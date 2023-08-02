@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados do Item', 'rota' => 'cadaux-items'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col col-9 mb-3">
            <h4>Item ID {{ $item->id }}</h4>
        </div>
        <div class="col col-3 text-end mb-3">
            <a class="btn btn-secondary" href="{{ route('cadaux-items-edit',$item->id) }}"><i class="fas fa-edit"></i> Editar</a>
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Departamento:</strong>
            {{ $item->departamento->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Tipo:</strong>
            {{ $item->tipo_item->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Medida:</strong>
            {{ $item->medida->tipo }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Nome:</strong>
            {{ $item->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Descrição:</strong>
            {!! $item->descricao !!}
        </div>
    </div>
</div>
@endsection
