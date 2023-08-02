@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Nova Entrada', 'rota' => 'entradas'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container mb-3">
        <p class="form-legenda"><em>Campos com asterisco (*) são obrigatórios</em></p>
    </div>
    <div class="container">
        {!! Form::open(array('route' => 'entradas-store','method'=>'POST', 'files' => true)) !!}
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Departamento: </strong></label>
                {!! Form::select('departamento_id', $departamentos, null, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Data de Entrada: </strong></label><div class='input-group' id='datetimepicker1' data-td-target-input='nearest' data-td-target-toggle='nearest'>
                    {!! Form::text('data_entrada', null, array("placeholder"=>"dd/mm/aaaa",'class' => 'form-control date')) !!}
                </div>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Local de Destino: </strong></label>
                {!! Form::select('local_id', $locais, null, array('class' => 'form-control', "placeholder"=>"-- Selecione --")) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Processo SEI: </strong></label>
                {!! Form::text('processo_sei', null, array('placeholder' => 'Número Processo SEI','class' => 'form-control processo_sei','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Contrato: </strong></label>
                {!! Form::text('numero_contrato', null, array('placeholder' => 'Número Contrato','class' => 'form-control contrato','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Nota Fiscal: </strong></label>
                {!! Form::text('numero_nota_fiscal', null, array('placeholder' => 'Número da Nota Fiscal','class' => 'form-control','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group mb-3">
                <label class="control-label"><strong>Arquivo Nota Fiscal: </strong></label>
                {{  Form::file('arquivo_nota_fiscal', array('class' => 'form-control mt-2', 'accept' => '.png,.jpg,.jpeg,.pdf')) }}
            </div>

        <div class="row g-3 align-items-center m-3">
            <h2 class="mb-2">Materiais</h2>
            <div class="form-group col col-3">
                <label class="control-label"><strong>Tipo: </strong></label>
                {!! Form::select('tipo_item', $tipo_items, null, array('class' => 'form-control', 'onchange'=>'carregaItems(this.value)', "placeholder"=>"-- Selecione --")) !!}
            </div>
            <div class="form-group col col-6">
                <label class="control-label"><strong>Item: </strong></label>
                {!! Form::select('items_procura', array(), null, array('class' => 'form-control', "placeholder"=>"-- Selecione --")) !!}
            </div>
            <div class="form-group col col-2">
                <a onclick="adicionarItem('entrada_items')" class="btn btn-primary mt-4"><i class="fas fa-plus"></i> Adicionar</a>
            </div>
        </div>
        <div class="row g-3 align-items-center m-5" id="lista_materiais">

        </div>
            <button type="submit" class="btn btn-secondary">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>

@include('utilitarios.scripts')
<script>
    iniciarDatePicker('datetimepicker1');
    var items_adicionados = [];
</script>
@endsection
