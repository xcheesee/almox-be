<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntradaItem;
use App\Http\Resources\EntradaItem as EntradaItemResource;

/**
 * @group EntradaItem
 *
 * APIs para listar, cadastrar, editar e remover dados de entrada de itens
 */

class EntradaItemController extends Controller
{
    /**
     * Lista as entradas de itens
     * @authenticated
     *
     */
    public function index()
    {
        $entrada_itens = EntradaItem::paginate(15);
        return EntradaItemResource::collection($entrada_itens);
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
     * Cadastra uma nova entrada de item
     * @authenticated
     *
     *
     * @bodyParam entrada_id integer ID da entrada. Example: 2
     * @bodyParam item_id integer ID do item. Example: 2
     * @bodyParam quantidade float required Quantidade. Example: 10
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "entrada_id": 2,
     *         "item_id": 2,
     *         "quantidade": "10"
     *     }
     * }
     */
    public function store(Request $request)
    {
        $entrada_item = new EntradaItem();
        $entrada_item->entrada_id = $request->input('entrada_id');
        $entrada_item->item_id = $request->input('item_id');
        $entrada_item->quantidade = $request->input('quantidade');

        if ($entrada_item->save()) {
            return new EntradaItemResource($entrada_item);
        }
    }

    /**
     * Mostra uma entrada de item específica
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada de item. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "entrada_id": 2,
     *         "item_id": 2,
     *         "quantidade": "10"
     *     }
     * }
     */
    public function show($id)
    {
        $entrada_item = EntradaItem::findOrFail($id);
        return new EntradaItemResource($entrada_item);
    }

    public function items_entrada($id){
        $entrada_itens = EntradaItem::where("entrada_id","=",$id)->get();
        return EntradaItemResource::collection($entrada_itens);
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
     * Edita uma entrada de item
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada de item que deseja editar. Example: 1
     *
     * @bodyParam entrada_id integer ID da entrada. Example: 2
     * @bodyParam item_id integer ID do item. Example: 2
     * @bodyParam quantidade float required Quantidade. Example: 10
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "entrada_id": 2,
     *         "item_id": 2,
     *         "quantidade": "10"
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $entrada_item = EntradaItem::findOrFail($id);
        $entrada_item->entrada_id = $request->input('entrada_id');
        $entrada_item->item_id = $request->input('item_id');
        $entrada_item->quantidade = $request->input('quantidade');

        if ($entrada_item->save()) {
            return new EntradaItemResource($entrada_item);
        }
    }

    /**
     * Deleta uma entrada de item
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "entrada deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "entrada_id": 2,
     *         "item_id": 2,
     *         "quantidade": "10"
     *     }
     * }
     */
    public function destroy($id)
    {
        $entrada_item = EntradaItem::findOrFail($id);

        if ($entrada_item->delete()) {
            return response()->json([
                'message' => 'Entrada de item deletada com sucesso!',
                'data' => new EntradaItemResource($entrada_item)
            ]);
        }
    }
}
