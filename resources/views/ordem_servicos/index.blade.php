@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Ordem de Serviço', 'rota' => 'home'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <form class="form-inline" method="GET">
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <label for="f-origem" class="col-form-label">Base Origem</label>
                <div class="d-flex">
                    <input type="text" class="form-control" id="f-origem" name="f-origem" placeholder="Base de Origem dos materiais" value="{{$filtros['origem']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-local_servico" class="col-form-label">Local de Serviço</label>
                <div class="d-flex">
                    <input type="text" class="form-control local_servico" id="f-local_servico" name="f-local_servico" placeholder="Local de destino dos materiais" value="{{$filtros['local_servico']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-servico_depois_de" class="col-form-label">Data de Emissão (início)</label>
                <div class="d-flex">
                    <input type="text" class="form-control date" id="f-servico_depois_de" name="f-servico_depois_de" placeholder="dd/m/yyyy" value="{{$filtros['servico_depois_de']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="col col-3 mb-2">
                <label for="f-servico_antes_de" class="col-form-label">Data de Emissão (fim)</label>
                <div class="d-flex">
                    <input type="text" class="form-control date" id="f-servico_antes_de" name="f-servico_antes_de" placeholder="dd/m/yyyy" value="{{$filtros['servico_antes_de']}}">
                    <button type="button" class="btn bg-primary btn_limpafiltro" onclick="$(this).siblings('input[type=\'text\']').val('')"><i class="fa fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="row align-items-end mb-2">
            <div class="col col-3 mb-2">
                <button type="submit" class="btn btn-primary btnForm"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
            <div class="col col-9 text-end">
                {{-- <a class="btn btn-secondary" href="{{ route('ordem_servicos-create') }}"><i class="fas fa-file"></i> Nova O.S.</a> --}}
            </div>
        </div>
    </form>
    <div class="row">
        <table class="table table-hover table-sm">
            <thead class="thead-dark">
                <tr>
                    <th class="col-md-1">@sortablelink('id', 'ID')</th>
                    <th class="col-md-1">@sortablelink('created_at', 'Data')</th>
                    <th class="col-md-2">@sortablelink('origem.nome', 'Contrato')</th>
                    <th class="col-md-2">@sortablelink('locais.nome', 'Local')</th>
                    <th class="col-md-2">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordem_servicos as $key => $ordem_servico)
                    <tr>
                        <td>{{ $ordem_servico->id }}</td>
                        <td>{{ $ordem_servico->created_at_formatado }}</td>
                        <td>{{ $ordem_servico->origem->nome }}</td>
                        <td>{{ $ordem_servico->local_servico->nome }}</td>
                        <td>
                            <a class="btn btn-primary tooltip-test" href="#" onclick="exibirOS({{ $ordem_servico->id }})" title="Exibir dados da O.S."><i class="far fa-eye"></i></a>
                            {{-- <a class="btn btn-primary" href="{{ route('ordem_servicos-edit',$ordem_servico->id) }}"><i class="fas fa-edit"></i></a> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $ordem_servicos->appends($_GET)->links() }}
    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="modal_show">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Dados da O.S. #<span id="titulo_id">X</span></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <div class="row">
                <div class="col col-12">
                    <strong>Data de Emissão:</strong>
                    <p><span id="modal-data_ordem_servico">-</span></p>
                </div>
            </div>
            <div class="row">
                <div class="col col-6">
                    <strong>Departamento:</strong>
                    <p><span id="modal-departamento">-</span></p>
                </div>
                <div class="col col-6">
                    <strong>Origem:</strong>
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
    function exibirOS(Id){
        $('#spinner-div').show();
        const url = `/{{ env('APP_FOLDER', 'almoxarifado') }}/ordem_servicos/${Id}/ver`;
        fetch(url, {
            method: 'GET'
        }).then(function(response) {
            $('#spinner-div').hide();
            if(response.ok){
                response.json().then(data => {
                    var lista = "";
                    if(data.ordem_servico_items){
                        data.ordem_servico_items.forEach(function(item){
                            lista += "<li>"+item.item+" ("+item.quantidade+" "+item.medida+")</li>";
                        });
                    }

                    var arqlink = "";
                    if(data.ordem_servico.arquivo_nota_fiscal){
                        arqlink = '<a class="btn btn-primary btn-sm" href="{{ env('ASSET_URL')}}/storage/'+data.ordem_servico.arquivo_nota_fiscal+'" target="_blank">Visualizar</a>'
                    }

                    $('#titulo_id').html(data.ordem_servico.id);
                    $('#modal-data_ordem_servico').html(data.ordem_servico.data_ordem_servico_formatada);
                    $('#modal-departamento').html(data.ordem_servico.departamento);
                    $('#modal-local').html(data.ordem_servico.local);
                    $('#modal-processo_sei').html(data.ordem_servico.processo_sei_formatado);
                    $('#modal-numero_contrato').html(data.ordem_servico.numero_contrato_formatado);
                    $('#modal-numero_nota_fiscal').html(data.ordem_servico.numero_nota_fiscal);
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
</script>
@endsection
