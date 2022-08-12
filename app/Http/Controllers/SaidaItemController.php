<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaidaItem;
use App\Http\Resources\SaidaItem as SaidaItemResource;

/**
 * @group SaidaItem
 *
 * APIs para listar, cadastrar, editar e remover dados de saida de itens
 */

class SaidaItemController extends Controller
{
    /**
     * Lista as saidas de itens
     * @authenticated
     *
     */
    public function index()
    {
        $saida_itens = SaidaItem::paginate(15);
            return SaidaItemResource::collection($saida_itens);
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
     * Cadastra uma saide de item
     * @authenticated
     *
     *
     * @bodyParam saida_id integer ID da Ordem de serviço. Example: 2
     * @bodyParam item_id integer ID do Item. Example: 1
     * @bodyParam quantidade float required. Example: 10
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "saida_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function store(Request $request)
    {
        $saida_item = new SaidaItem();
        $saida_item->saida_id = $request->input('saida_id');
        $saida_item->item_id = $request->input('item_id');
        $saida_item->quantidade = $request->input('quantidade');

        if ($saida_item->save()) {
            return new SaidaItemResource($saida_item);
        }
    }

    /**
     * Mostra uma saida de item
     * @authenticated
     *
     * @urlParam id integer required ID da saida de item. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "saida_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function show($id)
    {
        $saida_item= SaidaItem::findOrFail($id);
        return new SaidaItemResource($saida_item);
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
     * Edita uma saida de item
     * @authenticated
     *
     *
     * @urlParam id integer required ID da saida de item que deseja editar. Example: 1
     *
     * @bodyParam saida_id integer ID da Ordem de serviço. Example: 2
     * @bodyParam item_id integer ID do Item. Example: 1
     * @bodyParam quantidade float required. Example: 10
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "saida_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $saida_item = OrdemServicoItem::findOrFail($id);
        $saida_item->saida_id = $request->input('saida_id');
        $saida_item->item_id = $request->input('item_id');
        $saida_item->quantidade = $request->input('quantidade');

        if ($saida_item->save()) {
            return new OrdemServicoItemResource($saida_item);
        }
    }

    /**
     * Deleta uma saida de item
     * @authenticated
     *
     *
     * @urlParam id integer required ID da saida de item que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "Saida de item deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "saida_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function destroy($id)
    {
        $saida_item = SaidaItem::findOrFail($id);

        if ($saida_item->delete()) {
            return response()->json([
                'message' => 'Saida de item deletada com sucesso!',
                'data' => new SaidaItemResource($saida_item)
            ]);
        }
    }
}
