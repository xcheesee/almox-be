<?php

namespace App\Http\Controllers;

use App\Models\TransferenciaDeMateriais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     */
    public function index()
    {
        $transferencia = TransferenciaDeMateriais::all();

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
     * 
     *
     * @response 200 {
     *      {
     *    "mensagem": "Transferencia cadastrada com sucesso!",
     *    "transferencia": {
     *          "base_origem_id": 1,
     *          "base_destino_id": 2,
     *          "data_transferencia": "2023/05/05",
     *          "status": enviado,
     *          "user_id": 1,
     *          "observacao": "Teste Observação",
     *          "observacao_motivo": "itens_faltando",
     *          "observacao_user_id": 1,
     *          "updated_at": "2023-05-09T15:23:32.000000Z",
     *          "created_at": "2023-05-09T15:23:32.000000Z",
     *          "id": 9
     *      }
     * }
     */
    public function store(Request $request)
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

        $transferencia->save();

        return response()->json([
            'mensagem' => 'Transferencia cadastrada com sucesso!',
            'transferencia' => $transferencia
        ], 200);
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
     *          "id": 5,
     *          "base_origem_id": 2,
     *          "base_destino_id": 1,
     *          "data_transferencia": "2023-05-07 00:00:00",
     *          "status": "recebido",
     *          "user_id": 1,
     *          "observacao": "Teste Observação testado",
     *          "observacao_motivo": "extravio",
     *          "observacao_user_id": 1,
     *          "created_at": "2023-05-05T12:54:06.000000Z",
     *          "updated_at": "2023-05-05T12:57:59.000000Z"
     *      }
     * }
     * 
     * @response 404 {
     *      "mensagem": "Transferencia naõ encontrada!"
     *      }
     */
    public function show($id)
    {
        $transferencia = TransferenciaDeMateriais::where('id', $id)->first();

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
    public function update(Request $request, $id)
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
