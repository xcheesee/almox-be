@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Gestão de Usuários', 'rota' => 'home'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row containerTabela justify-content-center">
    <div class="row d-flex">
        <div class="col-10">
        </div>
        <div class="col-2 text-end">
            <a class="btn btn-secondary" href="{{ route('users.create') }}">Novo Usuário</a>
        </div>
    </div>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-1">ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th width="280px">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if(!empty($user->getRoleNames()))
                                @foreach($user->getRoleNames() as $val)
                                    <label class="badge bg-dark">{{ $val }}</label>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('users.show',$user->id) }}"><i class="far fa-eye"></i></a>
                            @can('user-edit')
                                <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}"><i class="fas fa-edit"></i></a>
                            @endcan
                            @can('user-delete')
                                {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline','onsubmit'=>'return confirm("Tem certeza que deseja remover \''.$user->name.'\'?")']) !!}
                                {!! Form::button('<i class="far fa-trash-alt"></i>', ['class' => 'btn btn-danger', 'type' => 'submit']) !!}
                                {!! Form::close() !!}
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $data->render() }}
    </div>
</div>
@endsection
