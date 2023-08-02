@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Gestão de Permissões', 'rota' => 'home'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row containerTabela justify-content-center">
    <div class="row d-flex">
        <div class="col-10">
        </div>
        <div class="col-2 text-end">
            <a class="btn btn-secondary" href="{{ route('permissions.create') }}">Nova Permissão</a>
        </div>
    </div>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-1">ID</th>
                    <th>Nome</th>
                    <th width="280px">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $permission)
                    <tr>
                        <td>{{ $permission->id }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('permissions.show',$permission->id) }}"><i class="far fa-eye"></i></a>
                            @can('permission-edit')
                                <a class="btn btn-primary" href="{{ route('permissions.edit',$permission->id) }}"><i class="fas fa-edit"></i></a>
                            @endcan
                            @can('permission-delete')
                                {!! Form::open(['method' => 'DELETE','route' => ['permissions.destroy', $permission->id],'style'=>'display:inline','onsubmit'=>'return confirm("Tem certeza que deseja remover \''.$permission->name.'\'?")']) !!}
                                {!! Form::button('<i class="far fa-trash-alt"></i>', ['class' => 'btn btn-danger', 'type' => 'submit']) !!}
                                {!! Form::close() !!}
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $data->appends($_GET)->links() }}
    </div>
</div>
@endsection
