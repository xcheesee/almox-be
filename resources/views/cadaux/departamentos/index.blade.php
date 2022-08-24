@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Departamentos', 'rota' => 'cadaux'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form method="GET">
        <div class="row align-items-end">
            <div class="col col-4 mb-2">
                <label for="f-nome" class="col-form-label">Nome</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-nome" name="f-nome" placeholder="Parte do nome do Departamento" value="{{$filtros['nome']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-andar" class="col-form-label">Andar</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-andar" name="f-andar" placeholder="Número Andar" value="{{$filtros['andar']}}">
                    <button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-ativo" class="col-form-label">Ativo?</label>
                <div class="d-flex">
                    <select name="f-ativo" id="f-ativo" class="form-control" placeholder="-- Selecione --">
                        <option value="">-- Selecione --</option>
                        <option value="s" @if ($filtros['ativo'] == 's') selected @endif>Sim</option>
                        <option value="n" @if ($filtros['ativo'] == 'n') selected @endif>Não</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <button type="submit" class="btn btn-success btnForm"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
            <div class="col col-6"></div>
            <div class="col col-3 text-end">
                <a class="btn btn-success" href="{{ route('cadaux-departamentos-create') }}"><i class="fas fa-file"></i> Novo Departamento</a>
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-md-1">@sortablelink('id', 'ID')</th>
                    <th class="col-md-3">@sortablelink('nome', 'Nome')</th>
                    <th class="col-md-2">@sortablelink('andar', 'Andar')</th>
                    <th class="col-md-1">@sortablelink('ativo', 'Ativo?')</th>
                    <th class="col-md-2 text-end">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $departamento)
                    <tr>
                        <td>{{ $departamento->id }}</td>
                        <td>{!! $departamento->nome !!}</td>
                        <td>{{ $departamento->andar }}</td>
                        <td>@if ($departamento->ativo)
                            Sim
                            @else
                            Não
                            @endif
                        </td>
                        <td class="text-end">
                            <a class="btn btn-success" href="{{ route('cadaux-departamentos-show',$departamento->id) }}"><i class="far fa-eye"></i></a>
                            <a class="btn btn-success" href="{{ route('cadaux-departamentos-edit',$departamento->id) }}"><i class="fas fa-edit"></i></a>
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
