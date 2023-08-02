@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Editar Responsável', 'rota' => 'cadaux-responsaveis_emails'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container mb-3">
        <p class="form-legenda"><em>Campos com asterisco (*) são obrigatórios</em></p>
    </div>
    <div class="container">
        {!! Form::model($responsaveis_email, array('route' => ['cadaux-responsaveis_emails-update', $responsaveis_email->id],'method'=>'POST', 'autocomplete'=>"off")) !!}
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Departamento: </strong></label>
                <select name="departamento_id" class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($userDeptos as $idDpt => $userDepto)
                        <option value="{{ $idDpt }}" @if ($idDpt == $responsaveis_email->departamento_id) selected  @endif>{{ $userDepto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Nome: </strong></label>
                {!! Form::text('nome', null, array('placeholder' => 'Nome completo do(a) Responsável','class' => 'form-control','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>E-mail: </strong></label>
                {!! Form::text('email', null, array('placeholder' => 'email do responsável','class' => 'form-control','maxlength'=>255)) !!}
            </div>
            <button type="submit" class="btn btn-secondary">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>

@include('utilitarios.scripts')
@endsection
