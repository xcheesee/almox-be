@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Items', 'rota' => 'cadaux'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form method="GET">
        <div class="row align-items-end">
            <div class="col col-3 mb-2">
                <label for="f-nome" class="col-form-label">Nome</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-nome" name="f-nome" placeholder="Parte do Nome do Item" value="{{$filtros['nome']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-departamento" class="col-form-label">Departamento</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-departamento" name="f-departamento" placeholder="Escopo/Depto do Item" value="{{$filtros['departamento']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-tipo" class="col-form-label">Tipo</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-tipo" name="f-tipo" placeholder="Tipo de Item" value="{{$filtros['tipo']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-medida" class="col-form-label">Medida</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-medida" name="f-medida" placeholder="Tipo de Medida" value="{{$filtros['medida']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <button type="submit" class="btn btn-success btnForm"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
            <div class="col col-6"></div>
            <div class="col col-3 text-end">
                <a class="btn btn-success" href="{{ route('cadaux-items-create') }}"><i class="fas fa-file"></i> Novo Item</a>
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-md-1">@sortablelink('id', 'ID')</th>
                    <th class="col-md-3">@sortablelink('departamento.nome', 'Departamento')</th>
                    <th class="col-md-3">@sortablelink('nome', 'Nome')</th>
                    <th class="col-md-2">@sortablelink('tipo_item.nome', 'Tipo')</th>
                    <th class="col-md-1">@sortablelink('medida.tipo', 'Medida')</th>
                    <th class="col-md-2 text-end">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->departamento->nome }}</td>
                        <td>{!! $item->nome !!}</td>
                        <td>{{ $item->tipo_item->nome }}</td>
                        <td>{{ $item->medida->tipo }}</td>
                        <td class="text-end">
                            <a class="btn btn-success" href="{{ route('cadaux-items-show',$item->id) }}"><i class="far fa-eye"></i></a>
                            <a class="btn btn-success" href="{{ route('cadaux-items-edit',$item->id) }}"><i class="fas fa-edit"></i></a>
                            @can('permission-edit')
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
