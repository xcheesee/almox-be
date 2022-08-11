<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Resources\Item as ItemResource;

/**
 * @group Item
 *
 * APIs para listar, cadastrar, editar e remover dados de itens
 */

class ItemController extends Controller
{
    /**
     * Lista os itens
     * @authenticated
     *
     */
    public function index()
    {
        $itens = Item::paginate(15);
            return ItemResource::collection($itens);
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
     * Cadastra um item
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam medida_id integer ID da Medida. Example: 2
     * @bodyParam nome string required Nome. Example: tinta
     * @bodyParam tipo enum required ('pintura', 'hidraulica', 'carpintaria', 'alvenaria') Tipo. Example: pintura
     * @bodyParam descricao text nullable Descrição. Example: tinta
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "medida_id": 2,
     *         "nome": tinta,
     *         "tipo": "pintura",
     *         "descricao": "tinta"
     *     }
     * }
     */
    public function store(Request $request)
    {
        $item = new Item();
        $item->departamento_id = $request->input('departamento_id');
        $item->medida_id = $request->input('medida_id');
        $item->nome = $request->input('nome');
        $item->tipo = $request->input('tipo');
        $item->descricao = $request->input('descricao');

        if ($item->save()) {
            return new ItemResource($item);
        }
    }

    /**
     * Mostra um item
     * @authenticated
     *
     * @urlParam id integer required ID de item. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "medida_id": 2,
     *         "nome": tinta,
     *         "tipo": "pintura",
     *         "descricao": "tinta"
     *     }
     * }
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);
        return new ItemResource($item);
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
     * Edita um Item
     * @authenticated
     *
     *
     * @urlParam id integer required ID do item que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam medida_id integer ID da Medida. Example: 2
     * @bodyParam nome string required Nome. Example: tinta
     * @bodyParam tipo enum required ('pintura', 'hidraulica', 'carpintaria', 'alvenaria') Tipo. Example: pintura
     * @bodyParam descricao text nullable Descrição. Example: tinta
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "medida_id": 2,
     *         "nome": tinta,
     *         "tipo": "pintura",
     *         "descricao": "tinta"
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->departamento_id = $request->input('departamento_id');
        $item->medida_id = $request->input('medida_id');
        $item->nome = $request->input('nome');
        $item->tipo = $request->input('tipo');
        $item->descricao = $request->input('descricao');

        if ($item->save()) {
            return new ItemResource($item);
        }
    }

    /**
     * Deleta um item
     * @authenticated
     *
     *
     * @urlParam id integer required ID do item que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "item deletado com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "medida_id": 2,
     *         "nome": tinta,
     *         "tipo": "pintura",
     *         "descricao": "tinta"
     *     }
     * }
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        if ($item->delete()) {
            return response()->json([
                'message' => 'Item deletado com sucesso!',
                'data' => new ItemResource($item)
            ]);
        }
    }
}
