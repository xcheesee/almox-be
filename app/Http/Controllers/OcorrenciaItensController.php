<?php

namespace App\Http\Controllers;

use App\Models\OcorrenciaItens;
use Illuminate\Http\Request;

class OcorrenciaItensController extends Controller
{
    public function index()
    {
        $item = OcorrenciaItens::all();

        return response()->json([
            'mensagem' => 'Todos itens de ocorrencias cadastrados',
            'itens' => $item
        ], 200);
    }

    public function show($id)
    {
        $item = OcorrenciaItens::where('id', $id)->first();

        if($item)
        {
            return response()->json([
                'mensagem' => 'Item de ocorrencia encontrado com sucesso!',
                'item' => $item
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Item de ocorrencia não encontrada!',
            ], 404);
        }

    }

    public function store(Request $request)
    {
        $item = new OcorrenciaItens();

        $item->ocorrencia_id = $request->ocorrencia_id;
        $item->item_id = $request->item_id;
        $item->quantidade = $request->quantidade;

        $item->save();

        return response()->json([
            'mensagem' => 'Item de ocorrencia cadastrado com sucesso!',
            'item' => $item
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $item = OcorrenciaItens::findOrFail($id);

        $item->ocorrencia_id = $request->ocorrencia_id;
        $item->item_id = $request->item_id;
        $item->quantidade = $request->quantidade;

        $item->update();

        return response()->json([
            'mensagem' => 'Item de ocorrencia editado com sucesso!',
            'item' => $item
        ], 200);
    }

    public function destroy($id)
    {
        $item = OcorrenciaItens::where('id', $id)->first();

        if($item)
        {
            $item->delete();

            return response()->json([
                'mensagem' => 'Ocorrencia deletada com sucesso!',
                'ocorrencia' => $item
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Ocorrencia não encontrada para deletar.',
            ], 404);
        }


    }
}
