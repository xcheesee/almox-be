<?php

namespace App\Http\Controllers;

use App\Helpers\BasesUsuariosHelper;
use App\Helpers\DepartamentoHelper;
use Illuminate\Http\Request;
use App\Http\Requests\SaidaFormRequest;
use App\Models\Saida;
use App\Http\Resources\Saida as SaidaResource;
use App\Http\Resources\OrdemServicoItem as OrdemServicoItemResource;
use App\Http\Resources\OrdemServicoProfissional as OrdemServicoProfissionalResource;
use App\Http\Resources\SaidaItem as SaidaItemResource;
use App\Http\Resources\SaidaProfissional as SaidaProfissionalResource;
use App\Mail\ItemAcabando;
use App\Models\Historico;
use App\Models\Inventario;
use App\Models\OrdemServico;
use App\Models\OrdemServicoItem;
use App\Models\OrdemServicoProfissional;
use App\Models\ResponsaveisEmail;
use App\Models\SaidaItem;
use App\Models\SaidaProfissional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
        // $saidas = Saida::paginate(15);
        //     return SaidaResource::collection($saidas);


        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);

        $saidas = QueryBuilder::for(Saida::class)
            ->select('locais.nome', 'origem.nome', 'saidas.*')
            ->leftJoin('locais as origem', 'origem.id', '=', 'saidas.origem_id')
            ->leftJoin('locais', 'locais.id', '=', 'saidas.local_servico_id')
            ->whereIn('saidas.departamento_id', $userDeptos)
            //->where('saidas.ativo','=',1)
            ->allowedFilters([
                AllowedFilter::partial('origem', 'origem.nome'),
                AllowedFilter::partial('local_servico', 'locais.nome'),
                AllowedFilter::partial('status', 'locais.status'),
                AllowedFilter::scope('baixa_depois_de'),
                AllowedFilter::scope('baixa_antes_de'),
            ])
            ->allowedSorts('id', 'baixa_datahora', 'created_at', 'origem.nome', 'locais.nome')
            ->paginate(15);

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
     * @bodyParam origem_id integer ID do Origem. Example: 1
     * @bodyParam local_servico_id integer ID do Local do serviço. Example: 2
     * @bodyParam ordem_servico_id integer ID da Ordem de serviço (caso seja fornecido). Example: 1
     * @bodyParam justificativa_os text nullable Justificativa caso a saída seja sem OS. Example: reforma
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "origem_id": 1,
     *         "local_servico_id": 2,
     *         "ordem_servico_id": 1,
     *         "justificativa_os": "Aberto sem OS devido a XYZ",
     *         "status": "A Iniciar",
     *         "baixa_datahora": "2022-08-12 08:59",
     *         "baixa_user_id": 1,
     *         "saida_profissionais": [
     *             {
     *                 "nome": "Jane Doe",
     *                 "data_inicio": "2022-08-30",
     *                 "horas_empregadas": 6
     *             },
     *             {
     *                 "nome": "John Doe",
     *                 "data_inicio": "2022-08-31",
     *                 "horas_empregadas": 4
     *             }
     *         ],
     *         "saida_items": [
     *             {
     *                 "id": 1,
     *                 "enviado": 500
     *             },
     *             {
     *                 "id": 2,
     *                 "enviado": 480
     *             }
     *         ]
     *     }
     * }
     */
    public function store(SaidaFormRequest $request)
    {
        $user = auth()->user();
        $localUser = BasesUsuariosHelper::ExibirIdsBasesUsuarios($user->id);
        $saida = new Saida();

        $saida->departamento_id = $request->input('departamento_id');
        $saida->ordem_servico_id = $request->input('ordem_servico_id');
        if (in_array($request->input('origem_id'), $localUser)) {
            $saida->origem_id = $request->input('origem_id');
        } else {
            return response()->json([
                'error' => "Você deve selecionar uma base em que esteja cadastrado."
            ]);
        }
        $saida->local_servico_id = $request->input('local_servico_id');
        $saida->tipo_servico_id = $request->input('tipo_servico_id');
        $saida->especificacao = $request->input('especificacao');
        $saida->observacoes = $request->input('observacoes');
        $saida->status = "A iniciar";
        $saida->flg_baixa = 0;
        $saida->baixa_user_id = Auth::user()->id;
        // $saida->baixa_datahora = $request->input('baixa_datahora');

        if (is_null($saida->ordem_servico_id)) {
            $this->validate($request, [
                'justificativa_os' => 'required'
            ]);

            $saida->justificativa_os = $request->input('justificativa_os');
        }

        DB::beginTransaction();
        if ($saida->save()) {
            $saidaItens = json_decode($request->input('saida_items'), true);
            if ($saidaItens) {
                $items_acabando = array();
                foreach ($saidaItens as $saida_items) {
                    //verifica se o frontend enviou lista vazia de materiais
                    if (!$saida_items["id"])
                        continue;

                    //Salvando itens na tabela saida_items
                    $saida_item = new SaidaItem();
                    $saida_item->saida_id = $saida->id;
                    $saida_item->item_id = $saida_items["id"];
                    $saida_item->quantidade = $saida_items["quantidade"];
                    $saida_item->enviado = $saida_items["quantidade"];
                    // $saida_item->usado = 0;
                    // $saida_item->retorno = 0;
                    $saida_item->save();

                    //lógica para retirar a quantidade dos itens no inventario
                    $inventario = Inventario::query()->where('local_id', '=', $saida->origem_id)
                    ->where('departamento_id', '=', $saida->departamento_id)
                    ->where('item_id', '=', $saida_items["id"])->first();
                    
                    if ($inventario) {
                        $resultado = $inventario->quantidade - $saida_items["quantidade"];
                        if ($resultado < 0) {
                            DB::rollBack();
                            $erroQtd = response()->json(['message' => 'Quantidade usada não pode exceder a quantidade em estoque.'], 410);
                            return $erroQtd;
                        } else {
                            $inventario->quantidade = $resultado;
                            $inventario->save();
                            if ($inventario->quantidade <= $inventario->qtd_alerta) {
                                $items_acabando[] = $inventario;
                            }
                        }
                    } else {
                        DB::rollBack();
                        $erroQtd = response()->json(['message' => 'O item informado não se encontra na base de origem selecionada.'], 410);
                        return $erroQtd;
                    }
                }
                if (count($items_acabando) > 0) {
                    //Enviar e-mail aos responsáveis
                    $responsaveis = ResponsaveisEmail::query()->where('departamento_id', '=', $saida->departamento_id)->get();
                    foreach ($responsaveis as $responsavel) {
                        Mail::to($responsavel->email)->send(new ItemAcabando($items_acabando));
                    }
                }
            }

            //Lidando com a lista de profissionais  (caso não tenha OS nessa saída)
            $saidaProfissionais = json_decode($request->input('saida_profissionais'), true);
            if ($saidaProfissionais) {
                foreach ($saidaProfissionais as $saida_profissionais) {
                    //Salvando itens na tabela saida_items
                    $saida_profissional = new SaidaProfissional();
                    $saida_profissional->saida_id = $saida->id;
                    // $saida_profissional->profissional_id = $saida_profissionais["id"];
                    $saida_profissional->nome = $saida_profissionais["nome"];
                    $saida_profissional->data_inicio = $saida_profissionais["data_inicio"];
                    $saida_profissional->horas_empregadas = $saida_profissionais["horas_empregadas"];
                    $saida_profissional->save();
                }
            }

            // Salva na tabela historicos
            $historico = new Historico();
            $historico->nome_tabela = 'Saida';
            $historico->data_acao = date("Y-m-d");
            $historico->tipo_acao = 'criacao';
            $historico->user_id = Auth::user()->id;
            $historico->registro = json_encode(new SaidaResource($saida));
            $historico->save();

            DB::commit();

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
     *         "baixa_user_id": 1,
     *         "baixa_datahora": "2022-08-12 08:59"
     *     }
     * }
     */
    public function show($id)
    {
        $saida = Saida::findOrFail($id);
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
     * @bodyParam baixa_user_id integer ID do usuario. Example: 1
     * @bodyParam baixa_datahora datetime required Data e hora da baixa. Example: "2022-08-12 08:59"
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "ordem_servico_id": 1,
     *         "baixa_user_id": 1,
     *         "baixa_datahora": "2022-08-12 08:59",
     *         "saida_profissionais": [
     *              {
     *                  "id": 1,
     *                  "horas_empregadas": 42
     *                  "data_inicio": "2023-01-01"
     *              }
     *          ]
     *     }
     * }
     */
    public function update(SaidaFormRequest $request, $id)
    {
        $saida = Saida::findOrFail($id);

        if ($saida->flg_baixa != 0) {
            return response()->json(['message' => "Não é possivel realizar a edição dos dados de uma saída apos sua baixa!"]);
        }

        //$saida->departamento_id = $request->input('departamento_id');
        $saida->ordem_servico_id = $request->input('ordem_servico_id');
        //$saida->origem_id = $request->input('origem_id');
        $saida->local_servico_id = $request->input('local_servico_id');
        $saida->tipo_servico_id = $request->input('tipo_servico_id');
        $saida->especificacao = $request->input('especificacao');
        $saida->observacoes = $request->input('observacoes');
        $saida->status = $request->input('status');
        $saida->flg_baixa = 0;
        $saida->baixa_user_id = Auth::user()->id;

        if (is_null($saida->ordem_servico_id)) {
            $this->validate($request, [
                'justificativa_os' => 'required'
            ]);

            $saida->justificativa_os = $request->input('justificativa_os');
        }

        if ($saida->save()) {

            //deletando items da ordem de servico
            $saidaItems = SaidaItem::where('saida_id', $id)->get();
            foreach ($saidaItems as $item) {

                $inventario = Inventario::query()->where('local_id', '=', $saida->origem_id)
                    ->where('departamento_id', '=', $saida->departamento_id)
                    ->where('item_id', '=', $item->item_id)->first();

                if ($inventario) {
                    $inventario->quantidade += $item->quantidade;
                    $inventario->save();
                }

                $item->delete();
            }

            $saidaItens = json_decode($request->input('saida_items'), true);
            if ($saidaItens) {
                foreach ($saidaItens as $saida_items) {

                    $saida_item = new saidaItem();
                    $saida_item->saida_id = $saida->id;
                    $saida_item->item_id = $saida_items["id"];
                    $saida_item->quantidade = $saida_items["quantidade"];
                    $saida_item->enviado = $saida_items["quantidade"];
                    $saida_item->save();

                    // lógica para retirar a quantidade dos itens no inventario (sem O.S.)
                    $inventario = Inventario::query()->where('local_id', '=', $saida->origem_id)
                        ->where('departamento_id', '=', $saida->departamento_id)
                        ->where('item_id', '=', $saida_items["id"])
                        ->first();

                    if ($inventario) {
                        $resultado = $inventario->quantidade - $saida_items["quantidade"];
                        if ($resultado <= 0) {
                            DB::rollBack();
                            $erroQtd = response()->json(['error' => 'Quantidade usada não pode exceder a quantidade em estoque.']);
                            return $erroQtd;
                        } else {
                            $inventario->quantidade = $resultado;
                            $inventario->save();
                            if ($inventario->quantidade <= $inventario->qtd_alerta) {
                                $items_acabando[] = $inventario;
                            }
                        }
                    }
                }
            }
        }

        if ($request->input('saida_profissionais')) {
            $input_profissionais = json_decode($request->input('saida_profissionais'), true);
            SaidaProfissional::where('saida_id', $id)->delete();

            foreach ($input_profissionais as $profissional) {
                if (!$profissional)
                    continue;
                $saida_profissional = new SaidaProfissional();
                $saida_profissional->saida_id = $saida->id;
                //$saida_profissional->profissional_id = $profissional["id"];
                $saida_profissional->nome = $profissional["nome"];
                $saida_profissional->data_inicio = $profissional["data_inicio"];
                $saida_profissional->horas_empregadas = $profissional["horas_empregadas"];
                $saida_profissional->save();
            }
        }

        return new SaidaResource($saida);
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
     *         "baixa_user_id": 1,
     *         "baixa_datahora": "2022-08-12 08:59"
     *     }
     * }
     */
    public function destroy($id)
    {

        SaidaProfissional::where('saida_id', $id)->delete();
        SaidaItem::where('saida_id', $id)->delete();
        $saida = Saida::findOrFail($id);

        if ($saida->delete()) {
            return response()->json([
                'message' => 'Saida deletada com sucesso!',
                'data' => new SaidaResource($saida)
            ]);
        }
    }

    /**
     * Mostra os itens de uma ordem de serviço ou da saída
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
    public function items($id)
    {
        $saida = Saida::findOrFail($id);

        //$ordem_servico_itens = OrdemServicoItem::where("ordem_servico_id", "=", $saida->ordem_servico_id)->get();
        $saida_itens = SaidaItem::where("saida_id", "=", $id)->get();

        //if($ordem_servico_itens->isNotEmpty()){
        //    return OrdemServicoItemResource::collection($ordem_servico_itens);
        if($saida_itens->isNotEmpty()){
            return SaidaItemResource::collection($saida_itens);
        } else {
            return response()->json([
                'mensagem' => "nada cadastrado."
            ]);
        }


    }

    /**
     * Emite a Baixa de uma ordem de serviço
     * @authenticated
     *
     *
     * @urlParam id integer required ID da ordem de serviço que deseja editar. Example: 1
     *
     * @bodyParam saida_items object[] required Itens da ordem de serviço. Example: [{"id": 2, "quantidade": 60, "usado": 50, "retorno": 10},{"id": 3, "quantidade": 5, "usado": 3, "retorno": 2}]
     * @bodyParam saida_items.id integer required ID do item. Example: 2
     * @bodyParam saida_items.quantidade integer required Quantidade solicitada do item para o local de serviço. Example: 60
     * @bodyParam saida_items.enviado integer required Quantidade enviada do item para o local de serviço. Example: 60
     * @bodyParam saida_items.usado integer required Quantidade usada do item para o serviço. Example: 50
     * @bodyParam saida_items.retorno integer required Quantidade a ser devolvida do item para a base de origem. Example: 10
     *
     *
     * @response 200 {
     *     "message": "Baixa da Ordem de serviço efetuada com sucesso!",
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
     *         "flg_baixa": 1,
     *         "user_id": 1
     *     }
     * }
     */
    public function baixa(Request $request, $id)
    {
        $saida = Saida::findOrFail($id);

        $items = $request->input('saida_items');
        if ($items) {
            //salvando a baixa na BD
            DB::beginTransaction();
            $saida->status = 'Finalizada';
            $saida->baixa_datahora = date('Y-m-d H:i:s');
            $saida->baixa_user_id = auth()->user()->id;
            if ($saida->save()) {
                SaidaItem::where('saida_id', '=', $saida->id)->delete();
                foreach ($items as $saida_items) {

                        // Cria um novo SaidaItem
                        $novo_item = new SaidaItem();
                        $novo_item->saida_id = $saida->id;
                        $novo_item->item_id = $saida_items["id"];
                        $novo_item->quantidade = $saida_items["quantidade"];
                        $novo_item->enviado = $saida_items["enviado"];
                        $novo_item->usado = $saida_items["usado"];
                        $novo_item->retorno = $saida_items["retorno"];
                        $novo_item->save();
                    
                    
                    //lógica para devolver a quantidade dos itens retornados para o inventario de origem
                    $saida_inventario = Inventario::query()->where('local_id', '=', $saida->origem_id)
                        ->where('departamento_id', '=', $saida->departamento_id)
                        ->where('item_id', '=', $saida_items["id"])->first();

                    if ($saida_inventario) {
                        $saida_inventario->quantidade += $saida_items["retorno"];
                        $saida_inventario->save();
                    }
                }

                $saida->flg_baixa = true;
                $saida->save();

                // Salva na tabela historicos
                $historico = new Historico();
                $historico->nome_tabela = 'Saida_Inventario';
                $historico->data_acao = date("Y-m-d");
                $historico->tipo_acao = 'atualizacao';
                $historico->user_id = Auth::user()->id;
                $historico->registro = json_encode(new SaidaResource($saida));
                $historico->save();

                DB::commit();
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Erro ao registrar a base, tente novamente mais tarde.'
                ], 410);
            }
        } else {
            return response()->json([
                'message' => 'Para dar baixa, é preciso enviar a lista de itens e seus valores de usado e retornado.'
            ], 410);
        }

        return response()->json([
            'message' => 'Baixa da Ordem de serviço efetuada com sucesso!',
            'data' => new SaidaResource($saida)
        ]);
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
    public function profissionais($id)
    {

        $saida = Saida::findOrFail($id);

        //if ($saida->ordem_servico_id){
        //    $ordem_servico_profissionais = OrdemServicoProfissional::where("ordem_servico_id","=",$saida->ordem_servico_id)->get();
        //    return OrdemServicoProfissionalResource::collection($ordem_servico_profissionais);
        //} else { //saida sem OS
        $saida_profissionais = SaidaProfissional::where("saida_id", "=", $id)->get();
        return SaidaProfissionalResource::collection($saida_profissionais);
        //}

    }
}
