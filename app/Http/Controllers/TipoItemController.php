<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use Illuminate\Http\Request;
use App\Models\TipoItem;
use App\Http\Resources\TipoItem as TipoItemResource;

/**
 * @group TipoItem
 *
 * APIs para listar, cadastrar, editar e remover dados de tipo_items
 */

class TipoItemController extends Controller
{
    /**
     * Lista os tipos de itens
     * @authenticated
     *
     */
    public function index(Request $request)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            $tipo_items = TipoItem::get();
            return TipoItemResource::collection($tipo_items);
        }

        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $tipo_items = TipoItem::query()->orderBy('id')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.tipo_item', compact('tipo_items','mensagem', 'userDeptos'));
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
     * Cadastra um tipo de item
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer required ID do departamento. Example: 1
     * @bodyParam nome string required Nome do tipo de item. Example: carpintaria
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "nome": "carpintaria"
     *     }
     * }
     */
    public function store(Request $request)
    {
        $tipo_item = new TipoItem();
        $tipo_item->departamento_id = $request->input('departamento');
        $tipo_item->nome = $request->input('nome');

        if ($tipo_item->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request) {
                return new TipoItemResource($tipo_item);
            }

            $request->session()->flash('mensagem',"Tipo de Item '{$tipo_item->nome}' criada com sucesso, ID {$tipo_item->id}.");
            return redirect()->route('cadaux-tipo_items');
        }
    }

    /**
     * Mostra um tipo de item
     * @authenticated
     *
     * @urlParam id integer required ID do tipo de item. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "nome": "carpintaria"
     *     }
     * }
     */
    public function show($id)
    {
        $tipo_item= TipoItem::findOrFail($id);
        return new TipoItemResource($tipo_item);
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
     * Edita um tipo de item
     * @authenticated
     *
     *
     * @urlParam id integer required ID do tipo de item que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer required ID do departamento. Example: 1
     * @bodyParam nome string required Nome do tipo de item. Example: carpintaria
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "nome": "carpintaria"
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $tipo_item = TipoItem::findOrFail($id);
        $tipo_item->departamento_id = $request->input('departamento');
        $tipo_item->nome = $request->input('nome');

        if ($tipo_item->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new TipoItemResource($tipo_item);
            }

            return response()->json(['mensagem' => "Tipo de Item '{$tipo_item->nome}' - ID {$tipo_item->id} editado com sucesso!"], 200);
        }
    }

    /**
     * Deleta um tipo de item
     * @authenticated
     *
     *
     * @urlParam id integer required ID do tipo de item que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "Tipo de Item deletado com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "nome": "carpintaria"
     *     }
     * }
     */
    public function destroy($id)
    {
        $tipo_item= TipoItem::findOrFail($id);

        if ($tipo_item->delete()) {
            return response()->json([
                'message' => 'Tipo de Item deletado com sucesso!',
                'data' => new TipoItemResource($tipo_item)
            ]);
        }
    }
}
