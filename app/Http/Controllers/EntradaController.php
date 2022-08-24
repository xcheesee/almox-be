<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Http\Resources\Entrada as EntradaResource;

/**
 * @group Entrada
 *
 * APIs para listar, cadastrar, editar e remover dados de entrada
 */

class EntradaController extends Controller
{
    /**
     * Lista as entradas
     * @authenticated
     *
     */
    public function index()
    {
        $entradas = Entrada::paginate(15);
            return EntradaResource::collection($entradas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Cadastra uma nova entrada
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do departamento. Example: 2
     * @bodyParam local_id integer ID do local. Example: 2
     * @bodyParam processo_sei string required Processo SEI. Example: 0123000134569000
     * @bodyParam numero_contrato string required Número do contrato. Example: 0001SVMA2022
     * @bodyParam numero_nota_fiscal string required Número da Nota Fiscal. Example: 1234
     * @bodyParam arquivo_nota_fiscal file nullable Arquivo da Nota Fiscal.
     * @bodyParam entrada_items object Lista de itens. Example: [{"id": 1, "quantidade": 500},{"id": 2, "quantidade": 480}]
     * @bodyParam entrada_items[].id integer ID do item. Example: 2
     * @bodyParam entrada_items[].quantidade integer Quantidade informada para o item. Example: 480
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "local_id": 2,
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "DANFE?"
     *     }
     * }
     */
    public function store(Request $request)
    {
        $entrada = new Entrada();
        $entrada->departamento_id = $request->input('departamento_id');
        $entrada->local_id = $request->input('local_id');
        $entrada->processo_sei = $request->input('processo_sei');
        $entrada->numero_contrato = $request->input('numero_contrato');
        $entrada->numero_nota_fiscal = $request->input('numero_nota_fiscal');

        //TODO: alterar este campo para receber upload de arquivo (https://laratutorials.com/laravel-8-file-upload-via-api/)
        $entrada->arquivo_nota_fiscal = $request->input('arquivo_nota_fiscal');

        if ($entrada->save()) {
            return new EntradaResource($entrada);
        }
    }

    /**
     * Mostra uma entrada específica
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "local_id": 2,
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "DANFE?"
     *     }
     * }
     */
    public function show($id)
    {
        $entrada = Entrada::findOrFail($id);
        return new EntradaResource($entrada);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Edita uma entrada
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do departamento. Example: 2
     * @bodyParam local_id integer ID do local. Example: 2
     * @bodyParam processo_sei string required Processo SEI. Example: 0123000134569000
     * @bodyParam numero_contrato string required Número do contrato. Example: 2343rbte67b63
     * @bodyParam numero_nota_fiscal string required Número da Nota Fiscal. Example: 1234
     * @bodyParam arquivo_nota_fiscal string required Arquivo da Nota Fiscal. Example: DANFE?
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "local_id": 2,
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "DANFE?"
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $entrada = Entrada::findOrFail($id);
        $entrada->departamento_id = $request->input('departamento_id');
        $entrada->local_id = $request->input('local_id');
        $entrada->processo_sei = $request->input('processo_sei');
        $entrada->numero_contrato = $request->input('numero_contrato');
        $entrada->numero_nota_fiscal = $request->input('numero_nota_fiscal');
        $entrada->arquivo_nota_fiscal = $request->input('arquivo_nota_fiscal');

        if ($entrada->save()) {
            return new EntradaResource($entrada);
        }
    }

    /**
     * Deleta uma entrada
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "entrada deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "nome": "Teste LTDA",
     *         "andar": "5",
     *         "ativo": "1"
     *     }
     * }
     */
    public function destroy($id)
    {
        $entrada = Entrada::findOrFail($id);

        if ($entrada->delete()) {
            return response()->json([
                'message' => 'Entrada deletada com sucesso!',
                'data' => new EntradaResource($entrada)
            ]);
        }
    }
}
