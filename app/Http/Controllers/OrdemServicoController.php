<?php

namespace App\Http\Controllers;

use App\Helpers\HtmlHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrdemServicoFormRequest;
use App\Models\OrdemServico;
use App\Models\Inventario;
use App\Models\OrdemServicoItem;
use App\Http\Resources\OrdemServico as OrdemServicoResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group OrdemServico
 *
 * APIs para listar, cadastrar, editar e remover dados de ordens de serviços
 */

class OrdemServicoController extends Controller
{
    /**
     * Lista as ordens de serviços
     * @authenticated
     *
     * @queryParam filter[origem] Filtro de Local (base) do item. Example: Leopoldina
     * @queryParam filter[local_servico] Filtro de nome do local de destino dos materiais. Example: Ibirapuera
     * @queryParam filter[almoxarife_nome] Filtro do Nome do almoxarife responsável. Example: Fulano
     * @queryParam filter[almoxarife_email] Filtro do e-mail do almoxarife responsável. Example: fulano@mail.com
     * @queryParam filter[servico_depois_de] Filtro inicial de período da data de serviço. Example: 2023-01-01
     * @queryParam filter[servico_antes_de] Filtro final de período da data de serviço. Example: 2023-12-31
     * @queryParam sort Campo a ser ordenado (padrão ascendente, inserir um hífen antes para decrescente). Colunas possíveis: 'id', 'items.nome', 'tipo_items.nome', 'medidas.tipo', 'locais.nome', 'quantidade' Example: -locais.nome
     *
     */
    public function index()
    {
        $ordem_servicos = QueryBuilder::for(OrdemServico::class)
        ->select('locais.nome', 'origem.nome', 'ordem_servicos.*')
        ->leftJoin('locais as origem', 'origem.id', 'ordem_servicos.origem_id')
        ->leftJoin('locais', 'locais.id', 'ordem_servicos.local_servico_id')
        ->allowedFilters([
                AllowedFilter::partial('origem','origem.nome'), AllowedFilter::partial('local_servico','locais.nome'),
                'almoxarife_nome', 'almoxarife_email',
                AllowedFilter::scope('servico_depois_de'),
                AllowedFilter::scope('servico_antes_de'),
            ])
        ->allowedSorts('id', 'data_servico', 'origem.nome', 'locais.nome')
        ->paginate(15);
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
     * @bodyParam local_servico_id integer ID do Local do serviço. Example: 2
     * @bodyParam data_inicio_servico datetime required Data do serviço. Example: 2022-08-30T14:48
     * @bodyParam data_fim_servico datetime required Data do serviço. Example: 2022-08-31T17:50
     * @bodyParam almoxarife_nome string required Nome do Almoxarife. Example: João
     * @bodyParam almoxarife_email string required E-mail do Almoxarife. Example: joao@teste.com.br
     * @bodyParam especificacao text nullable Especificação. Example: reforma
     * @bodyParam profissional string nullable Profissional. Example: José
     * @bodyParam horas_execucao integer nullable Horas de execução. Example: 10
     * @bodyParam observacoes text nullable Observações. Example: observações referente ao serviço
     * @bodyParam user_id integer required ID do usuário. Example: 1
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "origem_id": 1,
     *         "local_servico_id": 2,
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "especificacao": "reforma",
     *         "profissional": "José",
     *         "horas_execucao": 10,
     *         "observacoes": "observações referente ao serviço",
     *         "user_id": 1,
     *         "entrada_items": [
     *             {
     *                 "id": 1,
     *                 "quantidade": 500
     *             },
     *             {
     *                 "id": 2,
     *                 "quantidade": 480
     *             }
     *         ]
     *     }
     * }
     */
    public function store(OrdemServicoFormRequest $request)
    {
        $ordem_servico = new OrdemServico();
        $ordem_servico->departamento_id = $request->input('departamento_id');
        $ordem_servico->origem_id = $request->input('origem_id');
        $ordem_servico->local_servico_id = $request->input('local_servico_id');
        $ordem_servico->data_inicio_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_inicio_servico'));
        $ordem_servico->data_fim_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_fim_servico'));
        $ordem_servico->almoxarife_nome = $request->input('almoxarife_nome');
        $ordem_servico->almoxarife_email = $request->input('almoxarife_email');
        $ordem_servico->especificacao = $request->input('especificacao');
        $ordem_servico->profissional = $request->input('profissional');
        $ordem_servico->horas_execucao = $request->input('horas_execucao');
        $ordem_servico->observacoes = $request->input('observacoes');
        $ordem_servico->user_id = $request->input('user_id');

        if ($ordem_servico->save()) {

            $ordem_servico_items = OrdemServicoItem::query()->where('ordem_servico_id','=',$ordem_servico->id)->get();

            foreach ($ordem_servico_items as $ordem_servico_item) {
                $saida_inventario = Inventario::query()->where('local_id','=',$ordem_servico->origem_id)
                                                    ->where('departamento_id','=',$ordem_servico->departamento_id)
                                                    ->where('item_id','=',$ordem_servico_item->item_id)->first();

                if ($saida_inventario) {
                    $saida_inventario->quantidade -= $ordem_servico_item->quantidade;
                    $saida_inventario->save();
                }
            }

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
     *         "local_servico_id": 2,
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
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
     * @bodyParam local_servico_id integer ID do Local do serviço. Example: 2
     * @bodyParam data_inicio_servico datetime required Data do serviço. Example: 2022-08-30T14:48
     * @bodyParam data_fim_servico datetime required Data do serviço. Example: 2022-08-31T17:50
     * @bodyParam almoxarife_nome string required Nome do Almoxarife. Example: João
     * @bodyParam almoxarife_email string required E-mail do Almoxarife. Example: joao@teste.com.br
     * @bodyParam especificacao text nullable Especificação. Example: reforma
     * @bodyParam profissional string nullable Profissional. Example: José
     * @bodyParam horas_execucao integer nullable Horas de execução. Example: 10
     * @bodyParam observacoes text nullable Observações. Example: observações referente ao serviço
     * @bodyParam user_id integer required ID do usuário. Example: 1
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "origem_id": 1,
     *         "local_servico_id": 2,
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
     *         "especificacao": "reforma",
     *         "profissional": "José",
     *         "horas_execucao": 10,
     *         "observacoes": "observações referente ao serviço",
     *         "user_id": 1
     *     }
     * }
     */
    public function update(OrdemServicoFormRequest $request, $id)
    {
        $ordem_servico = OrdemServico::findOrFail($id);
        $ordem_servico->departamento_id = $request->input('departamento_id');
        $ordem_servico->origem_id = $request->input('origem_id');
        $ordem_servico->local_servico_id = $request->input('local_servico_id');
        $ordem_servico->data_inicio_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_inicio_servico'));
        $ordem_servico->data_fim_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_fim_servico'));
        $ordem_servico->almoxarife_nome = $request->input('almoxarife_nome');
        $ordem_servico->almoxarife_email = $request->input('almoxarife_email');
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
     *         "local_servico_id": 2,
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "almoxarife_nome": "João",
     *         "almoxarife_email": "joao@teste.com.br",
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
