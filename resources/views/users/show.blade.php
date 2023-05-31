@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados do Usuário', 'rota' => 'users.index'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col">
            <h4>Usuário ID {{ $user->id }}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Nome:</strong>
            {{ $user->name }}
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Email:</strong>
            {{ $user->email }}
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Departamentos:</strong>
            @if(!empty($userDeptos))
                @foreach($userDeptos as $depto)
                    <label class="badge bg-primary">{{ $depto }}</label>
                @endforeach
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Local do Usuário:</strong>
            @if (!isset($localUsers->nome))
                <label>Nenhum Local cadastrado.</label>
            @else
                <label>{{ $localUsers->nome }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Senha:</strong>
            ********
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Perfil:</strong>
            @if(!empty($userRole))
                @foreach($userRole as $role)
                    <label class="badge bg-primary">{{ $role }}</label>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
