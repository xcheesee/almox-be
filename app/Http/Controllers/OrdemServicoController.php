<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use App\Helpers\HtmlHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrdemServicoFormRequest;
use App\Models\OrdemServico;
use App\Models\Inventario;
use App\Models\OrdemServicoItem;
use App\Http\Resources\OrdemServico as OrdemServicoResource;
use App\Models\Saida;
use App\Models\SaidaItem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);

        $ordem_servicos = QueryBuilder::for(OrdemServico::class)
        ->select('locais.nome', 'origem.nome', 'ordem_servicos.*')
        ->leftJoin('locais as origem', 'origem.id', 'ordem_servicos.origem_id')
        ->leftJoin('locais', 'locais.id', 'ordem_servicos.local_servico_id')
        ->whereIn('ordem_servicos.departamento_id',$userDeptos)
        ->where('ordem_servicos.ativo','=',1)
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
     *         "observacoes": "observações referente ao serviço",,
     *         "flg_baixa": 0,
     *         "user_id": 1,
     *         "ordem_servico_items": [
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
        $ordem_servico->user_id = Auth::user()->id;

        if ($ordem_servico->save()) {
            // Lidando com os itens adicionados
            $ordemServicoItens = $request->input('ordem_servico_items');
            if ($ordemServicoItens){
                foreach ($ordemServicoItens as $ordem_servico_items){
                    //Salvando itens na tabela ordem_servico_items
                    $ordem_servico_item = new OrdemServicoItem();
                    $ordem_servico_item->ordem_servico_id = $ordem_servico->id;
                    $ordem_servico_item->item_id = $ordem_servico_items["id"];
                    $ordem_servico_item->quantidade = $ordem_servico_items["quantidade"];
                    $ordem_servico_item->save();

                    //lógica para retirar a quantidade dos itens no inventario
                    $inventario = Inventario::query()->where('local_id','=',$ordem_servico->origem_id)
                                                        ->where('departamento_id','=',$ordem_servico->departamento_id)
                                                        ->where('item_id','=',$ordem_servico_items["item_id"])->first();

                    if ($inventario) {
                        $inventario->quantidade -= $ordem_servico_items["quantidade"];
                        $resultado = $inventario->quantidade;
                            if ($resultado - 0) {
                                $erroQtd = response()->json(['error' => 'Quantidade usada não pode exceder a quantidade em estoque.']);
                                return $erroQtd;
                            } else {
                                $inventario->save();
                            }
                    }
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
     *         "flg_baixa": 0,
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
     *         "flg_baixa": 0,
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
        $ordem_servico->user_id = Auth::user()->id;

        if ($ordem_servico->save()) {
            // Lidando com os itens adicionados
            $ordemServicoItens = $request->input('ordem_servico_items');
            if ($ordemServicoItens){
                foreach ($ordemServicoItens as $ordem_servico_items){
                    // Atualizando item na tabela ordem_servico_items
                    $ordem_servico_item = OrdemServicoItem::query()->where('item_id','=',$ordem_servico_items["item_id"])->first();

                    if ($ordem_servico_item) {
                        $ordem_servico_item->ordem_servico_id = $ordem_servico->id;
                        $ordem_servico_item->item_id = $ordem_servico_items["item_id"];
                        $ordem_servico_item->quantidade = $ordem_servico_items["quantidade"];
                        $ordem_servico_item->save();
                    } else {
                        // Criando item na tabela ordem_servico_items
                        $ordem_servico_item = new OrdemServicoItem();
                        $ordem_servico_item->ordem_servico_id = $ordem_servico->id;
                        $ordem_servico_item->item_id = $ordem_servico_items["item_id"];
                        $ordem_servico_item->quantidade = $ordem_servico_items["quantidade"];
                        $ordem_servico_item->save();
                    }

                    // lógica para retirar a quantidade dos itens no inventario
                    $inventario = Inventario::query()->where('local_id','=',$ordem_servico->origem_id)
                                                        ->where('departamento_id','=',$ordem_servico->departamento_id)
                                                        ->where('item_id','=',$ordem_servico_items["item_id"])
                                                        ->first();

                    if ($inventario) {
                        $inventario->quantidade = $ordem_servico_items["quantidade"];
                        $resultado = $inventario->quantidade;
                        if ($resultado - 0) {
                            $erroQtd = response()->json(['error' => 'Quantidade usada não pode exceder a quantidade em estoque.']);
                            return $erroQtd;
                        } else {
                            $inventario->save();
                        }
                    }
                }
            }

            return new OrdemServicoResource($ordem_servico);
        }
    }

    /**
     * Emite a Baixa de uma ordem de serviço
     * @authenticated
     *
     *
     * @urlParam id integer required ID da ordem de serviço que deseja editar. Example: 1
     *
     * @bodyParam ordem_servico_items object[] required Itens da ordem de serviço. Example: [{"id": 2, "enviado": 60, "usado": 50, "retorno": 10},{"id": 3, "enviado": 5, "usado": 3, "retorno": 2}]
     * @bodyParam ordem_servico_items.id integer required ID do item. Example: 2
     * @bodyParam ordem_servico_items.enviado integer required Quantidade enviada do item para o local de serviço. Example: 60
     * @bodyParam ordem_servico_items.usado integer required Quantidade usada do item para o serviço. Example: 50
     * @bodyParam ordem_servico_items.retorno integer required Quantidade a ser devolvida do item para a base de origem. Example: 10
     *
     *
     * @response 200 {
     *     "message": "Baixa da Ordem de serviço efetuada com sucesso!",
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
     *         "flg_baixa": 1,
     *         "user_id": 1
     *     }
     * }
     */
    public function baixa(Request $request, $id){
        $ordem_servico = OrdemServico::findOrFail($id);

        //TODO: setar uma flag na ordem indicando que a baixa foi dada

        $ordemServicoItens = $request->input('ordem_servico_items');
        if($ordemServicoItens){
            //salvando a baixa na BD
            DB::beginTransaction();
            $saida = new Saida();
            $saida->departamento_id = $ordem_servico->departamento_id;
            $saida->ordem_servico_id = $ordem_servico->id;
            $saida->baixa_datahora = date('Y-m-d H:i:s');
            $saida->baixa_user_id = auth()->user()->id;
            if ($saida->save()) {
                foreach ($ordemServicoItens as $ordem_servico_items){
                    //Salvando itens na tabela ordem_servico_items
                    $saida_item = new SaidaItem();
                    $saida_item->saida_id = $saida->id;
                    $saida_item->item_id = $ordem_servico_items["id"];
                    $saida_item->enviado = $ordem_servico_items["enviado"];
                    $saida_item->usado = $ordem_servico_items["usado"];
                    $saida_item->retorno = $ordem_servico_items["retorno"];
                    $saida_item->save();

                    //lógica para retirar a quantidade dos itens no inventario
                    $saida_inventario = Inventario::query()->where('local_id','=',$ordem_servico->origem_id)
                                                        ->where('departamento_id','=',$ordem_servico->departamento_id)
                                                        ->where('item_id','=',$ordem_servico_items["id"])->first();

                    if ($saida_inventario) {
                        $saida_inventario->quantidade += $ordem_servico_items["retorno"];
                        $saida_inventario->save();
                    }
                }

                $ordem_servico->flg_baixa = true;
                $ordem_servico->save();

                DB::commit();
            }else{
                DB::rollBack();
                return response()->json([
                    'message' => 'Erro ao registrar a base, tente novamente mais tarde.'
                ], 410);
            }
        }else{
            return response()->json([
                'message' => 'Para dar baixa, é preciso enviar a lista de itens e seus valores de usado e retornado.'
            ], 410);
        }

        return response()->json([
            'message' => 'Baixa da Ordem de serviço efetuada com sucesso!',
            'data' => new OrdemServicoResource($ordem_servico)
        ]);
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

        /**
         * O seguinte código é para remoção "lógica", apenas setamos o ativo=0 e reduzimos a quantidade do item no inventário
         */
        if ($ordem_servico->ativo == 0){
            return response()->json([
                'message' => 'Ordem de serviço inativa na base de dados do sistema; a mesma foi removida anteriormente.'
            ], 410);
        }
        $ordem_servico->ativo = 0;
        $ordem_servico->save();

        $ordem_servico_items = OrdemServicoItem::where('ordem_servico_id','=',$id)->get();
        foreach($ordem_servico_items as $item){
            //lógica para devolver a quantidade dos itens no inventario
            $entrada_inventario = Inventario::query()->where('local_id','=',$ordem_servico->origem_id)
                ->where('departamento_id','=',$ordem_servico->departamento_id)
                ->where('item_id','=',$item->item_id)->first();

            if ($entrada_inventario) {
                $entrada_inventario->quantidade += $item->quantidade;
                $entrada_inventario->save();
            }
        }

        return response()->json([
            'message' => 'Ordem de serviço deletada com sucesso! Items referentes à ordem de serviço foram removidos do inventário',
            'data' => new OrdemServicoResource($ordem_servico)
        ]);

        // if ($ordem_servico->delete()) {
        //     return response()->json([
        //         'message' => 'Ordem de serviço deletada com sucesso!',
        //         'data' => new OrdemServicoResource($ordem_servico)
        //     ]);
        // }
    }

    /**
     * Gera um arquivo PDF da baixa efetuada na ordem de serviço
     * @authenticated
     *
     * @header Accept application/pdf
     *
     * @urlParam id integer required ID da ordem de serviço que deseja deletar. Example: 1
     *
     */
    public function baixa_pdf($id){
        $pdf = App::make('dompdf.wrapper');
        $ordem = OrdemServico::findOrFail($id);
        $saida = Saida::query()->where('ordem_servico_id','=',$id)->first();
        $saida_items = SaidaItem::query()->where('saida_id','=',$saida->id)->get();
        view()->share('ordem',$ordem);
        view()->share('saida',$saida);
        view()->share('dados',$saida_items);
        $pdf->loadView('saidas.pdf');
        return $pdf->stream('baixa_'.$ordem->id.'_'.date('Ymd-His').'.pdf');
    }
}
