@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Novo Departamento', 'rota' => 'cadaux-departamentos'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container mb-3">
        <p class="form-legenda"><em>Campos com asterisco (*) são obrigatórios</em></p>
    </div>
    <div class="container">
        {!! Form::open(array('route' => 'cadaux-departamentos-store','method'=>'POST')) !!}
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Nome: </strong></label>
                {!! Form::text('nome', null, array('placeholder' => 'Nome completo do Departamento','class' => 'form-control','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Andar: </strong></label>
                {!! Form::text('andar', null, array('placeholder' => 'Número do andar do departamento','class' => 'form-control','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <input type="checkbox" class="form-check-input" name="ativo" value="1">
                <label for="ativo" class="form-check-label" >Ativo?</label>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>

@include('utilitarios.scripts')
@endsection
