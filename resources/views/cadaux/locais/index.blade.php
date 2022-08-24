@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Locais', 'rota' => 'cadaux'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form method="GET">
        <div class="row align-items-end">
            <div class="col col-3 mb-2">
                <label for="f-nome" class="col-form-label">Nome</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-nome" name="f-nome" placeholder="Parte do Nome do Local" value="{{$filtros['nome']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-departamento" class="col-form-label">Departamento</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-departamento" name="f-departamento" placeholder="Escopo/Depto do Local" value="{{$filtros['departamento']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-tipo" class="col-form-label">Tipo</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-tipo" name="f-tipo" placeholder="Tipo de Local" value="{{$filtros['tipo']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-cep" class="col-form-label">CEP</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-cep" name="f-cep" placeholder="Tipo de Medida" value="{{$filtros['cep']}}">
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
                <a class="btn btn-success" href="{{ route('cadaux-locais-create') }}"><i class="fas fa-file"></i> Novo Local</a>
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-md-1">@sortablelink('id', 'ID')</th>
                    <th class="col-md-3">@sortablelink('departamentos.nome', 'Departamento')</th>
                    <th class="col-md-3">@sortablelink('nome', 'Nome')</th>
                    <th class="col-md-1">@sortablelink('tipo', 'Tipo')</th>
                    <th class="col-md-2">@sortablelink('cep', 'CEP')</th>
                    <th class="col-md-2 text-end">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $local)
                    <tr>
                        <td>{{ $local->id }}</td>
                        <td>{{ $local->departamento->nome }}</td>
                        <td>{!! $local->nome !!}</td>
                        <td>{{ $local->tipo }}</td>
                        <td>{{ $local->cep }}</td>
                        <td class="text-end">
                            <a class="btn btn-success" href="{{ route('cadaux-locais-show',$local->id) }}"><i class="far fa-eye"></i></a>
                            <a class="btn btn-success" href="{{ route('cadaux-locais-edit',$local->id) }}"><i class="fas fa-edit"></i></a>
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
