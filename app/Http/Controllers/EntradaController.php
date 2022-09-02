<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EntradaFormRequest;
use App\Models\Entrada;
use App\Models\EntradaItem;
use App\Models\Inventario;
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
     * @bodyParam data_entrada date required Data do serviço. Example: "2022-08-11"
     * @bodyParam processo_sei string required Processo SEI. Example: 0123000134569000
     * @bodyParam numero_contrato string required Número do contrato. Example: 0001SVMA2022
     * @bodyParam numero_nota_fiscal string required Número da Nota Fiscal. Example: 1234
     * @bodyParam arquivo_nota_fiscal file nullable Arquivo da Nota Fiscal.
     * @bodyParam entrada_items object Lista de itens. Example: [{"id": 1, "quantidade": 500},{"id": 2, "quantidade": 480}]
     * @bodyParam entrada_items[].id integer ID do item. Example: 2
     * @bodyParam entrada_items[].quantidade integer Quantidade informada para o item. Example: 480
     *
     *
     * {
     *       "departamento_id": 2,
     *       "local_id": 2,
     *       "data_entrada": "2022-08-11",
     *       "processo_sei": "0123000134569000",
     *       "numero_contrato": "0001SVMA2022",
     *       "numero_nota_fiscal": "1234",
     *       "arquivo_nota_fiscal": "DANFE?"
     *       "entrada_items": [
     *           {
     *               "id": 1,
     *               "quantidade": 500
     *           },
     *           {
     *               "id": 2,
     *               "quantidade": 480
     *           }
     *       ]
     *   }
     */
    public function store(EntradaFormRequest $request)
    {
        $entrada = new Entrada();
        $entrada->departamento_id = $request->input('departamento_id');
        $entrada->local_id = $request->input('local_id');
        $entrada->data_entrada = $request->input('data_entrada');
        $entrada->processo_sei = $request->input('processo_sei');
        $entrada->numero_contrato = $request->input('numero_contrato');
        $entrada->numero_nota_fiscal = $request->input('numero_nota_fiscal');
        // Campo para adicionar os arquivos das notas fiscais.
        $entrada->arquivo_nota_fiscal = $request->file('arquivo_nota_fiscal');

            $upload = $request->arquivo_nota_fiscal->store('public/files');

        if ($entrada->save()) {

            // Lógica para retornar uma array de "entrada_items", contendo uma coleção de objetos com id do item e a quantidade.
            $entradaItens = EntradaItem::query()->where('entrada_id','=',$entrada->id)->get();

            $entrada_items = array();

            foreach ($entradaItens as $key => $entradaItem){
                $entrada_items[$key]['id'] = $entradaItem->item_id;
                $entrada_items[$key]['quantidade'] = $entradaItem->quantidade;
            }

            $entrada_item = (object) $entrada_items;

            //lógica para adicionar a quantidade dos itens de entrada no inventario
            foreach ($entradaItens as $entrada_items){
                $inventario = Inventario::where('departamento_id','=',$entrada->departamento_id)
                                        ->where('local_id','=',$entrada->local_id)
                                        ->where('item_id','=',$entrada_items->item_id)
                                        ->first();

                if ($inventario) {
                    $inventario->quantidade += $entrada_items->quantidade;
                    $inventario->save();
                } else {
                    $inventario = new Inventario();
                    $inventario->departamento_id = $entrada->departamento_id;
                    $inventario->item_id = $entrada_items->item_id;
                    $inventario->local_id = $entrada->local_id;
                    $inventario->quantidade = $entrada_items->quantidade;
                    $inventario->qtd_alerta = 0;
                    $inventario->save();
                }
            }

            return response()->json([
                'departamento_id' => $entrada->departamento_id,
                'local_id' => $entrada->local_id,
                'processo_sei' => $entrada->processo_sei,
                'numero_contrato' => $entrada->numero_contrato,
                'numero_nota_fiscal' => $entrada->numero_nota_fiscal,
                'entrada_items' => $entrada_item,
            ]);
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
     *         "data_entrada": "2022-08-11",
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
     * @bodyParam data_entrada date required Data do serviço. Example: "2022-08-11"
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
     *         "data_entrada": "2022-08-11",
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "DANFE?"
     *     }
     * }
     */
    public function update(EntradaFormRequest $request, $id)
    {
        $entrada = Entrada::findOrFail($id);
        $entrada->departamento_id = $request->input('departamento_id');
        $entrada->local_id = $request->input('local_id');
        $entrada->data_entrada = $request->input('data_entrada');
        $entrada->processo_sei = $request->input('processo_sei');
        $entrada->numero_contrato = $request->input('numero_contrato');
        $entrada->numero_nota_fiscal = $request->input('numero_nota_fiscal');
        // Campo para alterar o arquivo enviado da nota fiscal.
        $entrada->arquivo_nota_fiscal = $request->input('arquivo_nota_fiscal');

            $arquivo_nota_fiscal = Entrada::query()->where('arquivo_nota_fiscal','=',$id)->first();

                if($arquivo_nota_fiscal){
                    $arquivo_nota_fiscal->delete();
                }

            $upload = $request->arquivo_nota_fiscal->store('public/files');

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
     *         "departamento_id": 2,
     *         "local_id": 2,
     *         "data_entrada": "2022-08-11",
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "DANFE?"
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
