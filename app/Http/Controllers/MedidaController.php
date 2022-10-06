<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MedidaFormRequest;
use App\Models\Medida;
use App\Http\Resources\Medida as MedidaResource;

/**
 * @group Medida
 *
 * APIs para listar, cadastrar, editar e remover dados de medidas
 */

class MedidaController extends Controller
{
    /**
     * Lista as medidas
     * @authenticated
     *
     */
    public function index(Request $request)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            $medidas = Medida::get();
            return MedidaResource::collection($medidas);
        }

        $medidas = Medida::query()->orderBy('id')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.medida', compact('medidas','mensagem'));
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
     * Cadastra uma medida
     * @authenticated
     *
     *
     * @bodyParam tipo string required Tipo. Example: peça
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "tipo": "peça"
     *     }
     * }
     */
    public function store(MedidaFormRequest $request)
    {
        $medida = new Medida();
        $medida->tipo = $request->input('tipo');

        if ($medida->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request) {
                return new MedidaResource($medida);
            }

            $request->session()->flash('mensagem',"Medida '{$medida->nome}' criada com sucesso, ID {$medida->id}.");
            return redirect()->route('cadaux-medidas');
        }
    }

    /**
     * Mostra uma medida
     * @authenticated
     *
     * @urlParam id integer required ID de medida. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "tipo": "peça"
     *     }
     * }
     */
    public function show($id)
    {
        $medida= Medida::findOrFail($id);
        return new MedidaResource($medida);
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
     * Edita uma medida
     * @authenticated
     *
     *
     * @urlParam id integer required ID da medida que deseja editar. Example: 1
     *
     * @bodyParam tipo string required Tipo. Example: peça
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "tipo": "peça"
     *     }
     * }
     */
    public function update(MedidaFormRequest $request, $id)
    {
        $medida = Medida::findOrFail($id);
        $medida->tipo = $request->input('tipo');

        if ($medida->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new MedidaResource($medida);
            }

            return response()->json(['mensagem' => "Medida '{$medida->nome}' - ID {$medida->id} editada com sucesso!"], 200);
        }
    }

    /**
     * Deleta uma medida
     * @authenticated
     *
     *
     * @urlParam id integer required ID da medida que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "medida deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "tipo": "peça"
     *     }
     * }
     */
    public function destroy($id)
    {
        $medida= Medida::findOrFail($id);

        if ($medida->delete()) {
            return response()->json([
                'message' => 'Medida deletada com sucesso!',
                'data' => new MedidaResource($medida)
            ]);
        }
    }
}
