@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Editar Profissional', 'rota' => 'cadaux-profissionais'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container mb-3">
        <p class="form-legenda"><em>Campos com asterisco (*) são obrigatórios</em></p>
    </div>
    <div class="container">
        {!! Form::model($profissional, array('route' => ['cadaux-profissionais-update', $profissional->id],'method'=>'POST', 'autocomplete'=>"off")) !!}
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Departamento: </strong></label>
                <select name="departamento_id" class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($userDeptos as $idDpt => $userDepto)
                        <option value="{{ $idDpt }}" @if ($idDpt == $profissional->departamento_id) selected  @endif>{{ $userDepto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Local: </strong></label>
                <select name="local_id" class="form-select">
                    <option value="">--Selecione--</option>
                    @foreach ($locais as $local)
                        <option value="{{ $local->id }}" @if ($local->id == $profissional->local_id) selected  @endif>{{ $local->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Nome: </strong></label>
                {!! Form::text('nome', null, array('placeholder' => 'Nome completo do Profissional','class' => 'form-control','required','maxlength'=>255)) !!}
            </div>
            <div class="form-group required mb-3">
                <label class="control-label"><strong>Profissão: </strong></label>
                {!! Form::text('profissao', null, array('placeholder' => 'Profissão/Cargo','class' => 'form-control','maxlength'=>255)) !!}
            </div>
            <button type="submit" class="btn btn-secondary">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>

@include('utilitarios.scripts')
<script type="text/javascript">
    jQuery(document).ready(function ()
    {
        jQuery('select[name="departamento_id"]').on('change',function(){carregaLocais(this,"local_id")});
    });
</script>
@endsection
