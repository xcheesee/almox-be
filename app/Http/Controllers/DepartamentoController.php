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
    public function index(Request $request)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            $departamentos = Departamento::get();
            return DepartamentoResource::collection($departamentos);
        }

        $filtros = array();
        $filtros['andar'] = $request->query('f-andar');
        $filtros['nome'] = $request->query('f-nome');
        $filtros['ativo'] = $request->query('f-ativo');

        $data = Departamento::sortable()
            ->when($filtros['andar'], function ($query, $val) {
                return $query->where('andar','=',$val);
            })
            ->when($filtros['ativo'], function ($query, $val) {
                if ($val == 's'){
                    return $query->where('ativo','=',1);
                }elseif ($val == 'n'){
                    return $query->where('ativo','=',0);
                }
            })
            ->when($filtros['nome'], function ($query, $val) {
                return $query->where('nome','like','%'.$val.'%');
            })
            ->paginate(10);

        $mensagem = $request->session()->get('mensagem');
        return view('cadaux.departamentos.index', compact('data','mensagem','filtros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.departamentos.create',compact('mensagem'));
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
     *         "ativo": "1"
     *     }
     * }
     */
    public function store(Request $request)
    {
        $departamento = new Departamento();
        $departamento->nome = $request->input('nome');
        $departamento->andar = $request->input('andar');
        $departamento->ativo = $request->input('ativo') ? 1 : 0;

        if ($departamento->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new DepartamentoResource($departamento);
            }

            $request->session()->flash('mensagem',"Departamento '{$departamento->nome}' (ID {$departamento->id}) criado com sucesso");
            return redirect()->route('cadaux-departamentos');
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
     *         "ativo": "1"
     *     }
     * }
     */
    public function show(Request $request, int $id)
    {
        $departamento = Departamento::findOrFail($id);
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            return new DepartamentoResource($departamento);
        }
        return view('cadaux.departamentos.show', compact('departamento'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, int $id)
    {
        $departamento = Departamento::findOrFail($id);
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.departamentos.edit', compact('departamento','mensagem'));
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
     *         "ativo": "1"
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->nome = $request->input('nome');
        $departamento->andar = $request->input('andar');
        $departamento->ativo = $request->input('ativo') ? 1 : 0;

        if ($departamento->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new DepartamentoResource($departamento);
            }

            $request->session()->flash('mensagem',"Departamento '{$departamento->nome}' (ID {$departamento->id}) editado com sucesso");
            return redirect()->route('cadaux-departamentos');
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
     *         "ativo": "1"
     *     }
     * }
     */
    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);

        if ($departamento->delete()) {
            return response()->json([
                'message' => 'Departamento deletado com sucesso!',
                'data' => new DepartamentoResource($departamento)
            ]);
        }
    }
}
