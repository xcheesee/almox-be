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
use App\Http\Resources\Saida as SaidaResource;
use App\Http\Resources\SaidaItem as SaidaItemResource;
use App\Http\Resources\OrdemServicoItem as OrdemServicoItemResource;
use App\Http\Resources\OrdemServicoProfissional as OrdemServicoProfissionalResource;
use App\Mail\ItemAcabando;
use App\Models\Saida;
use App\Models\SaidaItem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Historico;
use App\Models\OrdemServicoProfissional;
use App\Models\ResponsaveisEmail;

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
     * @queryParam filter[servico_depois_de] Filtro inicial de período da data de serviço. Example: 2023-01-01
     * @queryParam filter[servico_antes_de] Filtro final de período da data de serviço. Example: 2023-12-31
     * @queryParam sort Campo a ser ordenado (padrão ascendente, inserir um hífen antes para decrescente). Colunas possíveis: 'id', 'data_inicio_servico', 'data_fim_servico', 'origem.nome', 'locais.nome' Example: -locais.nome
     *
     */
    public function index()
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);

        $ordem_servicos = QueryBuilder::for(OrdemServico::class)
        ->select('locais.nome', 'origem.nome', 'ordem_servicos.*')
        ->leftJoin('locais as origem', 'origem.id', '=', 'ordem_servicos.origem_id')
        ->leftJoin('locais', 'locais.id', '=', 'ordem_servicos.local_servico_id')
        ->whereIn('ordem_servicos.departamento_id',$userDeptos)
        ->where('ordem_servicos.ativo','=',1)
        ->allowedFilters([
                AllowedFilter::partial('origem','origem.nome'), 
                AllowedFilter::partial('local_servico','locais.nome'),
                "id",
                AllowedFilter::scope('servico_depois_de'),
                AllowedFilter::scope('servico_antes_de'),
            ])
        ->allowedSorts('id', 'data_inicio_servico', 'data_fim_servico', 'origem.nome', 'locais.nome')
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
     * @bodyParam status enum required ('A iniciar','Iniciada','Finalizada') Tipo. Example: Iniciada
     * @bodyParam data_inicio_servico datetime required Data do serviço. Example: 2022-08-30T14:48
     * @bodyParam data_fim_servico datetime required Data do serviço. Example: 2022-08-31T17:50
     * @bodyParam especificacao text nullable Especificação. Example: reforma
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
     *         "status": "Iniciada",
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "especificacao": "reforma",
     *         "observacoes": "observações referente ao serviço",,
     *         "flg_baixa": 0,
     *         "user_id": 1,
     *         "ordem_servico_profissionais": [
     *             {
     *                 "id": 1,
     *                 "data_inicio": "2022-08-30",
     *                 "horas_empregadas": 6
     *             },
     *             {
     *                 "id": 2,
     *                 "data_inicio": "2022-08-31",
     *                 "horas_empregadas": 4
     *             }
     *         ],
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
        // $ordem_servico->status = $request->input('status');
        // $ordem_servico->data_inicio_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_inicio_servico'));
        // $ordem_servico->data_fim_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_fim_servico'));
        $ordem_servico->especificacao = $request->input('especificacao');
        $ordem_servico->observacoes = $request->input('observacoes');
        $ordem_servico->user_id = Auth::user()->id;


        // if (is_null($ordem_servico->numero_ordem_servico)){
        //     $this->validate($request, [
        //         'justificativa_os' => 'required'
        //     ]);

        //     $ordem_servico->justificativa_os = $request->input('justificativa_os');
        // }

        DB::beginTransaction();
        if ($ordem_servico->save()) {
            // Lidando com os itens adicionados
            $ordemServicoItens = $request->input('ordem_servico_items');
            if ($ordemServicoItens){
                $items_acabando = array();
                foreach ($ordemServicoItens as $ordem_servico_items){
                    //verifica se o frontend enviou lista vazia de materiais
                    if (!$ordem_servico_items["id"]) continue;

                    //Salvando itens na tabela ordem_servico_items
                    $ordem_servico_item = new OrdemServicoItem();
                    $ordem_servico_item->ordem_servico_id = $ordem_servico->id;
                    $ordem_servico_item->item_id = $ordem_servico_items["id"];
                    $ordem_servico_item->quantidade = $ordem_servico_items["quantidade"];
                    $ordem_servico_item->save();

                    //lógica para retirar a quantidade dos itens no inventario
                    $inventario = Inventario::query()->where('local_id','=',$ordem_servico->origem_id)
                                                        ->where('departamento_id','=',$ordem_servico->departamento_id)
                                                        ->where('item_id','=',$ordem_servico_items["id"])->first();

                    if ($inventario) {
                        $inventario->quantidade -= $ordem_servico_items["quantidade"];
                        $resultado = $inventario->quantidade;
                        if ($resultado <= 0) {
                            DB::rollBack();
                            $erroQtd = response()->json(['message' => 'Quantidade usada não pode exceder a quantidade em estoque.'], 410);
                            return $erroQtd;
                        } else {
                            $inventario->save();
                            if ($inventario->quantidade <= $inventario->qtd_alerta) {
                                $items_acabando[]=$inventario;
                            }
                        }
                    }else{
                        DB::rollBack();
                        $erroQtd = response()->json(['message' => 'O item informado não se encontra na base de origem selecionada.'], 410);
                        return $erroQtd;
                    }
                }
                if (count($items_acabando) > 0){
                    //Enviar e-mail aos responsáveis
                    $responsaveis = ResponsaveisEmail::query()->where('departamento_id','=',$ordem_servico->departamento_id)->get();
                    foreach($responsaveis as $responsavel){
                        Mail::to($responsavel->email)->send(new ItemAcabando($items_acabando));
                    }
                }
            }

            //Lidando com a lista de profissionais da ordem de serviço
            $ordemServicoProfissionais = $request->input('ordem_servico_profissionais');
            if ($ordemServicoProfissionais){
                foreach ($ordemServicoProfissionais as $ordem_servico_profissionais){
                    //Salvando itens na tabela ordem_servico_items
                    $ordem_servico_profissional = new OrdemServicoProfissional();
                    $ordem_servico_profissional->ordem_servico_id = $ordem_servico->id;
                    $ordem_servico_profissional->profissional_id = $ordem_servico_profissionais["id"];
                    $ordem_servico_profissional->data_inicio = $ordem_servico_profissionais["data_inicio"];
                    $ordem_servico_profissional->horas_empregadas = $ordem_servico_profissionais["horas_empregadas"];
                    $ordem_servico_profissional->save();
                }
            }

            // Salva na tabela historicos
            $historico = new Historico();
            $historico->nome_tabela = 'Ordem_Servico';
            $historico->data_acao = date("Y-m-d");
            $historico->tipo_acao = 'criacao';
            $historico->user_id = Auth::user()->id;
            $historico->registro = json_encode(new OrdemServicoResource($ordem_servico));
            $historico->save();

            DB::commit();

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
     *         "status": "Iniciada",
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "especificacao": "reforma",
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
     * @bodyParam status enum required ('A iniciar','Iniciada','Finalizada') Tipo. Example: Iniciada
     * @bodyParam data_inicio_servico datetime required Data do serviço. Example: 2022-08-30T14:48
     * @bodyParam data_fim_servico datetime required Data do serviço. Example: 2022-08-31T17:50
     * @bodyParam especificacao text nullable Especificação. Example: reforma
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
     *         "status": "Iniciada",
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "especificacao": "reforma",
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
        // $ordem_servico->status = $request->input('status');
        // $ordem_servico->data_inicio_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_inicio_servico'));
        // $ordem_servico->data_fim_servico = HtmlHelper::converteDatetimeLocal2MySQL($request->input('data_fim_servico'));
        $ordem_servico->especificacao = $request->input('especificacao');
        $ordem_servico->observacoes = $request->input('observacoes');
        $ordem_servico->user_id = Auth::user()->id;

        DB::beginTransaction();
        if ($ordem_servico->save()) {
            // Lidando com os itens adicionados
            $ordemServicoItens = $request->input('ordem_servico_items');
            if ($ordemServicoItens){
                foreach ($ordemServicoItens as $ordem_servico_items){
                    //verifica se o frontend enviou lista vazia de materiais
                    if (!$ordem_servico_items["id"]) continue;

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
                        if ($resultado <= 0) {
                            DB::rollBack();
                            $erroQtd = response()->json(['error' => 'Quantidade usada não pode exceder a quantidade em estoque.']);
                            return $erroQtd;
                        } else {
                            $inventario->save();
                        }
                    }
                }
            }

            // Salva na tabela historicos
            $historico = new Historico();
            $historico->nome_tabela = 'Ordem_Servico';
            $historico->data_acao = date("Y-m-d");
            $historico->tipo_acao = 'atualizacao';
            $historico->user_id = Auth::user()->id;
            $historico->registro = json_encode(new OrdemServicoResource($ordem_servico));
            $historico->save();

            DB::commit();
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
     *         "status": "Iniciada",
     *         "data_inicio_servico": "2022-08-30T14:48",
     *         "data_fim_servico": "2022-08-31T17:50",
     *         "especificacao": "reforma",
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

        // Salva na tabela historicos
        $historico = new Historico();
        $historico->nome_tabela = 'Ordem_Servico';
        $historico->data_acao = date("Y-m-d");
        $historico->tipo_acao = 'exclusao';
        $historico->user_id = Auth::user()->id;
        $historico->registro = json_encode(new OrdemServicoResource($ordem_servico));
        $historico->save();

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
     * Gera um json da baixa efetuada na ordem de serviço
     * @authenticated
     *
     * @header Accept application/pdf
     *
     * @urlParam id integer required ID da ordem de serviço que deseja deletar. Example: 1
     *
     */
    public function baixa_json($id){
        $ordem = OrdemServico::findOrFail($id);
        $saida = Saida::query()->where('ordem_servico_id','=',$id)->first();
        $saida_items = SaidaItem::query()->where('saida_id','=',$saida->id)->get();
        //dd($saida_items);
        return response()->json([
            'message' => 'Dados da baixa da Ordem de Serviço #'.$id,
            'ordem_servico' => new OrdemServicoResource($ordem),
            'baixa' => new SaidaResource($saida),
            'baixa_items' => SaidaItemResource::collection($saida_items)
        ]);
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
        $ordem_servico_items = OrdemServicoItem::query()->where('ordem_servico_id','=',$id)->get();
        $saida = Saida::query()->where('ordem_servico_id','=',$id)->first();
        $saida_items = array();
        if ($saida){
            $dados = SaidaItem::query()->where('saida_id','=',$saida->id)->get();
            foreach($dados as $saida_item){
                $saida_items[$saida_item->item_id] = [
                    $saida_item->enviado,
                    $saida_item->usado,
                    $saida_item->retorno,
                ];
            }
        }
        $profissionais = OrdemServicoProfissional::query()->where('ordem_servico_id','=',$id)->get();
        view()->share('ordem',$ordem);
        view()->share('ordem_servico_items',$ordem_servico_items);
        view()->share('saida',$saida);
        view()->share('saida_items',$saida_items);
        view()->share('profissionais',$profissionais);
        $pdf->loadView('saidas.pdf');
        return $pdf->stream('baixa_'.$ordem->id.'_'.date('Ymd-His').'.pdf');

        // return view ('saidas.pdf', compact('ordem','saida','saida_items'));
    }

    /**
     * Mostra os itens de uma ordem de serviço
     * @authenticated
     *
     * @urlParam id integer required ID da ordem de serviço. Example: 2
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "ordem_servico_id": 2,
     *             "item_id": 1,
     *             "quantidade": 10
     *         },{
     *             "id": 2,
     *             "ordem_servico_id": 2,
     *             "item_id": 3,
     *             "quantidade": 800
     *         }
     *     ]
     * }
     */
    public function items($id){
        $ordem_servico_itens = OrdemServicoItem::where("ordem_servico_id","=",$id)->get();
        return OrdemServicoItemResource::collection($ordem_servico_itens);
    }

    /**
     * Mostra os profissionais de uma ordem de serviço
     * @authenticated
     *
     * @urlParam id integer required ID da ordem de serviço. Example: 2
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "ordem_servico_id": 2,
     *             "profissional_id": 1,
     *             "data_inicio": '2022-11-07',
     *             "horas_empregadas": 10
     *         },{
     *             "id": 2,
     *             "ordem_servico_id": 2,
     *             "profissional_id": 2,
     *             "data_inicio": '2022-11-07',
     *             "horas_empregadas": 6
     *         }
     *     ]
     * }
     */
    public function profissionais($id){
        $ordem_servico_profissionais = OrdemServicoProfissional::where("ordem_servico_id","=",$id)->get();
        return OrdemServicoProfissionalResource::collection($ordem_servico_profissionais);
    }

    /**
     * Mostra as ordens de serviço pesquisando por número
     * @authenticated
     *
     * @urlParam id integer required ID da ordem de serviço. Example: 2
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "ordem_servico_id": 2,
     *             "data_inicio": '2022-11-07',
     *         },{
     *             "id": 2,
     *             "ordem_servico_id": 2,
     *             "data_inicio": '2022-11-07',
     *         }
     *     ]
     * }
     */
    public function os_por_numero($id){
        $ordem_servicos = OrdemServico::where("id","like",$id."%")->get();
        return OrdemServicoResource::collection($ordem_servicos);
    }
}
