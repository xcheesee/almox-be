@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Inventário', 'rota' => 'home'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form class="form-inline" method="GET">
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <label for="f-base" class="col-form-label">Local</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-base" name="f-base" placeholder="Base do inventário" value="{{$filtros['base']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-item" class="col-form-label">Item</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-item" name="f-item" placeholder="Nome do Item" value="{{$filtros['item']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-tipo_item" class="col-form-label">Tipo</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-tipo_item" name="f-tipo_item" placeholder="Tipo do Item" value="{{$filtros['tipo_item']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-medida" class="col-form-label">Medida</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-medida" name="f-medida" placeholder="Tipo de Medida" value="{{$filtros['medida']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-6 mb-2">
                <label for="f-quantidade_maior_que" class="col-form-label">Qtd Maior que</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-quantidade_maior_que" name="f-quantidade_maior_que" placeholder="Itens com quantidade maior que..." value="{{$filtros['quantidade_maior_que']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-6 mb-2">
                <label for="f-quantidade_menor_que" class="col-form-label">Qtd Menor que</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-quantidade_menor_que" name="f-quantidade_menor_que" placeholder="Itens com quantidade menor que..." value="{{$filtros['quantidade_menor_que']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <button type="submit" class="btn btn-primary btnForm"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
            <div class="col col-9 text-end">
                {{-- <button type="submit" class="btn btn-primary btnForm"><i class="fas fa-filter"></i> Filtrar</button> --}}
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table table-hover table-sm">
            <thead class="thead-dark">
                <tr>
                    <th class="col-md-1">@sortablelink('id', 'ID')</th>
                    <th class="col-md-2">@sortablelink('local.nome', 'Local')</th>
                    <th class="col-md-5">@sortablelink('item.nome', 'Item')</th>
                    <th class="col-md-1">@sortablelink('tipo', 'Tipo')</th>
                    <th class="col-md-1">@sortablelink('medida', 'Medida')</th>
                    <th class="col-md-1">@sortablelink('quantidade', 'Qtd')</th>
                    <th class="col-md-1">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventarios as $key => $inventario)
                    <tr>
                        <td>{{ $inventario->id }}</td>
                        <td>{{ $inventario->local->nome }}</td>
                        <td>{{ $inventario->item->nome }}</td>
                        <td>{{ $inventario->item->tipo_item->nome }}</td>
                        <td>{{ $inventario->item->medida->tipo }}</td>
                        <td>{{ $inventario->quantidade }}</td>
                        <td>
                            <input type="hidden" id="qtd_alerta_{{ $inventario->id }}" value="{{ $inventario->qtd_alerta }}">
                            <a class="btn btn-primary" href="#" onclick="editarQtdAlerta({{ $inventario->id }})" title="Definir Alerta de Qtd" data-bs-toggle="modal" data-bs-target="#modal_qtd"><i class="fas fa-bell"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $inventarios->appends($_GET)->links() }}
    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="modal_qtd">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Definir alerta para o item #<span id="titulo_id">X</span></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-4">
                  <label for="qtd_alerta">Quantidade:</label>
                  <input type="number" class="form-control numerico" name="qtd_alerta" id="qtd_alerta" placeholder="0.00">
                </div>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
            <input type="hidden" name="inventario_id" id="inventario_id">
            <button type="button" class="btn btn-danger" onclick="salvarEdicao()" data-bs-dismiss="modal">Salvar</button>
            @csrf
        </div>

      </div>
    </div>
</div>

@include('utilitarios.scripts')
<script>
    function editarQtdAlerta(Id) {
        const titulo_id = document.getElementById(`titulo_id`);
        const qtd = document.getElementById(`qtd_alerta_${Id}`);
        titulo_id.innerHTML = Id;
        $('#inventario_id').val(Id);
        $('#qtd_alerta').val(qtd.value);
    }

    function salvarEdicao(){
        let formData = new FormData();
        let Id = $('#inventario_id').val();
        const qtd_alerta = $('#qtd_alerta').val();
        const token = document.querySelector('input[name="_token"]').value;
        formData.append('qtd_alerta', qtd_alerta);
        formData.append('_token', token);

        const url = `/{{ env('APP_FOLDER', 'almoxarifado') }}/inventario_alerta/${Id}`;
        fetch(url, {
            body: formData,
            method: 'POST'
        }).then(function(response) {
            if(response.ok){
                response.json().then(data => {
                    $('#mensagem').html(data.mensagem);
                    $('#liveToast').toast('show');
                    $('#qtd_alerta_'+Id).val(qtd_alerta);
                });
            }else{
                response.json().then(data => {
                    alert(data.mensagem);
                });
            }
        });
    }
</script>
@endsection
