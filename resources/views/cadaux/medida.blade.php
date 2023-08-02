@extends('layouts.base')

@section('cabecalho')
    @include('layouts.cabecalho', ['titulo' => 'Medidas', 'rota' => 'cadaux'])
@endsection

@section('conteudo')
@include('layouts.mensagem', ['mensagem' => $mensagem])
@include('layouts.erros', ['errors' => $errors])

<div class="row d-flex justify-content-center mt-3 containerTabela">
    <div class="row d-flex mb-3">
        <div class="col-10">
        </div>
        <div class="col-2 text-end">
            <button class="btn btn-secondary" onclick="scrollToNewForm('newform')">Nova Medida</a>
        </div>
    </div>
    <div class="row">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-2">ID</th>
                    <th class="col-md-3">Tipo</th>
                    <th class="col-md-2 text-end">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medidas as $key => $medida)
                    <tr>
                        <td>{{ $medida->id }}</td>
                        <td>
                            <span id="tipo-{{ $medida->id }}">{{ $medida->tipo }}</span>
                            <div class="input-group w-50" hidden id="input-tipo-{{ $medida->id }}">
                                <input type="text" class="form-control" value="{{ $medida->tipo }}">
                            </div>
                        </td>
                        <td>
                            <span class="d-flex flex-row-reverse">
                                <span id="btn-edit-{{ $medida->id }}" >
                                    <button class="btn btn-primary" onclick="toggleInput({{ $medida->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </span>
                                <div id="btn-submit-{{ $medida->id }}" class="me-2" hidden>
                                    <button class="btn btn-primary" onclick="editarMedida({{ $medida->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @csrf
                                </div>
                            </span>
                            @can('medida-editar')
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div id="newform" class="row d-flex justify-content-center mt-3 containerTabela">
    <h4>Nova Medida</h4>
    <form method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="form-group required mt-3">
                <label class="control-label" for="tipo"><strong>Tipo: </strong></label>
                <input type="text" class="inputForm form-control" name="tipo">
            </div>
        </div>
        <button class="btn btn-secondary mt-3 btnForm"><i class="far fa-criar"></i> Criar</button>
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
        const tipoEl = document.getElementById(`tipo-${Id}`);
        const inputtipoEl = document.getElementById(`input-tipo-${Id}`);
        const btnEl = document.getElementById(`btn-edit-${Id}`);
        const subEl = document.getElementById(`btn-submit-${Id}`);
        if (tipoEl.hasAttribute('hidden')) {
            tipoEl.removeAttribute('hidden');
            inputtipoEl.hidden = true;
            subEl.hidden = true;
        } else {
            inputtipoEl.removeAttribute('hidden');
            subEl.removeAttribute('hidden');
            tipoEl.hidden = true;
        }
    }

    function editarMedida(Id) {
        let formData = new FormData();
        const tipo = document.querySelector(`#input-tipo-${Id} > input`).value;
        const token = document.querySelector('input[name="_token"]').value;
        formData.append('tipo', tipo);
        formData.append('_token', token);

        const url = `/{{ env('APP_FOLDER', 'almoxarifado') }}/medidas/${Id}`;
        fetch(url, {
            body: formData,
            method: 'POST'
        }).then(function(response) {
            if(response.ok){
                toggleInput(Id);
                document.getElementById(`tipo-${Id}`).textContent = tipo;
            }else{
                response.json().then(data => {
                    alert(data.mensagem);
                });
            }
        });
    }
</script>
@endsection
