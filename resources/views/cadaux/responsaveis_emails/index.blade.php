@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Responsáveis de Estoque (Compras)', 'rota' => 'cadaux'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])
@include('layouts.erros', ['errors' => $errors])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <div class="row d-flex mb-3">
        <div class="col-10">
        </div>
        <div class="col-2 text-end">
            <a class="btn btn-secondary" href="{{ route('cadaux-responsaveis_emails-create') }}"><i class="fas fa-file"></i> Novo(a) Responsável</a>
        </div>
    </div>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-2">ID</th>
                    <th class="col-md-3">Departamento</th>
                    <th class="col-md-3">Nome</th>
                    <th class="col-md-3">Email</th>
                    <th class="col-md-2 text-end">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($responsaveis_emails as $key => $responsaveis_email)
                    <tr>
                        <td>{{ $responsaveis_email->id }}</td>
                        <td>{{ $responsaveis_email->departamento->nome }}</td>
                        <td>{{ $responsaveis_email->nome }}</td>
                        <td>{!! $responsaveis_email->email !!}</td>
                        <td class="text-end">
                            <a class="btn btn-primary" href="{{ route('cadaux-responsaveis_emails-edit',$responsaveis_email->id) }}"><i class="fas fa-edit"></i></a>
                            @can('permission-edit')
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
