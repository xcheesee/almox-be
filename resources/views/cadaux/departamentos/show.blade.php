@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados do Departamento', 'rota' => 'cadaux-departamentos'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col col-9 mb-3">
            <h4>Departamento ID {{ $departamento->id }}</h4>
        </div>
        <div class="col col-3 text-end mb-3">
            <a class="btn btn-success" href="{{ route('cadaux-departamentos-edit',$departamento->id) }}"><i class="fas fa-edit"></i> Editar</a>
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Nome:</strong>
            {{ $departamento->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Andar:</strong>
            {{ $departamento->ativo }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Ativo:</strong>
            @if($departamento->ativo) Sim @else Não @endif
        </div>
    </div>
</div>
@endsection
