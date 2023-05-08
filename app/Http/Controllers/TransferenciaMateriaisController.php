<?php

namespace App\Http\Controllers;

use App\Models\TransferenciaDeMateriais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TransferenciaMateriaisController extends Controller
{
    public function index()
    {
        $transferencia = TransferenciaDeMateriais::all();

        return response()->json([
            'mensagem' => 'Todas transferencias cadastradas',
            'transferencias' => $transferencia
        ], 200);
    }

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
