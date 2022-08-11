<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Http\Resources\Departamento as DepartamentoResource;

/**
 * @group Departamento
 *
 * APIs para listar, cadastrar, editar e remover dados de departamento
 */

class DepartamentoController extends Controller
{
    /**
     * Lista os departamentos
     * @authenticated
     *
     */
    public function index()
    {
        $departamentos = Departamento::where('ativo', '=', true)->paginate(15);
            return DepartamentoResource::collection($departamentos);
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
     * Cadastra um novo departamento
     * @authenticated
     *
     *
     * @bodyParam nome string required Nome. Example: Teste LTDA
     * @bodyParam andar integer required Andar. Example: 5
     * @bodyParam ativo boolean required Ativo. Example: true
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nome": "Teste LTDA",
     *         "andar": "5",
     *         "ativo": "1",
     *     }
     * }
     */
    public function store(Request $request)
    {
        $departamento = new Departamento();
        $departamento->nome = $request->input('nome');
        $departamento->andar = $request->input('andar');
        $departamento->ativo = $request->input('ativo');

        if ($departamento->save()) {
            return new DepartamentoResource($departamento);
        }
    }

    /**
     * Mostra um departamento específico
     * @authenticated
     *
     *
     * @urlParam id integer required ID do departamento. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nome": "Teste LTDA",
     *         "andar": "5",
     *         "ativo": "1",
     *     }
     * }
     */
    public function show($id)
    {
        $departamento = Departamento::findOrFail($id);
        return new DepartamentoResource($departamento);
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
     * Edita um departamento
     * @authenticated
     *
     *
     * @urlParam id integer required ID do departamento que deseja editar. Example: 1
     *
     * @bodyParam nome string required Nome. Example: Teste LTDA
     * @bodyParam andar integer required Andar. Example: 5
     * @bodyParam ativo boolean required Ativo. Example: true
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nome": "Teste LTDA",
     *         "andar": "5",
     *         "ativo": "1",
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->nome = $request->input('nome');
        $departamento->andar = $request->input('andar');
        $departamento->ativo = $request->input('ativo');

        if ($departamento->save()) {
            return new DepartamentoResource($departamento);
        }
    }

    /**
     * Deleta um departamento
     * @authenticated
     *
     *
     * @urlParam id integer required ID do departamento que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "departamento deletado com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "nome": "Teste LTDA",
     *         "andar": "5",
     *         "ativo": "1",
     *     }
     * }
     */
    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);

        if ($departamento->delete()) {
            return response()->json([
                'message' => 'departamento deletado com sucesso!',
                'data' => new DepartamentoResource($departamento)
            ]);
        }
    } 
}