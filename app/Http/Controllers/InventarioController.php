<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use App\Helpers\LocalHelper;
use Illuminate\Http\Request;
use App\Http\Requests\InventarioFormRequest;
use App\Models\Inventario;
use App\Http\Resources\Inventario as InventarioResource;
use App\Http\Resources\Item as ItemResource;
use App\Http\Resources\Itemventario;
use App\Models\Item;
use App\Models\Local;
use App\Models\TipoItem;
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
        ->allowedSorts('id', 'items.nome', 'tipo_items.nome', 'medidas.tipo', 'locais.nome', 'quantidade', 'qtd_alerta')
        ->paginate(15);

        return InventarioResource::collection($inventarios);
    }

    public function index_web(Request $request){
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);
        $filtros = array();
        $filtros['base'] = $request->query('f-base');
        $filtros['item'] = $request->query('f-item');
        $filtros['tipo_item'] = $request->query('f-tipo_item');
        $filtros['medida'] = $request->query('f-medida');
        $filtros['quantidade_maior_que'] = $request->query('f-quantidade_maior_que');
        $filtros['quantidade_menor_que'] = $request->query('f-quantidade_menor_que');
        //dd($filter);

        $inventarios = Inventario::query()
        ->select('l.nome', 'tpi.nome', 'md.tipo', 'i.nome', 'inventarios.*')
        ->leftJoin('locais as l', 'l.id', 'inventarios.local_id')
        ->leftJoin('items as i', 'i.id', 'inventarios.item_id')
        ->leftJoin('tipo_items as tpi', 'tpi.id', 'i.tipo_item_id')
        ->leftJoin('medidas as md', 'md.id', 'i.medida_id')
        ->whereIn('inventarios.departamento_id',$userDeptos)
        ->when($filtros['base'], function ($query, $val) {
            return $query->where('l.nome','like','%'.$val.'%');
        })
        ->when($filtros['item'], function ($query, $val) {
            return $query->where('i.nome','like','%'.$val.'%');
        })
        ->when($filtros['tipo_item'], function ($query, $val) {
            return $query->where('tpi.nome','like','%'.$val.'%');
        })
        ->when($filtros['medida'], function ($query, $val) {
            return $query->where('md.tipo','like','%'.$val.'%');
        })
        ->when($filtros['quantidade_maior_que'], function ($query, $val) {
            return $query->where('quantidade','>',$val);
        })
        ->when($filtros['quantidade_menor_que'], function ($query, $val) {
            return $query->where('quantidade','<',$val);
        })
        ->sortable()
        ->paginate(10);

        //dd($filter);
        $tipo_items = TipoItem::whereIn('departamento_id',$userDeptos)->get();
        $locais = Local::whereIn('departamento_id',$userDeptos)->where('tipo','=','base')->get();
        $mensagem = $request->session()->get('mensagem');
        return view('inventario.index', compact('inventarios','tipo_items','locais','mensagem','filtros'));
    }

    /**
     * Lista os itens de inventário do local e departamento especificados
     * @authenticated
     *
     * @queryParam base required ID do local. Example: 2
     * @queryParam depto required ID do departamento. Example: 3
     * @queryParam tipo required ID do tipo de item. Example: 2
     *
     */
    public function items_local(Request $request)
    {
        // $user = auth()->user();
        // $userDeptos = DepartamentoHelper::ids_deptos($user);
        $local_id = $request->query('base') ? $request->query('base') : null;
        $departamento_id = $request->query('depto') ? $request->query('depto') : null;
        $tipo_item_id = $request->query('tipo') ? $request->query('tipo') : null;

        $inventarios = Item::query()
            ->select('inventarios.*','items.*')
            ->join('inventarios', 'inventarios.item_id', '=', 'items.id')
            ->when($local_id, function ($query, $val) {
                return $query->where('inventarios.local_id','=',$val);
            })
            ->when($departamento_id, function ($query, $val) {
                return $query->where('inventarios.departamento_id','=',$val);
            })
            ->when($tipo_item_id, function ($query, $val) {
                return $query->where('items.tipo_item_id','=',$val);
            })
            ->get();

        return Itemventario::collection($inventarios);
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
        $inventario->departamento_id = $request->input('departamento_id') ? $request->input('departamento_id') : $inventario->departamento_id;
        $inventario->item_id = $request->input('item_id') ? $request->input('item_id') : $inventario->item_id;
        $inventario->local_id = $request->input('local_id') ? $request->input('local_id') : $inventario->local_id;
        $inventario->quantidade = $request->input('quantidade') ? $request->input('quantidade') : $inventario->quantidade;
        $inventario->qtd_alerta = $request->input('qtd_alerta');

        if ($inventario->save()) {
            return new InventarioResource($inventario);
        }
    }

    public function salvar_alerta(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->qtd_alerta = $request->input('qtd_alerta');

        if ($inventario->save()) {
            return response()->json(['mensagem' => "Alerta para ID {$inventario->id} definido com sucesso!"], 200);
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

    /**
     * lista os itens que estão acabando, de acordo com a quantidade definida no alerta, ou se já acabaram
     * @authenticated
     *
     *
     * @urlParam id integer required ID do inventário que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 93,
     *             "departamento_id": 3,
     *             "departamento": "CGPABI/DGPU",
     *             "item_id": 1,
     *             "item": "Argamassa, Na Cor Cinza ",
     *             "tipo_item": "alvenaria",
     *             "medida": "SC",
     *             "local_id": 1,
     *             "local": "LAB",
     *             "local_tipo": "base",
     *             "quantidade": 100,
     *             "qtd_alerta": 101
     *         },
     *         {
     *             "id": 106,
     *             "departamento_id": 3,
     *             "departamento": "CGPABI/DGPU",
     *             "item_id": 18,
     *             "item": "Ripa de Peroba do Norte 1,5 cm  X 5 cm  Bruta (cupiúba)",
     *             "tipo_item": "carpintaria",
     *             "medida": "MT",
     *             "local_id": 2,
     *             "local": "UEM Base Leopoldina",
     *             "local_tipo": "base",
     *             "quantidade": 5,
     *             "qtd_alerta": 6
     *         }
     *     ]
     * }
     */
    public function items_acabando(){
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);

        $inventarios = Inventario::query()
            //->whereIn('departamento_id',$userDeptos)
            //->whereIn('id',[93,106])
            ->whereRaw('quantidade <= qtd_alerta')
            ->get();


        return InventarioResource::collection($inventarios);
    }
}
