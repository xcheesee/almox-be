<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferenciaFormRequest;
use App\Models\TransferenciaDeMateriais;
use App\Models\TransferenciaItens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $transferencia = TransferenciaDeMateriais::with('base_origem_id', 'base_destino_id')->get();

        return response()->json([
            'mensagem' => 'Todas transferencias cadastradas',
            'transferencias' => $transferencia
        ], 200);
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
        $transferencia = new TransferenciaDeMateriais();

        $transferencia->base_origem_id = $request->base_origem_id;
        $transferencia->base_destino_id = $request->base_destino_id;
        $transferencia->data_transferencia = $request->data_transferencia;
        $transferencia->status = $request->status;
        $transferencia->user_id = Auth::user()->id;
        $transferencia->observacao = $request->observacao;
        $transferencia->observacao_motivo = $request->observacao_motivo;
        $transferencia->observacao_user_id = Auth::user()->id;

        DB::beginTransaction();

        if($transferencia->save()){
            $itens = $request->input('itens');
            foreach($itens as $item) {
                $transferenciaItem = new TransferenciaItens();

                $transferenciaItem->transferencia_materiais_id = $transferencia->id;

                if (array_key_exists('item_id', $item)) {
                    $transferenciaItem->item_id = $item["item_id"];
                } else {
                    DB::rollBack();

                    return response()->json([
                        'mensagem' => "item_id não informado, Transferencia não cadastrada."
                    ], 420);
                }

                if (array_key_exists('quantidade', $item)){
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
        };

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
        $transferencia = TransferenciaDeMateriais::with('base_origem_id', 'base_destino_id', 'itens_da_transferencia')->findOrFail($id);

        if($transferencia)
        {
            return response()->json([
                'mensagem' => 'Transferencia encontrada com sucesso!',
                'transferencia' => $transferencia
            ], 200);
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
    public function update(TransferenciaFormRequest $request, $id)
    {
        $transferencia = TransferenciaDeMateriais::findOrFail($id);

        $transferencia->base_origem_id = $request->base_origem_id;
        $transferencia->base_destino_id = $request->base_destino_id;
        $transferencia->data_transferencia = $request->data_transferencia;
        $transferencia->status = $request->status;
        $transferencia->user_id = Auth::user()->id;
        $transferencia->observacao = $request->observacao;
        $transferencia->observacao_motivo = $request->observacao_motivo;
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

        
        if($transferencia)
        {
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
}
