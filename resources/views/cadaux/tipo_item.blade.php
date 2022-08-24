@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Tipos de Item', 'rota' => 'cadaux'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])
@include('layouts.erros', ['errors' => $errors])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <div class="row d-flex mb-3">
        <div class="col-10">
        </div>
        <div class="col-2 text-end">
            <button class="btn btn-success" onclick="scrollToNewForm('newform')">Novo Tipo de Item</a>
        </div>
    </div>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-2">ID</th>
                    <th class="col-md-3">Departamento</th>
                    <th class="col-md-3">Nome</th>
                    <th class="col-md-2 text-end">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tipo_items as $key => $tipo_item)
                    <tr>
                        <td>{{ $tipo_item->id }}</td>
                        <td>
                            <span id="departamento-{{ $tipo_item->id }}">{{ $tipo_item->departamento->nome }}</span>
                            <div class="input-group w-50" hidden id="input-departamento-{{ $tipo_item->id }}">
                                <select class="form-select">
                                    <option value="">--Selecione--</option>
                                    @foreach ($userDeptos as $idDpt => $userDepto)
                                        <option value="{{ $idDpt }}" @if ($idDpt == $tipo_item->departamento_id) selected  @endif>{{ $userDepto }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td>
                            <span id="nome-{{ $tipo_item->id }}">{{ $tipo_item->nome }}</span>
                            <div class="input-group w-50" hidden id="input-nome-{{ $tipo_item->id }}">
                                <input type="text" class="form-control" value="{{ $tipo_item->nome }}">
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="d-flex flex-row-reverse">
                                <span id="btn-edit-{{ $tipo_item->id }}">
                                    <button class="btn btn-success" onclick="toggleInput({{ $tipo_item->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </span>
                                <div id="btn-submit-{{ $tipo_item->id }}" class="me-2" hidden>
                                    <button class="btn btn-success" onclick="editarTipoItem({{ $tipo_item->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @csrf
                                </div>
                            </span>
                            @can('tipo_item-editar')
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div id="newform" class="row d-flex justify-content-center mt-3 containerTabela">
    <h4>Novo Tipo de Item</h4>
    <form method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="form-group required mt-3">
                <label class="control-label" for="departamento"><strong>Departamento: </strong></label>
                <select class="form-select" name="departamento">
                    <option value="">--Selecione--</option>
                    @foreach ($userDeptos as $idDpt => $userDepto)
                        <option value="{{ $idDpt }}">{{ $userDepto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group required mt-3">
                <label class="control-label" for="nome"><strong>Nome: </strong></label>
                <input type="text" class="inputForm form-control" name="nome">
            </div>
        </div>
        <button class="btn btn-success mt-3 btnForm"><i class="far fa-criar"></i> Criar</button>
    </form>
</div>

<script>
    function scrollToNewForm(id){
        var element = document.querySelector(`#${id}`);
        //element.style.visibility = 'visible'; //style="visibility: hidden !important;"

        // scroll to element
        element.scrollIntoView({ behavior: 'smooth', block: 'start'});
    }

    function toggleInput(Id) {
        const deptoEl = document.getElementById(`departamento-${Id}`);
        const inputdeptoEl = document.getElementById(`input-departamento-${Id}`);
        const nomeEl = document.getElementById(`nome-${Id}`);
        const inputnomeEl = document.getElementById(`input-nome-${Id}`);
        const btnEl = document.getElementById(`btn-edit-${Id}`);
        const subEl = document.getElementById(`btn-submit-${Id}`);
        if (deptoEl.hasAttribute('hidden')) {
            deptoEl.removeAttribute('hidden');
            inputdeptoEl.hidden = true;
            nomeEl.removeAttribute('hidden');
            inputnomeEl.hidden = true;
            subEl.hidden = true;
        } else {
            inputdeptoEl.removeAttribute('hidden');
            inputnomeEl.removeAttribute('hidden');
            subEl.removeAttribute('hidden');
            deptoEl.hidden = true;
            nomeEl.hidden = true;
        }
    }

    function editarTipoItem(Id) {
        let formData = new FormData();
        const departamento = document.querySelector(`#input-departamento-${Id} > select`);
        const departamento_id = departamento.value;
        const departamento_txt = departamento.options[departamento.selectedIndex].text;
        const nome = document.querySelector(`#input-nome-${Id} > input`).value;
        const token = document.querySelector('input[name="_token"]').value;
        formData.append('departamento', departamento_id);
        formData.append('nome', nome);
        formData.append('_token', token);

        const url = `/tipo_items/${Id}`;
        fetch(url, {
            body: formData,
            method: 'POST'
        }).then(function(response) {
            if(response.ok){
                toggleInput(Id);
                document.getElementById(`departamento-${Id}`).textContent = departamento_txt;
                document.getElementById(`nome-${Id}`).textContent = nome;
            }else{
                response.json().then(data => {
                    alert(data.mensagem);
                });
            }
        });
    }
</script>
@endsection
