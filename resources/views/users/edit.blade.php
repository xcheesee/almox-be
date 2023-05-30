@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Editar Usuário', 'rota' => 'users.index'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container">
        {!! Form::model($user, ['route' => ['users.update', $user->id], 'method'=>'PATCH', 'autocomplete'=>"off"]) !!}
            <div class="form-group mb-3">
                <strong>Nome:</strong>
                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
            </div>
            <div class="form-group mb-3">
                <strong>Email:</strong>
                {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
            </div>
            <div class="form-group mb-3">
                <strong>Departamentos:</strong>
                {!! Form::select('departamentos[]', $departamentos, $userDeptos, array('class' => 'form-control','multiple')) !!}
            </div>
            <div class="form-group mb-3">
                <strong>Local:</strong>
                <select name="local_usuario" class="form-select" aria-label="Default select example" >
                    @if (!isset($localUsers->local_id))
                        <option value="" selected>Nenhum(a)</option>
                            @foreach ($locais as $local)
                                <option value="{{$local->id}}">{{ $local->nome }}</option>
                            @endforeach
                    @else
                        @foreach ($locais as $local)
                            <option value="{{$local->id}}"{{ $local->id == $localUsers->local_id ? 'selected' : '' }}>{{ $local->nome }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <div class="form-group mb-3">
                <strong>Senha:</strong>
                {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
            </div>
            <div class="form-group mb-3">
                <strong>Confirmação Senha:</strong>
                {!! Form::password('password_confirmation', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
            </div>
            <div class="form-group mb-3">
                <strong>Perfis de Usuário:</strong>
                {!! Form::select('roles[]', $roles, $userRole, array('class' => 'form-control')) !!}
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>
@endsection
