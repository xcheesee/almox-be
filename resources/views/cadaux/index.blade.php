@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Cadastros Auxiliares', 'rota' => 'home'])
@endsection

@section('conteudo')
<div class="row d-flex justify-content-center mt-3 containerTabela">
    <div class="row d-flex justify-content-center mt-3 containerTabela">
        <ul class="list-group list-group-horizontal">
            <li class="list-group-item col-md-12 border-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('cadaux-items') }}" class="list-group-item list-group-item-action">Itens</a>
                    <a href="{{ route('cadaux-tipo_items') }}" class="list-group-item list-group-item-action">Tipos de Item</a>
                    <a href="{{ route('cadaux-departamentos') }}" class="list-group-item list-group-item-action">Departamentos</a>
                    <a href="{{ route('cadaux-medidas') }}" class="list-group-item list-group-item-action">Medidas</a>
                    <a href="{{ route('cadaux-locais') }}" class="list-group-item list-group-item-action">Locais</a>
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection
