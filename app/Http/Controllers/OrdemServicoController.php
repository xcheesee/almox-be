<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdemServico;
use App\Http\Resources\OrdemServico as OrdemServicoResource;

/**
 * @group Local
 *
 * APIs para listar, cadastrar, editar e remover dados de ordens de serviços
 */

class OrdemServicoController extends Controller
{
    /**
     * Lista as ordens de serviços
     * @authenticated
     *
     */
    public function index()
    {
        $ordem_servicos = OrdemServico::paginate(15);
            return OrdemServicoResource::collection($ordem_servicos);
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
     * Cadastra uma ordem de serviço
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam origem_id integer ID do Origem. Example: 1
     * @bodyParam destino_id integer ID do Destino. Example: 2
     * @bodyParam local_servico_id integer ID do Local do serviço. Example: 2
     * @bodyParam almoxarife_nome string required Nome do Almoxarife. Example: "João"
     * @bodyParam almoxarife_email string required E-mail do Almoxarife. Example: "joao@teste.com.br"
     * @bodyParam almoxarife_cargo string nullable Cargo do Almoxarife. Example: "Auxiliar de Almoxarifado"
     * @bodyParam data_servico datetime required Data do serviço. Example: "2022-08-11"
     * @bodyParam especificacao text nullable Especificação. Example: "reforma"
     * @bodyParam profissional string nullable Profissional. Example: "José"
     * @bodyParam horas_execucao integer nullable Horas de execução. Example: 10
     * @bodyParam observacoes text nullable Observações. Example: "observações referente ao serviço"
     * @bodyParam user_id integer required ID do usuário. Example: 1
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "origem_id": 1,
     *         "destino_id": 2,
     *         "local_servico_id": 2,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "almoxarife_cargo": "Auxiliar de Almoxarifado",
     *         "data_servico": "2022-08-11",
     *         "especificacao": "reforma",
     *         "profissional": "José",
     *         "horas_execucao": 10,
     *         "observacoes": "observações referente ao serviço",
     *         "user_id": 1
     *     }
     * }
     */
    public function store(Request $request)
    {
        $ordem_servico = new OrdemServico();
        $ordem_servico->departamento_id = $request->input('departamento_id');
        $ordem_servico->origem_id = $request->input('origem_id');
        $ordem_servico->destino_id = $request->input('destino_id');
        $ordem_servico->local_servico_id = $request->input('local_servico_id');
        $ordem_servico->almoxarife_nome = $request->input('almoxarife_nome');
        $ordem_servico->almoxarife_email = $request->input('almoxarife_email');
        $ordem_servico->almoxarife_cargo = $request->input('almoxarife_cargo');
        $ordem_servico->data_servico = $request->input('data_servico');
        $ordem_servico->especificacao = $request->input('especificacao');
        $ordem_servico->profissional = $request->input('profissional');
        $ordem_servico->horas_execucao = $request->input('horas_execucao');
        $ordem_servico->observacoes = $request->input('observacoes');
        $ordem_servico->user_id = $request->input('user_id');

        if ($ordem_servico->save()) {
            return new OrdemServicoResource($ordem_servico);
        }
    }

    /**
     * Mostra uma ordem de serviço
     * @authenticated
     *
     * @urlParam id integer required ID da ordem de serviço. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "origem_id": 1,
     *         "destino_id": 2,
     *         "local_servico_id": 2,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "almoxarife_cargo": "Auxiliar de Almoxarifado",
     *         "data_servico": "2022-08-11",
     *         "especificacao": "reforma",
     *         "profissional": "José",
     *         "horas_execucao": 10,
     *         "observacoes": "observações referente ao serviço",
     *         "user_id": 1
     *     }
     * }
     */
    public function show($id)
    {
        $ordem_servico= OrdemServico::findOrFail($id);
        return new OrdemServicoResource($ordem_servico);
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
     * Edita uma ordem de serviço
     * @authenticated
     *
     *
     * @urlParam id integer required ID da ordem de serviço que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam origem_id integer ID do Origem. Example: 1
     * @bodyParam destino_id integer ID do Destino. Example: 2
     * @bodyParam local_servico_id integer ID do Local do serviço. Example: 2
     * @bodyParam almoxarife_nome string required Nome do Almoxarife. Example: "João"
     * @bodyParam almoxarife_email string required E-mail do Almoxarife. Example: "joao@teste.com.br"
     * @bodyParam almoxarife_cargo string nullable Cargo do Almoxarife. Example: "Auxiliar de Almoxarifado"
     * @bodyParam data_servico datetime required Data do serviço. Example: "2022-08-11"
     * @bodyParam especificacao text nullable Especificação. Example: "reforma"
     * @bodyParam profissional string nullable Profissional. Example: "José"
     * @bodyParam horas_execucao integer nullable Horas de execução. Example: 10
     * @bodyParam observacoes text nullable Observações. Example: "observações referente ao serviço"
     * @bodyParam user_id integer required ID do usuário. Example: 1
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "origem_id": 1,
     *         "destino_id": 2,
     *         "local_servico_id": 2,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "almoxarife_cargo": "Auxiliar de Almoxarifado",
     *         "data_servico": "2022-08-11",
     *         "especificacao": "reforma",
     *         "profissional": "José",
     *         "horas_execucao": 10,
     *         "observacoes": "observações referente ao serviço",
     *         "user_id": 1
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $ordem_servico = Item::findOrFail($id);
        $ordem_servico->departamento_id = $request->input('departamento_id');
        $ordem_servico->origem_id = $request->input('origem_id');
        $ordem_servico->destino_id = $request->input('destino_id');
        $ordem_servico->local_servico_id = $request->input('local_servico_id');
        $ordem_servico->almoxarife_nome = $request->input('almoxarife_nome');
        $ordem_servico->almoxarife_email = $request->input('almoxarife_email');
        $ordem_servico->almoxarife_cargo = $request->input('almoxarife_cargo');
        $ordem_servico->data_servico = $request->input('data_servico');
        $ordem_servico->especificacao = $request->input('especificacao');
        $ordem_servico->profissional = $request->input('profissional');
        $ordem_servico->horas_execucao = $request->input('horas_execucao');
        $ordem_servico->observacoes = $request->input('observacoes');
        $ordem_servico->user_id = $request->input('user_id');

        if ($ordem_servico->save()) {
            return new OrdemServicoResource($ordem_servico);
        }
    }

    /**
     * Deleta uma ordem de serviço
     * @authenticated
     *
     *
     * @urlParam id integer required ID da ordem de serviço que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "Ordem de serviço deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "origem_id": 1,
     *         "destino_id": 2,
     *         "local_servico_id": 2,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "almoxarife_cargo": "Auxiliar de Almoxarifado",
     *         "data_servico": "2022-08-11",
     *         "especificacao": "reforma",
     *         "profissional": "José",
     *         "horas_execucao": 10,
     *         "observacoes": "observações referente ao serviço",
     *         "user_id": 1
     *     }
     * }
     */
    public function destroy($id)
    {
        $ordem_servico = OrdemServico::findOrFail($id);

        if ($ordem_servico->delete()) {
            return response()->json([
                'message' => 'Ordem de serviço deletada com sucesso!',
                'data' => new OrdemServicoResource($ordem_servico)
            ]);
        }
    }  
}
