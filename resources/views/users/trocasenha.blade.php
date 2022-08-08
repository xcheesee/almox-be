@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Alterar Senha do Usuário', 'rota' => 'users.index'])
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="container containerTabela justify-content-center">
    <div class="container">
        <form method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <strong>Email:</strong>
                <input type="text" class="form-control" name="email" value="{{ Auth::user()->email }}" readonly>
            </div>
            <div class="form-group mb-3">
                <strong>Senha Atual:</strong>
                <input type="password" name="password" id="password" required min="1" class="form-control">
            </div>
            <div class="form-group mb-3">
                <strong>Nova Senha:</strong>
                <input type="password" name="newpassword" id="newpassword" required min="1" class="form-control">
            </div>
            <div class="form-group mb-3">
                <strong>Confirmação Nova Senha:</strong>
                <input type="password" name="password_confirmation" id="password_confirmation" required min="1" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
        </form>
    </div>
</div>
@endsection
