<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrdemServicoItemFormRequest;
use App\Models\OrdemServicoItem;
use App\Http\Resources\OrdemServicoItem as OrdemServicoItemResource;

/**
 * @group OrdemServicoItem
 *
 * APIs para listar, cadastrar, editar e remover dados de ordens de serviços itens
 */

class OrdemServicoItemController extends Controller
{
    /**
     * Lista as ordens de serviços itens
     * @authenticated
     *
     */
    public function index()
    {
        $ordem_servico_itens = OrdemServicoItem::paginate(15);
            return OrdemServicoItemResource::collection($ordem_servico_itens);
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
     * Cadastra uma ordem de serviço item
     * @authenticated
     *
     *
     * @bodyParam ordem_servico_id integer ID da Ordem de serviço. Example: 2
     * @bodyParam item_id integer ID do Item. Example: 1
     * @bodyParam quantidade float required. Example: 10
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "ordem_servico_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function store(OrdemServicoItemFormRequest $request)
    {
        $ordem_servico_item = new OrdemServicoItem();
        $ordem_servico_item->ordem_servico_id = $request->input('ordem_servico_id');
        $ordem_servico_item->item_id = $request->input('item_id');
        $ordem_servico_item->quantidade = $request->input('quantidade');

        if ($ordem_servico_item->save()) {
            return new OrdemServicoItemResource($ordem_servico_item);
        }
    }

    /**
     * Mostra uma ordem de serviço item
     * @authenticated
     *
     * @urlParam id integer required ID da ordem de serviço item. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "ordem_servico_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function show($id)
    {
        $ordem_servico_item= OrdemServicoItem::findOrFail($id);
        return new OrdemServicoItemResource($ordem_servico_item);
    }

    /**
     * Mostra os itens de uma ordem de serviço
     * @authenticated
     *
     * @urlParam id integer required ID da ordem de serviço. Example: 2
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "ordem_servico_id": 2,
     *             "item_id": 1,
     *             "quantidade": 10
     *         },{
     *             "id": 2,
     *             "ordem_servico_id": 2,
     *             "item_id": 3,
     *             "quantidade": 800
     *         }
     *     ]
     * }
     */
    public function items_ordem($id){
        $ordem_servico_itens = OrdemServicoItem::where("ordem_servico_id","=",$id)->get();
        return OrdemServicoItemResource::collection($ordem_servico_itens);
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
     * Edita uma ordem de serviço item
     * @authenticated
     *
     *
     * @urlParam id integer required ID da ordem de serviço item que deseja editar. Example: 1
     *
     * @bodyParam ordem_servico_id integer ID da Ordem de serviço. Example: 2
     * @bodyParam item_id integer ID do Item. Example: 1
     * @bodyParam quantidade float required. Example: 10
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "ordem_servico_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function update(OrdemServicoItemFormRequest $request, $id)
    {
        $ordem_servico_item = OrdemServicoItem::findOrFail($id);
        $ordem_servico_item->ordem_servico_id = $request->input('ordem_servico_id');
        $ordem_servico_item->item_id = $request->input('item_id');
        $ordem_servico_item->quantidade = $request->input('quantidade');

        if ($ordem_servico_item->save()) {
            return new OrdemServicoItemResource($ordem_servico_item);
        }
    }

    /**
     * Deleta uma ordem de serviço item
     * @authenticated
     *
     *
     * @urlParam id integer required ID da ordem de serviço item que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "Ordem de serviço item deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "ordem_servico_id": 2,
     *         "item_id": 1,
     *         "quantidade": 10
     *     }
     * }
     */
    public function destroy($id)
    {
        $ordem_servico_item = OrdemServicoItem::findOrFail($id);

        if ($ordem_servico_item->delete()) {
            return response()->json([
                'message' => 'Ordem de serviço item deletada com sucesso!',
                'data' => new OrdemServicoItemResource($ordem_servico_item)
            ]);
        }
    }
}
