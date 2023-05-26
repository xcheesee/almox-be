<?php

namespace App\Http\Controllers;

use App\Models\TransferenciaItens;
use Illuminate\Http\Request;
use App\Http\Resources\TransferenciaDeMateriaisItem as TransferenciaDeMateriaisItemResource;


class TransferenciaItensController extends Controller
{
    /**
     * Lista Todos os itens de Transferencia.
     * @authenticated
     */
    public function index()
    {
        $itens = TransferenciaItens::all();

        return response()->json([
            'mensagem' => 'Todos itens de transfrerencia cadastrados',
            'itens' => $itens
        ], 200);
    }

    /**
     * Cadastra um item de transferencia
     * @authenticated
     * 
     * @bodyParam entrada_id integer required ID da entrada. Example: 1
     * @bodyParam item_id integer required ID do item transferencia. Example: 5
     * @bodyParam quantidade integer required Quantidade de itens. Example: 1 
     * 
     * @response 200 {
     *      "mensagem": "Item de transferencia cadastrado com sucesso!",
     *      "itens": {
     *          "entrada_id": 1,
     *          "item_id": 5,
     *          "quantidade": 1,
     *          "updated_at": "2023-05-10T14:37:52.000000Z",
     *          "created_at": "2023-05-10T14:37:52.000000Z",
     *          "id": 4
     *      }
     * }
     */
    public function store(Request $request)
    {
        $itens = new TransferenciaItens();

        $itens->entrada_id = $request->entrada_id;
        $itens->item_id = $request->item_id;
        $itens->quantidade = $request->quantidade;

        $itens->save();

        return response()->json([
            'mensagem' => 'Item de transferencia cadastrado com sucesso!',
            'itens' => $itens
        ], 200);
        }
    
    /**
     * Mostra um Item de Transferencia
     * @authenticated
     * 
     * @urlParam integer ID do item.
     * 
     * @response 200{
     *      "mensagem": "Item de transferencia encontrado com sucesso!",
     *      "itens": {
     *           "id": 2,
     *           "entrada_id": 244,
     *           "item_id": 9,
     *           "quantidade": 4,
     *           "created_at": "2023-05-05T15:20:08.000000Z",
     *           "updated_at": "2023-05-05T15:22:01.000000Z"
     *      }
     * }
     * 
     * @response 404 {
     *      "mensagem": "Item de transferencia não encontrada!"
     *      }
     */
    public function show($id)
    {
        $itens = TransferenciaItens::findOrFail($id);
        return new TransferenciaDeMateriaisItemResource($itens);

        //if($itens)
        //{
        //    return response()->json([
        //        'mensagem' => 'Item de transferencia encontrado com sucesso!',
        //        'itens' => $itens
        //    ], 200);
        //} else {
        //    return response()->json([
        //        'mensagem' => 'Item de transferencia não encontrada!',
        //    ], 404);
        //}
    }

    /**
     * Atualiza um Item de Transferencia
     * @authenticated
     * 
     * @urlParam integer ID do item que deseja atualizar. Example 5
     * 
     * @bodyParam entrada_id integer required ID da entrada. Example: 244
     * @bodyParam item_id integer required ID do item transferencia. Example: 9
     * @bodyParam quantidade integer required Quantidade de itens. Example: 4
     * 
     * @response 200 {
     *      "mensagem": "Item de transferencia atualizado com sucesso!",
     *      "itens": {
     *          "id": 5,
     *          "entrada_id": 244,
     *          "item_id": 9,
     *          "quantidade": 4,
     *          "created_at": "2023-05-10T14:41:11.000000Z",
     *          "updated_at": "2023-05-10T14:41:22.000000Z"
     *      }
     * }
     */
    public function update(Request $request, $id)
    {
        $itens = TransferenciaItens::findOrFail($id);

        $itens->entrada_id = $request->entrada_id;
        $itens->item_id = $request->item_id;
        $itens->quantidade = $request->quantidade;

        
        $itens->update();
        
        return response()->json([
            'mensagem' => 'Item de transferencia atualizado com sucesso!',
            'itens' => $itens
        ], 200);
    }

    /**
     * Deleta um Item de Transferencia
     * @authenticated
     * 
     * @urlParam integer ID do item. Example: 6
     * 
     * @response 200{
     *      "mensagem": "Item de transferencia deletado com sucesso!",
    *       "itens": {
    *           "id": 6,
    *           "entrada_id": 5,
    *           "item_id": 19,
    *           "quantidade": 2,
    *           "created_at": "2023-05-10T14:43:16.000000Z",
    *           "updated_at": "2023-05-10T14:43:16.000000Z"
    *       }
     * }
     * 
     * @response 404 {
     *      "mensagem": "Item de transferencia não encontrado para deletar."
     *      }
     */
    public function destroy($id)
    {
        $itens = TransferenciaItens::where('id', $id)->first();

        
        if($itens)
        {
            $itens->delete();

            return response()->json([
                'mensagem' => 'Item de transferencia deletado com sucesso!',
                'itens' => $itens
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Item de transferencia não encontrado para deletar.',
            ], 404);
        }
    }
}
