<?php

namespace App\Http\Controllers;

use App\Helpers\BasesUsuariosHelper;
use App\Helpers\DepartamentoHelper;
use Illuminate\Http\Request;
use App\Http\Requests\LocalFormRequest;
use App\Models\Local;
use App\Http\Resources\Local as LocalResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group Local
 *
 * APIs para listar, cadastrar, editar e remover dados de locais
 */

class LocalController extends Controller
{
    /**
     * Lista os locais
     * @authenticated
     *
     * @queryParam filter[tipo] Filtro de tipo de local (base, parque, autarquia, secretaria, subprefeitura). Example: base
     * @queryParam filter[nome] Filtro de nome do local Example: Base Leopoldina
     * @queryParam filter[cep] Filtro de CEP do local, separado por hífen. Example: 04103-000
     * @queryParam filter[departamento_id] Filtro do ID do departamento ao qual pertence o local. Example: 1234567
     *
     */
    public function index(Request $request)
    {
        $autenticado = $request->query('autenticado');

        if ($autenticado === "true") {
            $localUsers = BasesUsuariosHelper::ExibirBasesUsuarios(auth()->user()->id);
            return LocalResource::collection($localUsers);
        }

        $is_api_request = in_array('api', $request->route()->getAction('middleware'));
        if ($is_api_request) {
            $user = auth()->user();
            $userDeptos = DepartamentoHelper::ids_deptos($user);
            $locais = QueryBuilder::for(Local::class)
                //->whereIn('locais.departamento_id',$userDeptos)
                ->allowedFilters([
                    'departamento_id',
                    AllowedFilter::partial('nome'),
                    AllowedFilter::partial('tipo'),
                    AllowedFilter::partial('cep'),
                ])
                ->get();
            return LocalResource::collection($locais);
        }

        $filtros = array();
        $filtros['tipo'] = $request->query('f-tipo');
        $filtros['nome'] = $request->query('f-nome');
        $filtros['cep'] = $request->query('f-cep');
        $filtros['departamento'] = $request->query('f-departamento');

        $data = Local::sortable()
            ->select('locais.*')
            ->leftJoin('departamentos as dp', 'departamento_id', '=', 'dp.id')
            ->when($filtros['tipo'], function ($query, $val) {
                return $query->where('tipo', 'like', '%' . $val . '%');
            })
            ->when($filtros['cep'], function ($query, $val) {
                return $query->where('cep', 'like', '%' . $val . '%');
            })
            ->when($filtros['departamento'], function ($query, $val) {
                return $query->where('dp.nome', 'like', '%' . $val . '%');
            })
            ->when($filtros['nome'], function ($query, $val) {
                return $query->where('locais.nome', 'like', '%' . $val . '%');
            })
            ->paginate(10);

        $mensagem = $request->session()->get('mensagem');
        return view('cadaux.locais.index', compact('data', 'mensagem', 'filtros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user, 'nome');
        $mensagem = $request->session()->get('mensagem');
        $tipos = [
            'autarquia' => "Autarquia",
            'base' => "Base",
            'parque' => "Parque",
            'secretaria' => "Secretaria",
            'subprefeitura' => "Subprefeitura"
        ];
        return view('cadaux.locais.create', compact('mensagem', 'userDeptos', 'tipos'));
    }

    /**
     * Cadastra um local
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam nome string required Nome. Example: "teste"
     * @bodyParam tipo enum required ('base', 'parque', 'autarquia', 'secretaria', 'subprefeitura') Tipo. Example: base
     * @bodyParam cep string nullable Cep. Example: 12345-678
     * @bodyParam logradouro string nullable Logradouro. Example: Rua do Paraiso
     * @bodyParam numero string nullable Numero. Example: 387
     * @bodyParam bairro string nullable Bairro. Example: Paraiso
     * @bodyParam cidade string nullable Cidade. Example: São Paulo
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function store(LocalFormRequest $request)
    {
        $local = new Local();
        $local->departamento_id = $request->input('departamento_id');
        $local->nome = $request->input('nome');
        $local->tipo = $request->input('tipo');
        $local->cep = $request->input('cep');
        $local->logradouro = $request->input('logradouro');
        $local->numero = $request->input('numero');
        $local->bairro = $request->input('bairro');
        $local->cidade = $request->input('cidade');

        if ($local->save()) {
            $is_api_request = in_array('api', $request->route()->getAction('middleware'));
            if ($is_api_request) {
                return new LocalResource($local);
            }

            $request->session()->flash('mensagem', "Local '{$local->nome}' (ID {$local->id}) criado com sucesso");
            return redirect()->route('cadaux-locais');
        }
    }

    /**
     * Mostra um local
     * @authenticated
     *
     * @urlParam id integer required ID de local. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function show(Request $request, $id)
    {
        $local = Local::findOrFail($id);
        $is_api_request = in_array('api', $request->route()->getAction('middleware'));
        if ($is_api_request) {
            return new LocalResource($local);
        }
        return view('cadaux.locais.show', compact('local'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $local = Local::findOrFail($id);
        $user = auth()->user();
        $tipos = [
            'autarquia' => "Autarquia",
            'base' => "Base",
            'parque' => "Parque",
            'secretaria' => "Secretaria",
            'subprefeitura' => "Subprefeitura"
        ];
        $userDeptos = DepartamentoHelper::deptosByUser($user, 'nome');
        $mensagem = $request->session()->get('mensagem');
        return view('cadaux.locais.edit', compact('local', 'mensagem', 'userDeptos', 'tipos'));
    }

    /**
     * Edita um Local
     * @authenticated
     *
     *
     * @urlParam id integer required ID do local que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam nome string required Nome. Example: "teste"
     * @bodyParam tipo enum required ('base', 'parque', 'autarquia', 'secretaria', 'subprefeitura') Tipo. Example: base
     * @bodyParam cep string nullable Cep. Example: 12345-678
     * @bodyParam logradouro string nullable Logradouro. Example: Rua do Paraiso
     * @bodyParam numero string nullable Numero. Example: 387
     * @bodyParam bairro string nullable Bairro. Example: Paraiso
     * @bodyParam cidade string nullable Cidade. Example: São Paulo
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function update(LocalFormRequest $request, $id)
    {
        $local = Local::findOrFail($id);
        $local->departamento_id = $request->input('departamento_id');
        $local->nome = $request->input('nome');
        $local->tipo = $request->input('tipo');
        $local->cep = $request->input('cep');
        $local->logradouro = $request->input('logradouro');
        $local->numero = $request->input('numero');
        $local->bairro = $request->input('bairro');
        $local->cidade = $request->input('cidade');

        if ($local->save()) {
            $is_api_request = in_array('api', $request->route()->getAction('middleware'));
            if ($is_api_request) {
                return new LocalResource($local);
            }

            $request->session()->flash('mensagem', "Local '{$local->nome}' (ID {$local->id}) editado com sucesso");
            return redirect()->route('cadaux-locais');
        }
    }

    /**
     * Deleta um local
     * @authenticated
     *
     *
     * @urlParam id integer required ID do local que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "item deletado com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function destroy($id)
    {
        $local = Local::findOrFail($id);

        if ($local->delete()) {
            return response()->json([
                'message' => 'Local deletado com sucesso!',
                'data' => new LocalResource($local)
            ]);
        }
    }

    public function filtrar_dpt(int $id)
    {
        if (empty($id)) {
            return response()->json(['mensagem' => 'Departamento é obrigatório'], 400);
        }

        $locais = Local::query()->where('departamento_id', $id)->orderBy('nome')->get();
        return response()->json(['locais' => $locais], 200);
    }
}
