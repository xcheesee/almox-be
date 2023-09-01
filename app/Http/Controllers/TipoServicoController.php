<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use Illuminate\Http\Request;
use App\Models\TipoServico;
use App\Http\Resources\TipoServico as TipoServicoResource;
use App\Http\Requests\TipoServicoFormRequest;

class TipoServicoController extends Controller
{
    /**
     * Lista os tipos de serviços
     * @authenticated
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "departamento_id": 3,
     *             "departamento": "CGPABI/DGPU",
     *             "servico": "alvenaria",
     *             "descricao": null
     *         },
     *         {
     *             "id": 2,
     *             "departamento_id": 3,
     *             "departamento": "CGPABI/DGPU",
     *             "servico": "carpintaria",
     *             "descricao": null
     *         }
     *      ]
     * }
     */
    public function index(Request $request)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            $user = auth()->user();
            $userDeptos = DepartamentoHelper::ids_deptos($user);
            $tipo_servicos = TipoServico::whereIn('departamento_id',$userDeptos)->get();
            return TipoServicoResource::collection($tipo_servicos);
        }

        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $tipo_servicos = TipoServico::query()->orderBy('id')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.tipo_servico', compact('tipo_servicos','mensagem', 'userDeptos'));
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
     * Cadastra um tipo de serviço
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer required ID do departamento. Example: 1
     * @bodyParam servico string required Nome do tipo de serviço. Example: carpintaria
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "servico": "carpintaria"
     *     }
     * }
     */
    public function store(TipoServicoFormRequest $request)
    {
        $tipo_servico = new TipoServico();
        $tipo_servico->departamento_id = $request->input('departamento');
        $tipo_servico->servico = $request->input('servico');

        if ($tipo_servico->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request) {
                return new TipoServicoResource($tipo_servico);
            }

            $request->session()->flash('mensagem',"Tipo de Serviço '{$tipo_servico->servico}' criada com sucesso, ID {$tipo_servico->id}.");
            return redirect()->route('cadaux-tipo_servicos');
        }
    }

    /**
     * Mostra um tipo de serviço
     * @authenticated
     *
     * @urlParam id integer required ID do tipo de serviço. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "servico": "carpintaria"
     *     }
     * }
     */
    public function show($id)
    {
        $tipo_servico= TipoServico::findOrFail($id);
        return new TipoServicoResource($tipo_servico);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoServico  $tipoServico
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoServico $tipoServico)
    {
        //
    }

    /**
     * Edita um tipo de serviço
     * @authenticated
     *
     *
     * @urlParam id integer required ID do tipo de serviço que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer required ID do departamento. Example: 1
     * @bodyParam servico string required Nome do tipo de serviço. Example: carpintaria
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "servico": "carpintaria"
     *     }
     * }
     */
    public function update(TipoServicoFormRequest $request, $id)
    {
        $tipo_servico = TipoServico::findOrFail($id);
        $tipo_servico->departamento_id = $request->input('departamento');
        $tipo_servico->servico = $request->input('servico');

        if ($tipo_servico->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new TipoServicoResource($tipo_servico);
            }

            return response()->json(['mensagem' => "Tipo de Serviço '{$tipo_servico->servico}' - ID {$tipo_servico->id} editado com sucesso!"], 200);
        }
    }

    /**
     * Deleta um tipo de serviço
     * @authenticated
     *
     *
     * @urlParam id integer required ID do tipo de serviço que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "Tipo de Serviço deletado com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "servico": "carpintaria"
     *     }
     * }
     */
    public function destroy($id)
    {
        $tipo_servico= TipoServico::findOrFail($id);

        if ($tipo_servico->delete()) {
            return response()->json([
                'message' => 'Tipo de Serviço deletado com sucesso!',
                'data' => new TipoServicoResource($tipo_servico)
            ]);
        }
    }

    /**
     * Irá retornar os serviços pelo Id do departamento.
     * 
     * @urlParam id integer required ID do departemento que deseja ver os serviços. Example: 1
     * 
     * @response 200 {
     *     "message": "Tipo de Serviço deletado com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 1,
     *         "servico": "carpintaria"
     *     }
     */
    public function ServicosPorDepto ($id)
    {
        $servicos = TipoServico::where('departamento_id', $id)
        ->select('id', 'servico')
        ->get();


        return response()->json([
            'mensagem' => "Serviços do departamento solicitado",
            'servicos' => $servicos
        ], 200);
    }
}
