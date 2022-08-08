@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Dados do Perfil de Usuário', 'rota' => 'roles.index'])
@endsection

@section('conteudo')
<div class="container containerTabela justify-content-center">
    <div class="row">
        <div class="col">
            <h4>Perfil ID {{ $role->id }}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Nome:</strong>
            {{ $role->name }}
        </div>
    </div>
    <div class="row">
        <div class="col">
            <strong>Permissões:</strong><br>
            @if(!empty($rolePermissions))
                @foreach($rolePermissions as $permission)
                    <label class="badge bg-secondary">{{ $permission->name }}</label>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
