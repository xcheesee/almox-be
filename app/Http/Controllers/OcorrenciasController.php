<?php

namespace App\Http\Controllers;

use App\Models\Ocorrencias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OcorrenciasController extends Controller
{
    public function index()
    {
        $ocorrencia = Ocorrencias::all();

        return response()->json([
            'mensagem' => 'Todas ocorrencias cadastradas',
            'ocorrencia' => $ocorrencia
        ]);
    }

    public function store(Request $request)
    {
        $ocorrencia = new Ocorrencias();

        $ocorrencia->local_id = $request->local_id;
        $ocorrencia->data_ocorrencia = $request->data_ocorrencia;
        $ocorrencia->tipo_ocorrencia = $request->tipo_ocorrencia;
        $ocorrencia->boletim_ocorrencia = $request->boletim_ocorrencia;
        $ocorrencia->justificativa = $request->justificativa;
        $ocorrencia->user_id = Auth::user()->id;

        $ocorrencia->save();

        return response()->json([
            'mensagem' => 'Ocorrencia cadastrada com sucesso!',
            'ocorrencia' => $ocorrencia
        ], 200);
        }
    

    public function show($id)
    {
        $ocorrencia = Ocorrencias::where('id', $id)->first();

        if($ocorrencia)
        {
            return response()->json([
                'mensagem' => 'Ocorrencia encontrada com sucesso!',
                'ocorrencia' => $ocorrencia
            ]);
        } else {
            return response()->json([
                'mensagem' => 'Item de transferencia não encontrada!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $ocorrencia = Ocorrencias::findOrFail($id);
        
        $ocorrencia->local_id = $request->local_id;
        $ocorrencia->data_ocorrencia = $request->data_ocorrencia;
        $ocorrencia->tipo_ocorrencia = $request->tipo_ocorrencia;
        $ocorrencia->boletim_ocorrencia = $request->boletim_ocorrencia;
        $ocorrencia->justificativa = $request->justificativa;

        $ocorrencia->update();
        
        return response()->json([
            'mensagem' => 'Item de transferencia atualizado com sucesso!',
            'ocorrencia' => $ocorrencia
        ]);
    }

    public function destroy($id)
    {
        $ocorrencia = Ocorrencias::where('id', $id)->first();

        
        if($ocorrencia)
        {
            $ocorrencia->delete();

            return response()->json([
                'mensagem' => 'Item de transferencia deletado com sucesso!',
                'ocorrencia' => $ocorrencia
            ]);
        } else {
            return response()->json([
                'mensagem' => 'Item de transferencia não encontrado para deletar.',
            ]);
        }
    }
}
