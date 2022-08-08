@extends('layouts.base')

@section('cabecalho')
    Entrar
@endsection

@section('conteudo')
@include('layouts.erros', ['errors' => $errors])
<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form method="post">
        @csrf
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" required class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" name="password" id="password" required min="1" class="form-control">
        </div>

        <button type="submit" class="btn btn-success mt-3">
            Entrar
        </button>
    </form>
</div>
<div class="row containerTabela mt-5">
    <p>
    <strong>Importante:</strong> Se este for o seu primeiro acesso, recomendamos que troque a senha. Para isso, após efetuar o login basta clicar no ícone de troca de senha localizado ao lado do nome de usuário.
    <br><br>
    Caso tenha esquecido sua senha ou precise criar um usuário para acesso, favor enviar e-mail para <strong>tisvma@prefeitura.sp.gov.br</strong>.
    <br>
    No e-mail, informe o serviço a ser solicitado (esqueci a senha/criar usuário), o e-mail caso esteja solicitando o serviço para terceiro, e o sistema (<strong>{{ env('APP_NAME', 'NDTIC WEB APP') }}</strong>)
    </p>
</div>
@endsection
