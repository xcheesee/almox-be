<?php

namespace App\Http\Controllers;

use App\Models\Ocorrencias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Ocorrencias
 * 
 * APIs para listar, cadastrar, editar e remover dados de Ocorrencias.
 */
class OcorrenciasController extends Controller
{
    /**
     * Lista todas as Ocorrencias
     * @authenticated
     * 
     */
    public function index()
    {
        $ocorrencia = Ocorrencias::all();

        return response()->json([
            'mensagem' => 'Todas ocorrencias cadastradas',
            'ocorrencia' => $ocorrencia
        ], 200);
    }

    /**
     * Cadastra uma nova Transferencia.
     * @authenticated
     * 
     * @bodyParam local_id integer required ID do local. Example: 1
     * @bodyParam data_ocorrencia date required Data da Ocorrencia. Example: 2023/05/06
     * @bodyParam tipo_ocorrencia enum required Tipo da Ocorrencia. (avaria, furto, extravio) Example: furto
     * @bodyParam boletim_ocorrencia file required Arquivo do boletim de Ocorrencia
     * @bodyParam justificativa text required Campo para justificativa da Ocorrencia
     * 
     * @response 200 {
     *      "mensagem": "Ocorrencia cadastrada com sucesso!",
     *      "ocorrencia": {
     *          "local_id": 1,
     *          "data_ocorrencia": "2023-05-04",
     *          "tipo_ocorrencia": "furto",
     *          "boletim_ocorrencia": "teste",
     *          "justificativa": "teste kk",
     *          "user_id": 1,
     *          "updated_at": "2023-05-10T15:20:14.000000Z",
     *          "created_at": "2023-05-10T15:20:14.000000Z",
     *          "id": 5
     *      }
     * }
     */
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
    

        /**
     * Mostrar uma Ocorrencia
     * @authenticated
     * 
     * @urlParam id integer required ID da Ocorrencia. Example: 2
     * 
     * @response 200 {
     *      "mensagem": "Ocorrencia encontrada com sucesso!",
     *      "ocorrencia": {
     *          "id": 2,
     *          "local_id": 1,
     *          "data_ocorrencia": "2023-05-04 00:00:00",
     *          "tipo_ocorrencia": "furto",
     *          "boletim_ocorrencia": "path_teste",
     *          "justificativa": "foda kk",
     *          "user_id": 1,
     *          "created_at": "2023-05-05T15:47:04.000000Z",
     *          "updated_at": "2023-05-05T15:47:04.000000Z"
     *      }
     * }
     * 
     * @response 404 {
     *      "mensagem": "Ocorrencia não encontrada!"
     *      }
     */
    public function show($id)
    {
        $ocorrencia = Ocorrencias::where('id', $id)->first();

        if($ocorrencia)
        {
            return response()->json([
                'mensagem' => 'Ocorrencia encontrada com sucesso!',
                'ocorrencia' => $ocorrencia
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Ocorrencia não encontrada!',
            ], 404);
        }
    }

    /**
     * Edita uma Ocorrencia.
     * @authenticated
     * 
     * @urlParam id integer required ID da Ocorrencia. Example: 6
     * 
     * @bodyParam local_id integer required ID do local. Example: 1
     * @bodyParam data_ocorrencia date required Data da Ocorrencia. Example: 2023/05/06
     * @bodyParam tipo_ocorrencia enum required Tipo da Ocorrencia. (avaria, furto, extravio) Example: furto
     * @bodyParam boletim_ocorrencia file required Arquivo do boletim de Ocorrencia
     * @bodyParam justificativa text required Campo para justificativa da Ocorrencia. Example:"Teste" 
     * 
     * @response 200 {
     *      "mensagem": "Ocorrencia atualizada com sucesso!",
     *      "ocorrencia": {
     *          "local_id": 1,
     *          "data_ocorrencia": "2023-05-04",
     *          "tipo_ocorrencia": "furto",
     *          "boletim_ocorrencia": "teste",
     *          "justificativa": "teste kk",
     *          "user_id": 1,
     *          "updated_at": "2023-05-10T15:20:14.000000Z",
     *          "created_at": "2023-05-10T15:20:14.000000Z",
     *          "id": 5
     *      }
     * }
     */
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
            'mensagem' => 'Ocorrencia atualizada com sucesso!',
            'ocorrencia' => $ocorrencia
        ], 200);
    }

    /**
     * Deletar uma Ocorrencia
     * @authenticated
     *
     *
     * @urlParam id integer required ID da Ocorrencia que deseja deletar. Example: 5
     *
     * @response 200 {
     *     "mensagem": "Ocorrencia deletada com sucesso!",
     *      "ocorrencia": {
     *          "id": 6,
     *          "local_id": 1,
     *          "data_ocorrencia": "2023-05-04 00:00:00",
     *          "tipo_ocorrencia": "furto",
     *          "boletim_ocorrencia": "teste",
     *          "justificativa": "teste kk",
     *          "user_id": 1,
     *          "created_at": "2023-05-10T15:28:10.000000Z",
     *          "updated_at": "2023-05-10T15:28:10.000000Z"
     *      }
     *}
      
     * @response 404 {
     *     "mensagem": "Transferencia não encontrada para deletar!"
     *     }
     */
    public function destroy($id)
    {
        $ocorrencia = Ocorrencias::where('id', $id)->first();

        
        if($ocorrencia)
        {
            $ocorrencia->delete();

            return response()->json([
                'mensagem' => 'Ocorrencia deletada com sucesso!',
                'ocorrencia' => $ocorrencia
            ], 200);
        } else {
            return response()->json([
                'mensagem' => 'Ocorrencia não encontrada para deletar.',
            ], 404);
        }
    }
}
