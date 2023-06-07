<?php

namespace App\Http\Controllers;

use App\Http\Requests\OcorrenciaFormRequest;
use App\Models\Inventario;
use App\Models\local_users;
use App\Models\OcorrenciaItens;
use App\Models\Ocorrencias;
use App\Models\TransferenciaDeMateriais;
use App\Models\TransferenciaItens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Ocorrencias as OcorrenciasResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Storage;

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
     * @response 200 {
     *      "mensagem": "Todas ocorrencias cadastradas",
     *      "ocorrencia": [
     *          {
     *              "id": 1,
     *              "local_id": 1,
     *              "data_ocorrencia": "2023-05-07 00:00:00",
     *              "tipo_ocorrencia": "extravio",
     *              "boletim_ocorrencia": "path",
     *              "justificativa": "justificativa_teste",
     *              "user_id": 1,
     *              "created_at": null,
     *              "updated_at": "2023-05-05T15:44:30.000000Z",
     *              "local": {
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
     *              }
     *        }    
     */
    public function index()
    {
        $ocorrencias = QueryBuilder::for(Ocorrencias::class)
        ->leftJoin('locais', 'locais.id', '=', 'ocorrencias.local_id')
        ->select("locais.nome as local", "ocorrencias.*")
        ->allowedSorts("data_ocorrencia", "local", "tipo_ocorrencia")
        ->allowedFilters([
            allowedFilter::partial("local", "locais.nome"),
            allowedFilter::scope('ocorrencia_depois_de'),
            allowedFilter::scope('ocorrencia_antes_de'),
            "tipo_ocorrencia"
        ])
        ->get();

        return OcorrenciasResource::collection($ocorrencias);
    }

    /**
     * Cadastra uma nova Ocorrencia.
     * @authenticated
     * 
     * @bodyParam local_id integer required ID do local. Example: 1
     * @bodyParam data_ocorrencia date required Data da Ocorrencia. Example: 2023/05/06
     * @bodyParam tipo_ocorrencia enum required Tipo da Ocorrencia. (avaria, furto, extravio) Example: furto
     * @bodyParam boletim_ocorrencia file required Arquivo do boletim de Ocorrencia
     * @bodyParam justificativa text required Campo para justificativa da Ocorrencia
     * @bodyParam itens[].ocorrencia_id integer required ID da entrada. Example: 3
     * @bodyParam itens[].item_id integer required ID do item. Example: 5
     * @bodyParam itens[].quantidade integer required Quantidade de itens. Example: 401
     * 
     * @response 200 {
     *      "mensagem": "Ocorrencia cadastrada com sucesso!",
     *      "ocorrencia": {
     *          "local_id": 1,
     *          "data_ocorrencia": "2023-05-04",
     *          "tipo_ocorrencia": "furto",
     *          "boletim_ocorrencia": "teste",
     *          "justificativa": "justificativa_teste",
     *          "user_id": 1,
     *          "updated_at": "2023-05-10T15:20:14.000000Z",
     *          "created_at": "2023-05-10T15:20:14.000000Z",
     *          "id": 5
     *      },
     *      "itens": [
     *          {
     *              "ocorrencia_id": 3,
     *              "item_id": 5,
     *              "quantidade": 255
     *          },
     *      ]
     * }
     */
    public function store(OcorrenciaFormRequest $request)
    {
        $user = auth()->user();

        if ($user->hasRole(['almoxarife', 'encarregado'])){
            
            $ocorrencia = new Ocorrencias();
                    
            $ocorrencia->local_id = $request->input('local_id');
            $ocorrencia->data_ocorrencia = $request->input('data_ocorrencia');
            $ocorrencia->tipo_ocorrencia = $request->input('tipo_ocorrencia');
            if ($request->input('tipo_ocorrencia') == 'furto' or $request->input('tipo_ocorrencia') == 'extravio'){
                $this->validate($request, [
                    'boletim_ocorrencia' => 'required'
                ]);
            } else {
                $ocorrencia->boletim_ocorrencia = $request->input('boletim_ocorrencia');
            }
            $ocorrencia->justificativa = $request->input('justificativa');
            $ocorrencia->user_id = Auth::user()->id;
        
            if ($request->hasFile('boletim_ocorrencia')){
                $tabela=DB::select("SHOW TABLE STATUS LIKE 'ocorrencias'");
                $next_id=$tabela[0]->Auto_increment;
                $file = $request->file('boletim_ocorrencia');
                $extension = $file->extension();
                
                $upload = $request->file('boletim_ocorrencia')->storeAs('files','ocorrencias'.$next_id.'-'.date('Ymdhis').'.'.$extension);
                $ocorrencia->boletim_ocorrencia = $upload;
            }
            
            DB::beginTransaction();
            
            if($ocorrencia->save()){
                
                $localUsers = local_users::where('user_id', $user->id)->first();
                
                if(!($localUsers->local_id == $ocorrencia->local_id)){

                        return response()->json([
                            'mensagem' => 'Você não pode cadastrar uma ocorrencia de outra base.'
                        ], 401);
                    }
                    
            $itens = $request->input('itens');
                    
            foreach($itens as $item) {
                $ocorrenciaItem = new OcorrenciaItens();
                
                $ocorrenciaItem->ocorrencia_id = $ocorrencia->id;
                
                if (array_key_exists('id', $item)){
                    $ocorrenciaItem->item_id = $item["id"];
                } else {
                    DB::rollBack();
                    
                    return response()->json([
                        'mesagem' => "id do item não informado, Ocorrencia não cadastrada."
                    ], 420);
                }
                
                if (array_key_exists('quantidade', $item)){
                    $ocorrenciaItem->quantidade = $item["quantidade"];
                } else {
                    DB::rollBack();
                    
                    return response()->json([
                        'mensagem' => "quantidade não informado, Ocorrencia não cadastrada."
                    ], 420);
                }
                
                $ocorrenciaItem->save();
            }
                    
                $iventarios_local = Inventario::where('local_id', $ocorrencia->local_id)->get();   
                
                foreach ($iventarios_local as $iventario_local) {

                $itensOcorrencia = OcorrenciaItens::where('ocorrencia_id', $ocorrencia->id)->get();
                                
                foreach ($itensOcorrencia as $item_ocorrencia) {
                    if ($item_ocorrencia->item_id == $iventario_local->item_id){

                        $iventario_local->quantidade -= $item_ocorrencia->quantidade;

                        $iventario_local->save();
                    }
                }
            }       
                DB::commit();
                            
                return response()->json([
                    'mensagem' => "Ocorrencia cadastrada com sucesso!",
                    'Ocorrencia' => $ocorrencia,
                    'itens' => $itens
                ], 200);
            }
        } else {
            return response()->json([
                'mensagem' => "Você não possui permissão para cadastrar um ocorrencia."
            ], 401);
        }

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
     *          "id": 20,
     *          "local_id": 1,
     *          "data_ocorrencia": "2023-05-04 00:00:00",
     *          "tipo_ocorrencia": "furto",
     *          "boletim_ocorrencia": "teste",
     *          "justificativa": "teste kk",
     *          "user_id": 1,
     *          "created_at": "2023-05-12T12:34:44.000000Z",
     *          "updated_at": "2023-05-12T12:34:44.000000Z",
     *          "local": {
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
     *          }
     *      }
     *  }
     * 
     * @response 404 {
     *      "mensagem": "Ocorrencia não encontrada!"
     *      }
     */
    public function show($id)
    {
        $ocorrencia = Ocorrencias::with('local')->where('id', $id)->first();

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

    public function mostrar_boletim_ocorrencia($id)
    {
        $ocorrencia = Ocorrencias::findOrFail($id);

        $caminhoArquivo = $ocorrencia->boletim_ocorrencia;

        return Storage::download($caminhoArquivo);
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
