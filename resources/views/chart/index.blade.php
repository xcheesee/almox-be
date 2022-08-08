@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Teste de Gráfico', 'rota' => 'home'])
@endsection

@section('conteudo')

@include('layouts.mensagem', ['mensagem' => $mensagem])
<div class="row d-flex justify-content-center mt-3 containerTabela">
    <div class="row d-flex justify-content-center m-3" style="height: 600px;">
        <div class="col d-grid gap-2">
            {!! $chart1->container() !!}
        </div>
        <div class="col d-grid gap-2">
            {!! $chart2->container() !!}
        </div>
    </div>
</div>

<script src="{{ asset('js/apexcharts.js') }}"></script>

{{ $chart1->script() }}
{{ $chart2->script() }}
@endsection
