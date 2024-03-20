@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Histórico de operações no sistema', 'rota' => 'home'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form class="form-inline" method="GET">
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <label for="f-depois_de" class="col-form-label">Data da Ação (início)</label>
                <div class="d-flex">
                    <input type="text" class="form-control date" id="f-depois_de" name="f-depois_de" placeholder="dd/m/yyyy" value="{{$filtros['depois_de']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-antes_de" class="col-form-label">Data da Ação (fim)</label>
                <div class="d-flex">
                    <input type="text" class="form-control date" id="f-antes_de" name="f-antes_de" placeholder="dd/m/yyyy" value="{{$filtros['antes_de']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <button type="submit" class="btn btn-primary btnForm"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table table-hover table-sm">
            <thead class="thead-dark">
                <tr>
                    <th class="col-md-1">@sortablelink('id', 'ID')</th>
                    <th class="col-md-1">@sortablelink('data_acao', 'Data')</th>
                    <th class="col-md-1">@sortablelink('nome_tabela', 'Tabela')</th>
                    <th class="col-md-2">@sortablelink('tipo_acao', 'Operação')</th>
                    <th class="col-md-2">Usuário</th>
                    {{-- <th class="col-md-2">Ação</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($historicos as $key => $historico)
                    <tr>
                        <td>{{ $historico->id }}</td>
                        <td>{{ $historico->data_acao_formatada }}</td>
                        <td>{{ $historico->nome_tabela }}</td>
                        <td>{{ $historico->tipo_acao }}</td>
                        <td>{{ $historico->user->name }}</td>
                        {{-- <td>
                            <a class="btn btn-primary tooltip-test" href="#" onclick="exibirRegistro({{ $historico->id }})" title="Exibir dados do Registro"><i class="far fa-eye"></i></a>
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $historicos->appends($_GET)->links() }}
    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="modal_show">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Dados da Registro #<span id="titulo_id">X</span></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <div class="row">
                <div class="col col-12">
                    <strong>Data:</strong>
                    <p><span id="modal-data_acao">-</span></p>
                </div>
            </div>
            <div class="row">
                <div class="col col-6">
                    <strong>Tabela:</strong>
                    <p><span id="modal-nome_tabela">-</span></p>
                </div>
                <div class="col col-6">
                    <strong>tipo_acao:</strong>
                    <p><span id="modal-tipo_acao">-</span></p>
                </div>
                <div class="col col-6">
                    <strong>Usuário:</strong>
                    <p><span id="modal-user_id">-</span></p>
                </div>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
            <input type="hidden" name="inventario_id" id="inventario_id">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
        </div>

      </div>
    </div>
</div>

@include('utilitarios.scripts')
<script>
    function exibirRegistro(Id){
        $('#spinner-div').show();
        const url = `/{{ env('APP_FOLDER', 'almoxarifado') }}/historicos/${Id}`;
        fetch(url, {
            method: 'GET'
        }).then(function(response) {
            $('#spinner-div').hide();
            if(response.ok){
                response.json().then(data => {
                    // var lista = "";
                    // if(data.historico.registro){
                    //     data.historico.registro.forEach(function(item){
                    //         lista += "<li>"+item.item+" ("+item.quantidade+" "+item.medida+")</li>";
                    //     });
                    // }

                    $('#titulo_id').html(data.historico.id);
                    $('#modal-data_acao').html(data.historico.data_acao_formatada);
                    $('#modal-nome_tabela').html(data.historico.nome_tabela);
                    $('#modal-tipo_acao').html(data.historico.tipo_acao);
                    $('#modal-user_id').html(data.historico.user_name);
                    // $('#modal-materiais').html(lista);
                    $('#modal_show').modal('show');
                });
            }else{
                response.json().then(data => {
                    alert(data.mensagem);
                });
            }
        });
    }

    function listaItems(item){
        var htmlLista = "";

    }
</script>
@endsection
