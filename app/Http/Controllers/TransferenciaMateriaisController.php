<?php

namespace App\Http\Controllers;

use App\Helpers\BasesUsuariosHelper;
use App\Http\Requests\TransferenciaFormRequest;
use App\Models\Inventario;
use App\Models\Local;
use App\Models\local_users;
use App\Models\TransferenciaDeMateriais;
use App\Models\TransferenciaItens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\TransferenciaDeMateriais as TransferenciaDeMateriaisResource;
use App\Http\Resources\TransferenciaDeMateriaisItem as TransferenciaDeMateriaisItemResource;

/**
 * @group Tranferencia de Materiais
 * 
 * APIs para listar, cadastrar, editar e remover dados das Transferencias.
 */
class TransferenciaMateriaisController extends Controller
{
    /**
     * Lista Todas as Transferencias
     * @authenticated
     * 
     * @response 200 {
     *      "mensagem": "Todas transferencias cadastradas",
     *      "transferencias": [
     *          {
     *              "id": 4,
     *              "base_origem_id": {
     *                  "id": 1,
     *                  "departamento_id": 1,
     *                  "nome": "Lugar 01",
     *                  "tipo": "parque",
     *                  "cep": "93472898",
     *                  "logradouro": null,
     *                  "numero": null,
     *                  "bairro": null,
     *                  "cidade": null,
     *                  "created_at": null,
     *                  "updated_at": null
     *              },
     *              "base_destino_id": {
     *                  "id": 2,
     *                  "departamento_id": 2,
     *                  "nome": "Lugar 02",
     *                  "tipo": "base",
     *                  "cep": null,
     *                  "logradouro": null,
     *                  "numero": null,
     *                  "bairro": null,
     *                  "cidade": null,
     *                  "created_at": null,
     *                  "updated_at": null
     *              },
     *              "data_transferencia": "2023-05-05 00:00:00",
     *              "status": "recebido",
     *              "user_id": 1,
     *              "observacao": "Está faltando um parafuso",
     *              "observacao_motivo": "avaria",
     *              "observacao_user_id": 1,
     *              "created_at": "2023-05-04T15:02:33.000000Z",
     *              "updated_at": "2023-05-10T13:02:27.000000Z"
     *    }
     * }
     */
    public function index()
    {
        $transferencia = QueryBuilder::for(TransferenciaDeMateriais::class)
            ->leftJoin('locais as origem', 'origem.id', '=', 'transferencia_de_materiais.base_origem_id')
            ->leftJoin('locais', 'locais.id', '=', 'transferencia_de_materiais.base_destino_id')
            ->select('locais.nome as destino', 'origem.nome as origem', 'transferencia_de_materiais.*')
            ->allowedSorts('id', 'data_transferencia', 'destino', 'origem', 'status')
            ->allowedFilters([
                allowedFilter::partial('origem', 'origem.nome'),
                allowedFilter::partial('destino', 'locais.nome'),
                allowedFilter::scope('transferencia_depois_de'),
                allowedFilter::scope('transferencia_antes_de'),
                "status"
            ])
            ->paginate(15);

        return TransferenciaDeMateriaisResource::collection($transferencia);
    }

