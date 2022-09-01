<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\InventarioFormRequest;
use App\Models\Inventario;
use App\Http\Resources\Inventario as InventarioResource;

/**
 * @group Inventário
 *
 * APIs para listar, cadastrar, editar e remover dados de inventários
 */

class InventarioController extends Controller
{
    /**
     * Lista as entradas de inventário
     * @authenticated
     *
     */
    public function index()
    {
        $inventarios = Inventario::paginate(15);
            return InventarioResource::collection($inventarios);
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
     * Cadastra um novo inventário
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do departamento. Example: 2
     * @bodyParam item_id integer ID do item. Example: 2
     * @bodyParam local_id integer ID do local. Example: 2
     * @bodyParam quantidade float required Quantidade. Example: 10
     * @bodyParam qtd_alerta float required Quantidade. Example: 10
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "item_id": 2,
     *         "local_id": 2,
     *         "quantidade": "10",
     *         "qtd_alerta": "10"
     *     }
     * }
     */
    public function store(InventarioFormRequest $request)
    {
        $inventario = new Inventario();
        $inventario->departamento_id = $request->input('departamento_id');
        $inventario->item_id = $request->input('item_id');
        $inventario->local_id = $request->input('local_id');
        $inventario->quantidade = $request->input('quantidade');
        $inventario->qtd_alerta = $request->input('qtd_alerta');

        if ($inventario->save()) {
            return new InventarioResource($inventario);
        }
    }

    /**
     * Mostra uma entrada de inventário
     * @authenticated
     *
     * @urlParam id integer required ID de inventário. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "item_id": 2,
     *         "local_id": 2,
     *         "quantidade": "10",
     *         "qtd_alerta": "10"
     *     }
     * }
     */
    public function show($id)
    {
        $inventario = Inventario::findOrFail($id);
        return new InventarioResource($inventario);
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
     * Edita um inventario
     * @authenticated
     *
     *
     * @urlParam id integer required ID do inventário que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do departamento. Example: 2
     * @bodyParam item_id integer ID do item. Example: 2
     * @bodyParam local_id integer ID do local. Example: 2
     * @bodyParam quantidade float required Quantidade. Example: 10
     * @bodyParam qtd_alerta float required Quantidade. Example: 10
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "item_id": 2,
     *         "local_id": 2,
     *         "quantidade": "10",
     *         "qtd_alerta": "10"
     *     }
     * }
     */
    public function update(InventarioFormRequest $request, $id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->departamento_id = $request->input('departamento_id');
        $inventario->item_id = $request->input('item_id');
        $inventario->local_id = $request->input('local_id');
        $inventario->quantidade = $request->input('quantidade');
        $inventario->qtd_alerta = $request->input('qtd_alerta');

        if ($inventario->save()) {
            return new InventarioResource($inventario);
        }
    }

    /**
     * Deleta um inventário
     * @authenticated
     *
     *
     * @urlParam id integer required ID do inventário que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "entrada deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "item_id": 2,
     *         "local_id": 2,
     *         "quantidade": "10",
     *         "qtd_alerta": "10"
     *     }
     * }
     */
    public function destroy($id)
    {
        $inventario = Inventario::findOrFail($id);

        if ($inventario->delete()) {
            return response()->json([
                'message' => 'Inventário deletado com sucesso!',
                'data' => new InventarioResource($inventario)
            ]);
        }
    }
}
