<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use App\Models\Historico;
use App\Http\Resources\Historico as HistoricoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HistoricoController extends Controller
{
    //
    public function index_web(Request $request)
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);
        $filtros = array();
        $filtros['depois_de'] = $request->query('f-depois_de');
        $filtros['antes_de'] = $request->query('f-antes_de');

        $historicos = Historico::query()
        ->select('users.name', 'historicos.*')
        ->leftJoin('users', 'users.id', 'historicos.user_id')
        // ->whereIn('historicos.departamento_id',$userDeptos)
        ->when($filtros['depois_de'], function ($query, $val) {
            $date = Carbon::createFromFormat('d/m/Y', $val);
            $data = $date->format("Y-m-d");
            return $query->where('historicos.data_acao','>=',$data);
        })
        ->when($filtros['antes_de'], function ($query, $val) {
            $date = Carbon::createFromFormat('d/m/Y', $val);
            $data = $date->format("Y-m-d");
            return $query->where('historicos.data_acao','<=',$data);
        })
        ->sortable()
        ->paginate(15);

        // $tipo_items = TipoItem::whereIn('departamento_id',$userDeptos)->get();

        $mensagem = $request->session()->get('mensagem');
        return view('historico.index', compact('historicos','mensagem','filtros'));
    }

    /**
     * Mostra um registro de histórico específica
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "data_acao": "2022-08-11",
     *         "nome_tabela": "entradas",
     *         "tipo_acao": "criação",
     *         "user_id": "1"
     *     }
     * }
     */
    public function show(Request $request, $id)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        $historico = Historico::findOrFail($id);

        if($is_api_request){
            return new HistoricoResource($historico);
        }

        return response()->json(
            ['historico' => new HistoricoResource($historico)]
            , 200);
    }
}