    /**
     * Cadastra uma nova Transferencia.
     * @authenticated
     *
     *
     * @bodyParam base_origem_id integer required ID do Local que sairá. Example: 2
     * @bodyParam base_destino_id integer required ID do Local destino. Example: 1
     * @bodyParam data_transferencia date required Data da Transferencia. Example: 2023/05/05
     * @bodyParam status enum required Status da transferencia (enviado, recebido, recusado). Example: enviado
     * @bodyParam observacao text Observação. Example: Está faltando um parafuso
     * @bodyParam observacao_motivo enum Observação Motivo (nao_enviado, itens_faltando, extravio, furto, avaria). Example: itens_faltando
     * @bodyParam itens object required Lista de itens.
     * @bodyParam itens[].item_id integer required ID do item. Example: 5
     * @bodyParam itens[].quantidade integer required Quantidade de itens. Example: 355
     *
     * @response 200 {
     *      "mensagem": "Transferencia criada com sucesso",
     *      "transferencia": {
     *          "base_origem_id": 1,
     *          "base_destino_id": 2,
     *          "data_transferencia": "2023/05/05",
     *          "status": "recebido",
     *          "user_id": 1,
     *          "observacao": "Uma Observação",
     *          "observacao_motivo": "avaria",
     *          "observacao_user_id": 1,
     *          "updated_at": "2023-05-11T13:18:55.000000Z",
     *          "created_at": "2023-05-11T13:18:55.000000Z",
     *          "id": 18
     *      },
     *      "itens": [
     *          {
     *              "item_id": 4,
     *              "quantidade": 1
     *          },
     *          {
     *              "item_id": 45,
     *              "quantidade": 3
     *          }
     *      ]
     *  }
     */
    public function store(TransferenciaFormRequest $request)
    {
        $user = auth()->user();
        $localUser = BasesUsuariosHelper::ExibirIdsBasesUsuarios($user->id);

        if ($user->hasRole(['almoxarife', 'encarregado'])) {

            $transferencia = new TransferenciaDeMateriais();

            if (in_array($request->input('base_origem_id'), $localUser)) {
                $transferencia->base_origem_id = $request->input('base_origem_id');
            } else {
                return response()->json([
                    'error' => "Você deve selecionar alguma base em que esteja cadastrado."
                ]);
            }
            $transferencia->base_destino_id = $request->input('base_destino_id');
            $transferencia->data_transferencia = $request->input('data_transferencia');
            $transferencia->status = "enviado";
            $transferencia->user_id = Auth::user()->id;
            $transferencia->observacao = $request->input('observacao');
            $transferencia->observacao_motivo = $request->input('observacao_motivo');
            $transferencia->observacao_user_id = Auth::user()->id;

            DB::beginTransaction();

            if ($transferencia->save()) {
                $itens = json_decode($request->input('itens'), true);
                foreach ($itens as $item) {
                    $transferenciaItem = new TransferenciaItens();

                    $transferenciaItem->transferencia_materiais_id = $transferencia->id;

                    if (array_key_exists('id', $item)) {
                        $transferenciaItem->item_id = $item["id"];
                    } else {
                        DB::rollBack();

                        return response()->json([
                            'mensagem' => "id do item não informado, Transferencia não cadastrada."
                        ], 420);
                    }

                    if (array_key_exists('quantidade', $item)) {
                        $transferenciaItem->quantidade = $item['quantidade'];
                    } else {
                        DB::rollBack();

                        return response()->json([
                            'mensagem' => "quantidade não informada, Transferencia não cadastrada."
                        ], 420);
                    }

                    $transferenciaItem->save();
                }

                DB::commit();

                return response()->json([
                    'mensagem' => 'Transferencia cadastrada com sucesso!',
                    'transferencia' => $transferencia,
                    'itens' => $itens
                ], 200);
            }
            ;
        } else {
            return response()->json([
                'mensagem' => 'Você não possui permissão para cadastrar uma transferencia'
            ], 401);
        }
    }



    /**
     * Mostrar uma transferencia
     * @authenticated
     * 
     * @urlParam id integer required ID da transferencia. Example: 5
     * 
     * @response 200 {
     *      "mensagem": "Transferencia encontrada com sucesso!",
     *      "transferencia": {
     *          "id": 4,
     *          "base_origem_id": {
     *              "id": 1,
     *              "departamento_id": 1,
     *              "nome": "Lugar 01",
     *              "tipo": "parque",
     *              "cep": "93472898",
     *              "logradouro": null,
     *              "numero": null,
     *              "bairro": null,
     *              "cidade": null,
     *              "created_at": null,
     *              "updated_at": null
     *          },
     *          "base_destino_id": {
     *              "id": 2,
     *              "departamento_id": 2,
     *              "nome": "Lugar 02",
     *              "tipo": "base",
     *              "cep": null,
     *              "logradouro": null,
     *              "numero": null,
     *              "bairro": null,
     *              "cidade": null,
     *              "created_at": null,
     *              "updated_at": null
     *          },
     *          "data_transferencia": "2023-05-05 00:00:00",
     *          "status": "recebido",
     *          "user_id": 1,
     *          "observacao": "Está faltando um parafuso",
     *          "observacao_motivo": "avaria",
     *          "observacao_user_id": 1,
     *          "created_at": "2023-05-04T15:02:33.000000Z",
     *          "updated_at": "2023-05-10T13:02:27.000000Z"
     *      }
     * }
     * 
     * @response 404 {
     *      "mensagem": "Transferencia naõ encontrada!"
     *      }
     */
    public function show($id)
    {
        $transferencia = TransferenciaDeMateriais::with('base_origem', 'base_destino', 'itens_da_transferencia')->findOrFail($id);

        if ($transferencia) {
            return new TransferenciaDeMateriaisResource($transferencia);
        } else {
            return response()->json([
                'mensagem' => 'Transferencia naõ encontrada!',
            ], 404);
        }
    }

