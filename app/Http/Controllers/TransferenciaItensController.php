<?php

namespace App\Http\Controllers;

use App\Models\TransferenciaItens;
use Illuminate\Http\Request;

class TransferenciaItensController extends Controller
{
    public function index()
    {
        $itens = TransferenciaItens::all();

        return response()->json([
            'mensagem' => 'Todos itens de transfrerencia cadastrados',
            'itens' => $itens
        ]);
    }

    public function store(Request $request)
    {
        $itens = new TransferenciaItens();

        $itens->entrada_id = $request->entrada_id;
        $itens->item_id = $request->item_id;
        $itens->quantidade = $request->quantidade;

        $itens->save();

        return response()->json([
            'mensagem' => 'Item de transferencia cadastrado com sucesso!',
            'itens' => $itens
        ]);
        }
    

    public function show($id)
    {
        $itens = TransferenciaItens::where('id', $id)->first();

        if($itens)
        {
            return response()->json([
                'mensagem' => 'Item de transferencia encontrado com sucesso!',
                'itens' => $itens
            ]);
        } else {
            return response()->json([
                'mensagem' => 'Item de transferencia não encontrada!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $itens = TransferenciaItens::findOrFail($id);

        $itens->entrada_id = $request->entrada_id;
        $itens->item_id = $request->item_id;
        $itens->quantidade = $request->quantidade;

        
        $itens->update();
        
        return response()->json([
            'mensagem' => 'Item de transferencia atualizado com sucesso!',
            'itens' => $itens
        ]);
    }

    public function destroy($id)
    {
        $itens = TransferenciaItens::where('id', $id)->first();

        
        if($itens)
        {
            $itens->delete();

            return response()->json([
                'mensagem' => 'Item de transferencia deletado com sucesso!',
                'itens' => $itens
            ]);
        } else {
            return response()->json([
                'mensagem' => 'Item de transferencia não encontrado para deletar.',
            ]);
        }
    }
}
