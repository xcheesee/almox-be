@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados do Profissional', 'rota' => 'cadaux-profissionais'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col col-9 mb-3">
            <h4>Profssional ID {{ $profissional->id }}</h4>
        </div>
        <div class="col col-3 text-end mb-3">
            <a class="btn btn-secondary" href="{{ route('cadaux-profissionais-edit',$profissional->id) }}"><i class="fas fa-edit"></i> Editar</a>
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Departamento:</strong>
            {{ $profissional->departamento->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Local:</strong>
            {{ $profissional->local->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Nome:</strong>
            {{ $profissional->nome }}
        </div>
    </div>
    <div class="row">
        <div class="col mb-3">
            <strong>Profissão:</strong>
            {!! $profissional->profissao !!}
        </div>
    </div>
</div>
@endsection