    /**
     * Edita uma Transferencia.
     * @authenticated
     * 
     * @urlParam id integer required ID da transferencia. Example: 4
     * 
     * @bodyParam base_origem_id integer required ID do Local que sairá. Example: 1
     * @bodyParam base_destino_id integer required ID do Local destino. Example: 2
     * @bodyParam data_transferencia date required Data da Transferencia. Example: 2023/05/05
     * @bodyParam status enum required Status da transferencia (enviado, recebido, recusado). Example: recebido
     * @bodyParam observacao text Observação. Example: Está faltando um parafuso
     * @bodyParam observacao_motivo enum Observação Motivo (nao_enviado, itens_faltando, extravio, furto, avaria). Example: avaria
     *
     * @response 200 {
     *      {
     *    "mensagem": "Transferencia atualizada com sucesso!",
     *    "transferencia": {
     *        "id": 12,
     *        "base_origem_id": 1,
     *        "base_destino_id": 2,
     *        "data_transferencia": "2023/05/05",
     *        "status": "recebido",
     *        "user_id": 1,
     *        "observacao": "Está faltando um parafuso",
     *        "observacao_motivo": "avaria",
     *        "observacao_user_id": 1,
     *        "created_at": "2023-05-10T12:56:18.000000Z",
     *        "updated_at": "2023-05-10T12:57:33.000000Z"
     *      }
     * }
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $localUser = BasesUsuariosHelper::ExibirIdsBasesUsuarios($user->id);
        $transferencia = TransferenciaDeMateriais::findOrFail($id);

        if (in_array($request->input('base_origem_id'), $localUser)) {
            $transferencia->base_origem_id = $request->input('base_origem_id');
        } else {
            return response()->json([
                'error' => "Você deve selecionar alguma base em que esteja cadastrado."
            ]);
        }
        $transferencia->base_destino_id = $request->input('base_destino_id');
        $transferencia->data_transferencia = $request->input('data_transferencia');
        $transferencia->status = $request->input('status');
        $transferencia->user_id = Auth::user()->id;
        $transferencia->observacao = $request->input('observacao');
        $transferencia->observacao_motivo = $request->input('observacao_motivo');
        $transferencia->observacao_user_id = Auth::user()->id;

        $transferencia->update();

        return response()->json([
            'mensagem' => 'Transferencia atualizada com sucesso!',
            'transferencia' => $transferencia
        ], 200);
    }

    /**
     * Deleta uma Transferencia
     * @authenticated
     *
     *
     * @urlParam id integer required ID da Transferencia que deseja deletar. Example: 4
     *
     * @response 200 {
     *     "mensagem": "Transferencia deletada com sucesso!",
     *     "transferencia": {
     *         "id": 3,
     *         "base_origem_id": 1,
     *         "base_destino_id": 2,
     *         "data_transferencia": "2023-05-04 11:54:26",
     *         "status": "enviado",
     *         "user_id": 1,
     *         "observacao": null,
     *         "observacao_motivo": null,
     *         "observacao_user_id": null,
     *         "created_at": null,
     *         "updated_at": null
     *     }
     * }
     * 
     * @response 404 {
     *     "mensagem": "Transferencia não encontrada para deletar!"
     *     }
     */
    public function destroy($id)
    {
        $transferencia = TransferenciaDeMateriais::where('id', $id)->first();

        if ($transferencia) {
            $transferenciaItens = TransferenciaItens::where('transferencia_materiais_id', $id)->get();

            foreach ($transferenciaItens as $itens) {
                $itens->delete();
            }

            $transferencia->delete();

            return response()->json([
                'mensagem' => 'Transferencia deletada com sucesso!',
                'transferencia' => $transferencia
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Transferencia não encontrada para deletar!',
            ], 404);
        }
    }

