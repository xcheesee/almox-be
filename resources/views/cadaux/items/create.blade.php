@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Novo Item', 'rota' => 'cadaux-items'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container mb-3">
        <p class="form-legenda"><em>Campos com asterisco (*) são obrigatórios</em></p>
    </div>
    <div class="container">
        {!! Form::open(array('route' => 'cadaux-items-store','method'=>'POST')) !!}
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Departamento: </strong></label>
                <select name="departamento_id" class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($userDeptos as $idDpt => $userDepto)
                        <option value="{{ $idDpt }}">{{ $userDepto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Tipo de Item: </strong></label>
                <select name="tipo_item_id" class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($tipo_items as $tipo_item)
                        <option value="{{ $tipo_item->id }}">{{ $tipo_item->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Medida: </strong></label>
                <select name="medida_id" class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($medidas as $medida)
                        <option value="{{ $medida->id }}">{{ $medida->tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Nome: </strong></label>
                {!! Form::text('nome', null, array('placeholder' => 'Nome completo do Item','class' => 'form-control','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Descrição: </strong></label>
                {!! Form::textarea('descricao', null, array('placeholder' => 'Texto de descrição do Item','class' => 'form-control','rows'=>4,'maxlength'=>255)) !!}
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>

@include('utilitarios.scripts')
@endsection
