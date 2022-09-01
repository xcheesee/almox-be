<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SaidaFormRequest;
use App\Models\Saida;
use App\Http\Resources\Saida as SaidaResource;

/**
 * @group Saida
 *
 * APIs para listar, cadastrar, editar e remover dados de saidas.
 */

class SaidaController extends Controller
{
    /**
     * Lista as saidas
     * @authenticated
     *
     */
    public function index()
    {
        $saidas = Saida::paginate(15);
            return SaidaResource::collection($saidas);
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
     * Cadastra uma saida
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam ordem_servico_id integer ID da Ordem de serviço. Example: 1
     * @bodyParam almoxarife_nome string required Nome do Almoxarife. Example: "João"
     * @bodyParam almoxarife_email string required E-mail do Almoxarife. Example: "joao@teste.com.br"
     * @bodyParam baixa_user_id integer ID do usuario. Example: 1
     * @bodyParam baixa_datahora datetime required Data e hora da baixa. Example: "2022-08-12 08:59"
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "ordem_servico_id": 1,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "baixa_user_id": 1,
     *         "baixa_datahora": "2022-08-12 08:59"
     *     }
     * }
     */
    public function store(SaidaFormRequest $request)
    {
        $saida = new Saida();
        $saida->departamento_id = $request->input('departamento_id');
        $saida->ordem_servico_id = $request->input('ordem_servico_id');
        $saida->almoxarife_nome = $request->input('almoxarife_nome');
        $saida->almoxarife_email = $request->input('almoxarife_email');
        $saida->baixa_user_id = $request->input('baixa_user_id');
        $saida->baixa_datahora = $request->input('baixa_datahora');

        if ($saida->save()) {
            return new SaidaResource($saida);
        }
    }

    /**
     * Mostra uma saida
     * @authenticated
     *
     * @urlParam id integer required ID da saida. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "ordem_servico_id": 1,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "baixa_user_id": 1,
     *         "baixa_datahora": "2022-08-12 08:59"
     *     }
     * }
     */
    public function show($id)
    {
        $saida= Saida::findOrFail($id);
        return new SaidaResource($saida);
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
     * Edita uma saida
     * @authenticated
     *
     *
     * @urlParam id integer required ID da ordem de serviço que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam ordem_servico_id integer ID da Ordem de serviço. Example: 1
     * @bodyParam almoxarife_nome string required Nome do Almoxarife. Example: "João"
     * @bodyParam almoxarife_email string required E-mail do Almoxarife. Example: "joao@teste.com.br"
     * @bodyParam baixa_user_id integer ID do usuario. Example: 1
     * @bodyParam baixa_datahora datetime required Data e hora da baixa. Example: "2022-08-12 08:59"
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "ordem_servico_id": 1,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "baixa_user_id": 1,
     *         "baixa_datahora": "2022-08-12 08:59"
     *     }
     * }
     */
    public function update(SaidaFormRequest $request, $id)
    {
        $saida = Saida::findOrFail($id);
        $saida->departamento_id = $request->input('departamento_id');
        $saida->ordem_servico_id = $request->input('ordem_servico_id');
        $saida->almoxarife_nome = $request->input('almoxarife_nome');
        $saida->almoxarife_email = $request->input('almoxarife_email');
        $saida->baixa_user_id = $request->input('baixa_user_id');
        $saida->baixa_datahora = $request->input('baixa_datahora');

        if ($saida->save()) {
            return new SaidaResource($saida);
        }
    }

    /**
     * Deleta uma saida
     * @authenticated
     *
     *
     * @urlParam id integer required ID da saida que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "Saida deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "ordem_servico_id": 1,
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "baixa_user_id": 1,
     *         "baixa_datahora": "2022-08-12 08:59"
     *     }
     * }
     */
    public function destroy($id)
    {
        $saida = Saida::findOrFail($id);

        if ($saida->delete()) {
            return response()->json([
                'message' => 'Saida deletada com sucesso!',
                'data' => new SaidaResource($saida)
            ]);
        }
    }
}
