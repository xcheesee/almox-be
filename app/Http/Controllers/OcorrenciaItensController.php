<?php

namespace App\Http\Controllers;

use App\Models\OcorrenciaItens;
use Illuminate\Http\Request;

class OcorrenciaItensController extends Controller
{
    /**
     * Lista todos os itens de uma Ocorrencia.
     * @authenticated
     * 
     */
    public function index()
    {
        $item = OcorrenciaItens::all();

        return response()->json([
            'mensagem' => 'Todos itens de ocorrencias cadastrados',
            'itens' => $item
        ], 200);
    }

    /**
     * Mostra um item de Ocorrencia
     * @autheticated
     * 
     * @urlParam integer required ID do item que deseja mostrar.
     * 
     * @response 200 {
     *      "mensagem": "Item de ocorrencia encontrado com sucesso!",
     *      "item": {
     *        "id": 1,
     *        "ocorrencia_id": 1,
     *        "item_id": 2,
     *        "quantidade": 1,
     *        "created_at": "2023-05-08T14:30:10.000000Z",
     *        "updated_at": "2023-05-08T14:30:10.000000Z"
     *    }  
     *}       
     * 
     * @response 404 {
     *      "mensagem": "Item de ocorrencia não encontrada!"
     *      }
     */
    public function show($id)
    {
        $item = OcorrenciaItens::where('id', $id)->first();

        if($item)
        {
            return response()->json([
                'mensagem' => 'Item de ocorrencia encontrado com sucesso!',
                'item' => $item
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Item de ocorrencia não encontrada!',
            ], 404);
        }

    }

    /**
     * Cadastra um Item de Ocorrencia.
     * @authenticated
     * 
     * @bodyParam ocorrencia_id integer required ID da Ocorrencia. Example: 5
     * @bodyParam item_id integer required ID do item. Example: 6
     * @bodyParam quantidade integer required Quantidade de itens. Example: 36
     * 
     * @response 200 {
     *      "mensagem": "Item de ocorrencia cadastrado com sucesso!",
     *      "item": {
     *          "ocorrencia_id": 5,
     *          "item_id": 6,
     *          "quantidade": 36,
     *          "updated_at": "2023-05-10T15:46:54.000000Z",
     *          "created_at": "2023-05-10T15:46:54.000000Z",
     *          "id": 6
     *      }
     * }
     */
    public function store(Request $request)
    {
        $item = new OcorrenciaItens();

        $item->ocorrencia_id = $request->ocorrencia_id;
        $item->item_id = $request->item_id;
        $item->quantidade = $request->quantidade;

        $item->save();

        return response()->json([
            'mensagem' => 'Item de ocorrencia cadastrado com sucesso!',
            'item' => $item
        ], 200);
    }

    /**
     * Edita um Item de Ocorrencia.
     * @authenticated
     * 
     * 
     * @urlParam integer required ID do item que deseja editar. Example 6
     * 
     * @bodyParam ocorrencia_id integer required ID da Ocorrencia. Example: 5
     * @bodyParam item_id integer required ID do item. Example: 6
     * @bodyParam quantidade integer required Quantidade de itens. Example: 36
     * 
     * @response 200 {
     *      "mensagem": "Item de ocorrencia editado com sucesso!",
     *      "item": {
     *          "id": 6,
     *          "ocorrencia_id": 5,
     *          "item_id": 6,
     *          "quantidade": 36,
     *          "created_at": "2023-05-10T15:46:54.000000Z",
     *          "updated_at": "2023-05-10T15:46:54.000000Z"
     *      }
     * }
     */
    public function update(Request $request, $id)
    {
        $item = OcorrenciaItens::findOrFail($id);

        $item->ocorrencia_id = $request->ocorrencia_id;
        $item->item_id = $request->item_id;
        $item->quantidade = $request->quantidade;

        $item->update();

        return response()->json([
            'mensagem' => 'Item de ocorrencia editado com sucesso!',
            'item' => $item
        ], 200);
    }

    /**
     * Deleta um item de Ocorrencia
     * @autheticated
     * 
     * @urlParam integer required ID do item que deseja deletar.
     * 
     * @response 200 {
     *      "mensagem": "Ocorrencia deletada com sucesso!",
     *      "ocorrencia": {
     *          "id": 2,
     *          "ocorrencia_id": 0,
     *          "item_id": 3,
     *          "quantidade": 1,
     *          "created_at": "2023-05-08T14:31:20.000000Z",
     *          "updated_at": "2023-05-08T14:32:24.000000Z"
     *      }
     *  }     
     * 
     * @response 404 {
     *      "mensagem": "Ocorrencia não encontrada para deletar."
     *      }
     */
    public function destroy($id)
    {
        $item = OcorrenciaItens::where('id', $id)->first();

        if($item)
        {
            $item->delete();

            return response()->json([
                'mensagem' => 'Ocorrencia deletada com sucesso!',
                'ocorrencia' => $item
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Ocorrencia não encontrada para deletar.',
            ], 404);
        }


    }
}
