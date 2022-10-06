<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use Illuminate\Http\Request;
use App\Http\Requests\InventarioFormRequest;
use App\Models\Inventario;
use App\Http\Resources\Inventario as InventarioResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
     * @queryParam filter[base] Filtro de Local (base) do item. Example: Leopoldina
     * @queryParam filter[item] Filtro de Nome do Item Example: Adaptador Pvc
     * @queryParam filter[tipo_item] Filtro de Tipo de item. Example: alvenaria
     * @queryParam filter[tipo_medida] Filtro do Tipo de medida do item. Example: Pç
     * @queryParam filter[quantidade_maior_que] Filtro inicial de quantidade. Example: 200
     * @queryParam filter[quantidade_menor_que] Filtro final de quantidade. Example: 800
     * @queryParam sort Campo a ser ordenado (padrão ascendente, inserir um hífen antes para decrescente). Colunas possíveis: 'id', 'items.nome', 'tipo_items.nome', 'medidas.tipo', 'locais.nome', 'quantidade' Example: -locais.nome
     *
     */
    public function index()
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);

        $inventarios = QueryBuilder::for(Inventario::class)
        ->select('locais.nome', 'tipo_items.nome', 'medidas.tipo', 'items.nome', 'inventarios.*')
        ->leftJoin('locais', 'locais.id', 'inventarios.local_id')
        ->leftJoin('items', 'items.id', 'inventarios.item_id')
        ->leftJoin('tipo_items', 'tipo_items.id', 'items.tipo_item_id')
        ->leftJoin('medidas', 'medidas.id', 'items.medida_id')
        ->whereIn('inventarios.departamento_id',$userDeptos)
        ->allowedFilters([
                AllowedFilter::partial('base','locais.nome'), AllowedFilter::partial('item','items.nome'),
                AllowedFilter::partial('tipo_item','tipo_items.nome'), AllowedFilter::partial('tipo_medida','medidas.tipo'),
                AllowedFilter::scope('quantidade_maior_que'),
                AllowedFilter::scope('quantidade_menor_que'),
            ])
        ->allowedSorts('id', 'items.nome', 'tipo_items.nome', 'medidas.tipo', 'locais.nome', 'quantidade')
        ->paginate(15);

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
