@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Saída de Material', 'rota' => 'home'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form class="form-inline" method="GET">
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <label for="f-local" class="col-form-label">Local</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-local" name="f-local" placeholder="Base do inventário" value="{{$filtros['local']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-processo_sei" class="col-form-label">Processo SEI</label>
                <div class="d-flex">
                    <input type="text" class="form-control processo_sei" id="f-processo_sei" name="f-processo_sei" placeholder="Número do Processo SEI" value="{{$filtros['processo_sei']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-numero_contrato" class="col-form-label">Contrato</label>
                <div class="d-flex">
                    <input type="text" class="form-control contrato" id="f-numero_contrato" name="f-numero_contrato" placeholder="Número do Contrato" value="{{$filtros['numero_contrato']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-numero_nota_fiscal" class="col-form-label">Nota Fiscal</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-numero_nota_fiscal" name="f-numero_nota_fiscal" placeholder="Número da Nota Fiscal" value="{{$filtros['numero_nota_fiscal']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <label for="f-tipo" class="col-form-label">Tipo</label>
                <div class="d-flex">
                    <select name="f-tipo" id="f-tipo" class="form-control" placeholder="-- Selecione --">
                        <option value="">-- Selecione --</option>
                        @foreach ($tipo_items as $tipo_item)
                            <option value="{{ $tipo_item->id }}" @if ($filtros['tipo'] == $tipo_item->id) selected @endif>{{ $tipo_item->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-entrada_depois_de" class="col-form-label">Data de Entrada (início)</label>
                <div class="d-flex">
                    <input type="text" class="form-control date" id="f-entrada_depois_de" name="f-entrada_depois_de" placeholder="dd/m/yyyy" value="{{$filtros['entrada_depois_de']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-entrada_antes_de" class="col-form-label">Data de Entrada (fim)</label>
                <div class="d-flex">
                    <input type="text" class="form-control date" id="f-entrada_antes_de" name="f-entrada_antes_de" placeholder="dd/m/yyyy" value="{{$filtros['entrada_antes_de']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <button type="submit" class="btn btn-primary btnForm"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
            <div class="col col-9 text-end">
                <a class="btn btn-secondary" href="{{ route('entradas-create') }}"><i class="fas fa-file"></i> Nova Entrada</a>
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table table-hover table-sm">
            <thead class="thead-dark">
                <tr>
                    <th class="col-md-1">@sortablelink('id', 'ID')</th>
                    <th class="col-md-1">@sortablelink('data_entrada', 'Data')</th>
                    <th class="col-md-2">@sortablelink('processo_sei', 'Processo SEI')</th>
                    <th class="col-md-2">@sortablelink('numero_contrato', 'Contrato')</th>
                    <th class="col-md-2">@sortablelink('locais.nome', 'Local')</th>
                    <th class="col-md-2">@sortablelink('numero_nota_fiscal', 'Nota Fiscal')</th>
                    <th class="col-md-2">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entradas as $key => $entrada)
                    <tr>
                        <td>{{ $entrada->id }}</td>
                        <td>{{ $entrada->data_entrada_formatada }}</td>
                        <td>{{ $entrada->processo_sei_formatado }}</td>
                        <td>{{ $entrada->numero_contrato_formatado }}</td>
                        <td>{{ $entrada->local->nome }}</td>
                        <td>{{ $entrada->numero_nota_fiscal }}</td>
                        <td>
                            <a class="btn btn-primary tooltip-test" href="#" onclick="exibirEntrada({{ $entrada->id }})" title="Exibir dados da Entrada"><i class="far fa-eye"></i></a>
                            <a class="btn btn-primary" href="{{ route('entradas-edit',$entrada->id) }}"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $entradas->appends($_GET)->links() }}
    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="modal_show">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Dados da Entrada #<span id="titulo_id">X</span></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <div class="row">
                <div class="col col-12">
                    <strong>Data de Entrada:</strong>
                    <p><span id="modal-data_entrada">-</span></p>
                </div>
            </div>
            <div class="row">
                <div class="col col-6">
                    <strong>Departamento:</strong>
                    <p><span id="modal-departamento">-</span></p>
                </div>
                <div class="col col-6">
                    <strong>Local:</strong>
                    <p><span id="modal-local">-</span></p>
                </div>
            </div>
            <div class="row">
                <div class="col col-6">
                    <strong>Processo SEI:</strong>
                    <p><span id="modal-processo_sei">-</span></p>
                </div>
                <div class="col col-6">
                    <strong>Número do Contrato:</strong>
                    <p><span id="modal-numero_contrato">-</span></p>
                </div>
            </div>
            <div class="row">
                <div class="col col-6">
                    <strong>Número da Nota Fiscal:</strong>
                    <p><span id="modal-numero_nota_fiscal">-</span></p>
                </div>
                <div class="col col-6">
                    <strong>Arquivo da Nota Fiscal:</strong>
                    <p><span id="modal-arquivo_nota_fiscal">-</span></p>
                </div>
            </div>
            <div class="row">
                <div class="col col-12">
                    <strong>Materiais:</strong>
                    <p><ul><span id="modal-materiais">-</span></ul></p>
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
    function exibirEntrada(Id){
        $('#spinner-div').show();
        const url = `/{{ env('APP_FOLDER', 'almoxarifado') }}/entradas/${Id}/ver`;
        fetch(url, {
            method: 'GET'
        }).then(function(response) {
            $('#spinner-div').hide();
            if(response.ok){
                response.json().then(data => {
                    var lista = "";
                    if(data.entrada_items){
                        data.entrada_items.forEach(function(item){
                            lista += "<li>"+item.item+" ("+item.quantidade+" "+item.medida+")</li>";
                        });
                    }

                    var arqlink = "";
                    if(data.entrada.arquivo_nota_fiscal){
                        arqlink = '<a class="btn btn-primary btn-sm" href="{{ env('ASSET_URL')}}/storage/'+data.entrada.arquivo_nota_fiscal+'" target="_blank">Visualizar</a>'
                    }

                    $('#titulo_id').html(data.entrada.id);
                    $('#modal-data_entrada').html(data.entrada.data_entrada_formatada);
                    $('#modal-departamento').html(data.entrada.departamento);
                    $('#modal-local').html(data.entrada.local);
                    $('#modal-processo_sei').html(data.entrada.processo_sei_formatado);
                    $('#modal-numero_contrato').html(data.entrada.numero_contrato_formatado);
                    $('#modal-numero_nota_fiscal').html(data.entrada.numero_nota_fiscal);
                    $('#modal-arquivo_nota_fiscal').html(arqlink);
                    $('#modal-materiais').html(lista);
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