    /**
     * Realiza a transferencia de materiais
     * @authenticated
     * 
     * @urlParam id integer required ID da transferencia. Example: 199
     * 
     * @response 200 {
     *      "mensagem": "Tranferencia de materiais realizada com sucesso!"
     *      }
     * 
     */
    public function transferir_itens(Request $request, $id)
    {
        $transferencia = TransferenciaDeMateriais::find($id);

        $user = auth()->user();

        $localUsers = local_users::where('user_id', $user->id)->first();

        if ($localUsers->local_id == $transferencia->base_destino_id) {

            if ($user->hasRole(['almoxarife', 'encarregado'])) {

                $transferencia->status = 'recebido';

                $transferencia->save();

                $iventarios_base_origem = Inventario::where('local_id', $transferencia->base_origem_id)->get();

                foreach ($iventarios_base_origem as $iventario_origem) {

                    $itensTransferecia = TransferenciaItens::where('transferencia_materiais_id', $transferencia->id)->get();

                    foreach ($itensTransferecia as $item_transferencia) {

                        if ($item_transferencia->item_id == $iventario_origem->item_id) {

                            $iventario_origem->quantidade -= $item_transferencia->quantidade;

                            $iventario_origem->save();
                        }
                    }
                }

                $inventarios_base_destino = Inventario::where('local_id', $transferencia->base_destino_id)->get();
                $transferencia_itens = TransferenciaItens::where('transferencia_materiais_id', $transferencia->id)->get();

                if ($inventarios_base_destino->count() <= 0) {
                    foreach ($transferencia_itens as $itens_transferencia) {

                        $local = Local::where('id', $transferencia->base_destino_id)->first();

                        $novo_item = new Inventario();

                        $novo_item->departamento_id = $local->departamento_id;
                        $novo_item->item_id = $itens_transferencia->item_id;
                        $novo_item->local_id = $transferencia->base_destino_id;
                        $novo_item->quantidade += $itens_transferencia->quantidade;
                        $novo_item->qtd_alerta = 0;

                        $novo_item->save();
                    }
                }

                foreach ($transferencia_itens as $itens_transferencia) {
                    foreach ($inventarios_base_destino as $destino_itens) {
                        if ($itens_transferencia->item_id == $destino_itens->item_id) {

                            $destino_itens->quantidade += $itens_transferencia->quantidade;

                            $destino_itens->save();
                        }
                    }
                }

                foreach ($transferencia_itens as $itens_transferencia) {
                    foreach ($inventarios_base_destino as $destino_itens) {
                        $result = DB::table('inventarios')
                            ->join('transferencia_itens', 'transferencia_itens.item_id', '=', 'inventarios.item_id')
                            ->where('inventarios.item_id', $itens_transferencia->item_id)
                            ->where('inventarios.local_id', $destino_itens->local_id)
                            ->get();

                        if ($result->count() <= 0) {

                            $local = Local::where('id', $transferencia->base_destino_id)->first();

                            $novo_item = new Inventario();

                            $novo_item->departamento_id = $local->departamento_id;
                            $novo_item->item_id = $itens_transferencia->item_id;
                            $novo_item->local_id = $transferencia->base_destino_id;
                            $novo_item->quantidade += $itens_transferencia->quantidade;
                            $novo_item->qtd_alerta = 0;

                            $novo_item->save();
                        }
                    }
                }

                return response()->json([
                    'mesagem' => 'Tranferencia de materiais realizada com sucesso!',
                ], 200);
            } else {
                return response()->json([
                    'mensagem' => 'Você não possui cargo para aceitar transferencia.'
                ], 403);
            }
        } else {
            return response()->json([
                'mensagem' => 'Voce não pode aceitar transferencia de outra base.'
            ], 403);
        }
    }

    /**
     * Recusar Transferencia
     * @authenticated
     * 
     * @urlParam id integer required ID da transferencia. Example: 169
     * 
     * 
     * @response 200 {
     *      "mensagem": "Tranferencia recusada!"
     *      }
     */
    public function recusar_transferencia(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->hasRole(['almoxarife', 'encarregado'])) {
            $transferencia = TransferenciaDeMateriais::find($id);

            $localUsers = local_users::where('user_id', $user->id)->first();

            if ($localUsers->local_id == $transferencia->base_destino_id) {

                $transferencia->status = 'recusado';

                $transferencia->save();

                return response()->json([
                    'mensagem' => 'Transferecia Recusada.'
                ]);
            } else {
                return response()->json([
                    'mensagem' => 'Voce não pode recusar uma transferencia de outra base.'
                ], 403);
            }
        } else {
            return response()->json([
                'mensagem' => 'Voce não tem permissão para recusar uma transferencia'
            ], 401);
        }
    }

    /**
     * Mostra os itens de uma transferencia
     * @authenticated
     *
     * @urlParam id integer required ID da transferencia. Example: 2
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 3,
     *             "transferencia_materiais_id": 2,
     *             "item_id": 45,
     *             "item": "Luva,pvc Soldavel Marrom,c/diam.32mm",
     *             "medida": "PÇ",
     *             "quantidade": 10
     *         }
     *     ]
     * }
     */

    public function itens($id)
    {
        $transferencia_itens = TransferenciaItens::where("transferencia_materiais_id", "=", $id)->get();
        return TransferenciaDeMateriaisItemResource::collection($transferencia_itens);
    }
}