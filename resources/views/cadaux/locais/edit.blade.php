@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Editar Local', 'rota' => 'cadaux-locais'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container mb-3">
        <p class="form-legenda"><em>Campos com asterisco (*) são obrigatórios</em></p>
    </div>
    <div class="container">
        {!! Form::model($local, ['route' => ['cadaux-locais-update', $local->id], 'method'=>'POST']) !!}
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Departamento: </strong></label>
                <select  name="departamento_id"class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($userDeptos as $idDpt => $userDepto)
                        <option value="{{ $idDpt }}" @if ($idDpt == $local->departamento_id) selected  @endif>{{ $userDepto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Nome: </strong></label>
                {!! Form::text('nome', null, array('placeholder' => 'Nome completo do Local','class' => 'form-control','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Tipo: </strong></label>
                <select  name="tipo"class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($tipos as $k=>$tipo)
                        <option value="{{ $k }}" @if ($k == $local->tipo) selected  @endif>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 align-items-center mb-3">
                <div class="form-group col-12">
                    <label class="control-label"><strong>CEP: </strong></label>
                    {!! Form::text('cep', null, array('placeholder' => '00000-000','onblur'=>"consultaCEP(this.value,'endereco','bairro','cidade')",'class' => 'form-control cep','maxlength'=>8)) !!}
                </div>
            </div>
            <div class="row g-3 align-items-center mb-3">
                <div class="form-group col-6 required">
                    <label class="control-label"><strong>Endereço: </strong></label>
                    {!! Form::text('logradouro', null, array('id'=>'endereco','placeholder' => 'Logradouro sem o número','class' => 'form-control','required','maxlength'=>100)) !!}
                </div>
                <div class="form-group col-2 required">
                    <label class="control-label"><strong>Número: </strong></label>
                    {!! Form::text('numero', null, array('placeholder' => '00000','class' => 'form-control','required','maxlength'=>5)) !!}
                </div>
            </div>
            <div class="row g-3 align-items-center mb-3">
                <div class="form-group col-6 required">
                    <label class="control-label"><strong>Bairro: </strong></label>
                    {!! Form::text('bairro', null, array('id'=>'bairro','placeholder' => 'Bairro','class' => 'form-control','required','maxlength'=>45)) !!}
                </div>
                <div class="form-group col-6">
                    <label class="control-label"><strong>Cidade: </strong></label>
                    {!! Form::text('cidade', null, array('id'=>'cidade','placeholder' => 'Cidade','class' => 'form-control','maxlength'=>45)) !!}
                </div>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>

@include('utilitarios.scripts')
@endsection
