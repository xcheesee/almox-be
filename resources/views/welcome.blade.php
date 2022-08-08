@extends('layouts.base')

@section('cabecalho')
@endsection

@section('conteudo')
<div class="container">
    <div class="justify-content-center">
        <div class="d-flex flex-shrink-0 justify-content-center align-items-center pt-2 pb-3" style="height: 400px;">
            <a class="navbar-brand" href="{{ route('entrar') }}">
                <img id="sitelogo" src="{{ asset('img/Logo1024_vertical_original.png') }}" width="auto" height="400px">
            </a>
        </div>
        <div class="d-flex flex-shrink-0 justify-content-center align-items-center pt-2 pb-3">
            <span><h1>Módulo Administrador</h1></span>
        </div>
        <div class="d-flex flex-shrink-0 justify-content-center align-items-center pt-2 pb-3">
            <span>Clique no logo acima para entrar</span>
        </div>
    </div>
</div>
<script>enableScripts()</script>
@endsection
