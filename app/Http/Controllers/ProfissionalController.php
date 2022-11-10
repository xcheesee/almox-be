<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use App\Models\Profissional;
use App\Http\Requests\ProfissionalFormRequest;
use App\Http\Resources\Profissional as ProfissionalResource;
use App\Models\Local;
use Illuminate\Http\Request;

/**
 * @group Profissional
 *
 * APIs para listar, cadastrar, editar e remover dados de profissionais
 */

class ProfissionalController extends Controller
{
    /**
     * Lista os profissionais
     * @authenticated
     *
     */
    public function index(Request $request)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            $profissionais = Profissional::get();
            return ProfissionalResource::collection($profissionais);
        }

        $filtros = array();
        $filtros['nome'] = $request->query('f-nome');
        $filtros['departamento'] = $request->query('f-departamento');
        $filtros['local'] = $request->query('f-local');
        $filtros['profissao'] = $request->query('f-profissao');

        $data = Profissional::sortable()
            ->select('profissionais.*', 'dp.nome as departamento_nome', 'lc.nome as local_nome')
            ->leftJoin('locais as lc', 'local_id', '=', 'lc.id')
            ->leftJoin('departamentos as dp', 'profissionais.departamento_id', '=', 'dp.id')
            ->when($filtros['profissao'], function ($query, $val) {
                return $query->where('profissao','like','%'.$val.'%');
            })
            ->when($filtros['local'], function ($query, $val) {
                return $query->where('lc.tipo','like','%'.$val.'%');
            })
            ->when($filtros['departamento'], function ($query, $val) {
                return $query->where('dp.nome','like','%'.$val.'%');
            })
            ->when($filtros['nome'], function ($query, $val) {
                return $query->where('profissionais.nome','like','%'.$val.'%');
            })
            ->paginate(10);

        $mensagem = $request->session()->get('mensagem');
        return view('cadaux.profissionais.index', compact('data','mensagem','filtros'));
    }

    /**
     * Lista os profissionais de acordo com o local e departamento especificados
     * @authenticated
     *
     * @queryParam local ID do local. Example: 2
     * @queryParam depto ID do departamento. Example: 3
     *
     */
    public function profissionais_local(Request $request)
    {
        // $user = auth()->user();
        // $userDeptos = DepartamentoHelper::ids_deptos($user);
        $local_id = $request->query('local') ? $request->query('local') : null;
        $departamento_id = $request->query('depto') ? $request->query('depto') : null;

        $profissionais = Profissional::query()
            ->when($local_id, function ($query, $val) {
                return $query->where('local_id','=',$val);
            })
            ->when($departamento_id, function ($query, $val) {
                return $query->where('departamento_id','=',$val);
            })
            ->get();

        return ProfissionalResource::collection($profissionais);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $locais = Local::query()->orderBy('nome')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.profissionais.create',compact('mensagem','userDeptos','locais'));
    }

    /**
     * Cadastra uma profissional
     * @authenticated
     *
     *
     * @bodyParam nome string required Nome do profissional. Example: João Pedro Silva
     * @bodyParam profissao string required Profissão/Cargo. Example: Arquiteto
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nome": "João Pedro Silva",
     *         "profissao": "Arquiteto",
     *         "completo": "João Pedro Silva (Arquiteto)"
     *     }
     * }
     */
    public function store(ProfissionalFormRequest $request)
    {
        $profissional = new Profissional();
        $profissional->departamento_id = $request->input('departamento_id');
        $profissional->local_id = $request->input('local_id');
        $profissional->nome = $request->input('nome');
        $profissional->profissao = $request->input('profissao');

        if ($profissional->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request) {
                return new ProfissionalResource($profissional);
            }

            $request->session()->flash('mensagem',"Profissional '{$profissional->nome}' criado(a) com sucesso, ID {$profissional->id}.");
            return redirect()->route('cadaux-profissionais');
        }
    }

    /**
     * Mostra uma profissional
     * @authenticated
     *
     * @urlParam id integer required ID de profissional. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nome": "João Pedro Silva",
     *         "profissao": "Arquiteto",
     *         "completo": "João Pedro Silva (Arquiteto)"
     *     }
     * }
     */
    public function show(Request $request, $id)
    {
        $profissional= Profissional::findOrFail($id);
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            return new ProfissionalResource($profissional);
        }
        return view('cadaux.profissionais.show', compact('profissional'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
        $profissional = Profissional::findOrFail($id);
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $locais = Local::query()->where('departamento_id','=',$profissional->departamento_id)->orderBy('nome')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.profissionais.edit',compact('mensagem','profissional','userDeptos','locais'));
    }

    /**
     * Edita uma profissional
     * @authenticated
     *
     *
     * @urlParam id integer required ID da profissional que deseja editar. Example: 1
     *
     * @bodyParam profissao string required Tipo. Example: peça
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nome": "João Pedro Silva",
     *         "profissao": "Arquiteto",
     *         "completo": "João Pedro Silva (Arquiteto)"
     *     }
     * }
     */
    public function update(ProfissionalFormRequest $request, $id)
    {
        $profissional = Profissional::findOrFail($id);
        $profissional->departamento_id = $request->input('departamento_id');
        $profissional->local_id = $request->input('local_id');
        $profissional->nome = $request->input('nome');
        $profissional->profissao = $request->input('profissao');

        if ($profissional->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new ProfissionalResource($profissional);
            }

            $request->session()->flash('mensagem',"Profissional '{$profissional->nome}' - ID {$profissional->id} editado(a) com sucesso!");
            return redirect()->route('cadaux-profissionais');
        }
    }

    /**
     * Deleta uma profissional
     * @authenticated
     *
     *
     * @urlParam id integer required ID da profissional que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "profissional deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "nome": "João Pedro Silva",
     *         "profissao": "Arquiteto",
     *         "completo": "João Pedro Silva (Arquiteto)"
     *     }
     * }
     */
    public function destroy($id)
    {
        $profissional= Profissional::findOrFail($id);

        if ($profissional->delete()) {
            return response()->json([
                'message' => 'Profissional deletado(a) com sucesso!',
                'data' => new ProfissionalResource($profissional)
            ]);
        }
    }
}
