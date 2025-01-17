@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Criar Usuário', 'rota' => 'users.index'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container">
        {!! Form::open(array('route' => 'users.store','method'=>'POST', 'autocomplete'=>"off")) !!}
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
                {!! Form::select('departamentos[]', $departamentos, [], array('class' => 'form-control','multiple')) !!}
            </div>
            <div class="form-group mb-3">
                <strong>Local:</strong>
                <select name="local_usuario" class="form-select" placeholder="valor" aria-label="Default select example">
                    <option value="" selected disabled></option>
                    @foreach ($locais as $local)
                        <option value="{{$local->id}}">{{ $local->nome }}</option>
                    @endforeach
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
                {!! Form::select('roles[]', $roles,[], array('class' => 'form-control')) !!}
            </div>
            <button type="submit" class="btn btn-secondary">Salvar</button>
        {!! Form::close() !!}
    </div>
</div>
@endsection
