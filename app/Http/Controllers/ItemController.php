<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Resources\Item as ItemResource;
use App\Models\Medida;
use App\Models\TipoItem;

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
    public function index(Request $request)
    {

        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            $itens = Item::paginate(15);
            return ItemResource::collection($itens);
        }

        $filtros = array();
        $filtros['tipo'] = $request->query('f-tipo');
        $filtros['nome'] = $request->query('f-nome');
        $filtros['medida'] = $request->query('f-medida');
        $filtros['departamento'] = $request->query('f-departamento');

        $data = Item::sortable()
            ->select('items.*', 'tp.nome as tipoitem', 'md.tipo as tipomedida')
            ->leftJoin('tipo_items as tp', 'tipo_item_id', '=', 'tp.id')
            ->leftJoin('medidas as md', 'medida_id', '=', 'md.id')
            ->leftJoin('departamentos as dp', 'items.departamento_id', '=', 'dp.id')
            ->when($filtros['tipo'], function ($query, $val) {
                return $query->where('tp.nome','like','%'.$val.'%');
            })
            ->when($filtros['medida'], function ($query, $val) {
                return $query->where('md.tipo','like','%'.$val.'%');
            })
            ->when($filtros['departamento'], function ($query, $val) {
                return $query->where('dp.nome','like','%'.$val.'%');
            })
            ->when($filtros['nome'], function ($query, $val) {
                return $query->where('items.nome','like','%'.$val.'%');
            })
            ->paginate(10);

        $mensagem = $request->session()->get('mensagem');
        return view('cadaux.items.index', compact('data','mensagem','filtros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $tipo_items = TipoItem::query()->orderBy('nome')->get();
        $medidas = Medida::query()->orderBy('tipo')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.items.create',compact('mensagem','userDeptos','tipo_items','medidas'));
    }

    /**
     * Cadastra um item
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam tipo_item_id integer ID do tipo de item. Example: 1
     * @bodyParam medida_id integer ID da Medida. Example: 2
     * @bodyParam nome string required Nome. Example: tinta
     * @bodyParam descricao text nullable Descrição. Example: tinta
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "tipo_item_id": 1,
     *         "medida_id": 2,
     *         "nome": tinta,
     *         "descricao": "tinta"
     *     }
     * }
     */
    public function store(Request $request)
    {
        $item = new Item();
        $item->departamento_id = $request->input('departamento_id');
        $item->medida_id = $request->input('medida_id');
        $item->tipo_item_id = $request->input('tipo_item_id');
        $item->nome = $request->input('nome');
        $item->descricao = $request->input('descricao');

        if ($item->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new ItemResource($item);
            }

            $request->session()->flash('mensagem',"Item '{$item->nome}' (ID {$item->id}) criado com sucesso");
            return redirect()->route('cadaux-items');
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
     *         "tipo_item_id": 1,
     *         "medida_id": 2,
     *         "nome": tinta,
     *         "descricao": "tinta"
     *     }
     * }
     */
    public function show(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            return new ItemResource($item);
        }
        return view('cadaux.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $tipo_items = TipoItem::query()->orderBy('nome')->get();
        $medidas = Medida::query()->orderBy('tipo')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.items.edit', compact('item','mensagem','userDeptos','tipo_items','medidas'));
    }

    /**
     * Edita um Item
     * @authenticated
     *
     *
     * @urlParam id integer required ID do item que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam tipo_item_id integer ID do tipo de item. Example: 1
     * @bodyParam medida_id integer ID da Medida. Example: 2
     * @bodyParam nome string required Nome. Example: tinta
     * @bodyParam descricao text nullable Descrição. Example: tinta
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "tipo_item_id": 1,
     *         "medida_id": 2,
     *         "nome": tinta,
     *         "descricao": "tinta"
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->departamento_id = $request->input('departamento_id');
        $item->tipo_item_id = $request->input('tipo_item_id');
        $item->medida_id = $request->input('medida_id');
        $item->nome = $request->input('nome');
        $item->descricao = $request->input('descricao');

        if ($item->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new ItemResource($item);
            }

            $request->session()->flash('mensagem',"Item '{$item->nome}' (ID {$item->id}) editado com sucesso");
            return redirect()->route('cadaux-items');
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
     *         "tipo_item_id": 1,
     *         "medida_id": 2,
     *         "nome": tinta,
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
