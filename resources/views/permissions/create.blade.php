@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Criar Permissão', 'rota' => 'permissions.index'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container">
        {!! Form::open(array('route' => 'permissions.store','method'=>'POST')) !!}
            <div class="form-group mb-3">
                <strong>Nome:</strong>
                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
            </div>
            <button type="submit" class="btn btn-secondary">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>
@endsection
