<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use Illuminate\Http\Request;
use App\Http\Requests\EntradaFormRequest;
use App\Http\Requests\EntradaUpdateFormRequest;
use App\Models\Entrada;
use App\Models\EntradaItem;
use App\Models\Inventario;
use App\Http\Resources\Entrada as EntradaResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group Entrada
 *
 * APIs para listar, cadastrar, editar e remover dados de entrada
 */

class EntradaController extends Controller
{
    /**
     * Lista as entradas
     * @authenticated
     *
     * @queryParam filter[processo_sei] Filtro de número do processo. Example: 6000.2022/0000123-4
     * @queryParam filter[numero_contrato] Filtro de número do contrato da entrada Example: 001/SVMA/2022
     * @queryParam filter[local] Filtro de nome da base destino dos materiais da entrada. Example: Base Leopoldina
     * @queryParam filter[numero_nota_fiscal] Filtro do número da nota fiscal. Example: 1234567
     * @queryParam filter[entrada_depois_de] Filtro inicial de período da data de entrada do contrato. Example: 2023-01-01
     * @queryParam filter[entrada_antes_de] Filtro final de período da data de entrada do contrato. Example: 2023-12-31
     * @queryParam filter[tipo] Tipo de material da entrada. Example: alvenaria
     * @queryParam sort Campo a ser ordenado (padrão ascendente, inserir um hífen antes para decrescente). Colunas possíveis: 'numero_nota_fiscal', 'data_entrada', 'processo_sei', 'numero_contrato', 'locais.nome', 'tipo_items.nome' Example: -processo_sei
     *
     */
    public function index()
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);

        $entradas = QueryBuilder::for(Entrada::class)
        ->select('locais.nome', 'tipo_items.nome', 'entradas.*')
        ->leftJoin('locais', 'locais.id', 'entradas.local_id')
        ->leftJoin('tipo_items', 'tipo_items.id', 'entradas.tipo_item_id')
        ->where('entradas.ativo','=',1)
        ->whereIn('entradas.departamento_id',$userDeptos)
        ->allowedFilters([
                'processo_sei', 'numero_contrato', 'numero_nota_fiscal',
                AllowedFilter::partial('local','locais.nome'),
                AllowedFilter::partial('tipo','tipo_items.nome'),
                AllowedFilter::scope('entrada_depois_de'),
                AllowedFilter::scope('entrada_antes_de'),
            ])
        ->allowedSorts('numero_nota_fiscal', 'processo_sei', 'data_entrada', 'numero_contrato', 'numero_contrato')
        ->paginate(15);

        return EntradaResource::collection($entradas);
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
     * Cadastra uma nova entrada
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do departamento. Example: 2
     * @bodyParam local_id integer ID do local. Example: 2
     * @bodyParam data_entrada date required Data do serviço. Example: "2022-08-11"
     * @bodyParam processo_sei string required Processo SEI. Example: 0123000134569000
     * @bodyParam numero_contrato string required Número do contrato. Example: 0001SVMA2022
     * @bodyParam numero_nota_fiscal string required Número da Nota Fiscal. Example: 1234
     * @bodyParam arquivo_nota_fiscal file nullable Arquivo da Nota Fiscal.
     * @bodyParam entrada_items object Lista de itens. Example: [{"id": 1, "quantidade": 500},{"id": 2, "quantidade": 480}]
     * @bodyParam entrada_items[].id integer ID do item. Example: 2
     * @bodyParam entrada_items[].quantidade integer Quantidade informada para o item. Example: 480
     *
     *
     * {
     *       "departamento_id": 2,
     *       "local_id": 2,
     *       "data_entrada": "2022-08-11",
     *       "processo_sei": "0123000134569000",
     *       "numero_contrato": "0001SVMA2022",
     *       "numero_nota_fiscal": "1234",
     *       "arquivo_nota_fiscal": "DANFE?"
     *       "entrada_items": [
     *           {
     *               "id": 1,
     *               "quantidade": 500
     *           },
     *           {
     *               "id": 2,
     *               "quantidade": 480
     *           }
     *       ]
     *   }
     */
    public function store(EntradaFormRequest $request)
    {
        $entrada = new Entrada();
        $entrada->departamento_id = $request->input('departamento_id');
        $entrada->local_id = $request->input('local_id');
        $entrada->data_entrada = $request->input('data_entrada');
        $entrada->processo_sei = $request->input('processo_sei');
        $entrada->numero_contrato = $request->input('numero_contrato');
        $entrada->numero_nota_fiscal = $request->input('numero_nota_fiscal');

        // Lidando com o upload de arquivo
        if ($request->hasFile('arquivo_nota_fiscal')){
            $tabela=DB::select("SHOW TABLE STATUS LIKE 'entradas'");
            $next_id=$tabela[0]->Auto_increment;
            $file = $request->file('arquivo_nota_fiscal');
            $extension = $file->extension();

            $upload = $request->file('arquivo_nota_fiscal')->storeAs('files','entrada_'.$next_id.'-'.date('Ymdhis').'.'.$extension);
            $entrada->arquivo_nota_fiscal = $upload;
        }

        if ($entrada->save()) {
            // Lidando com os itens adicionados
            $entradaItens = $request->input('entrada_items');
            if ($entradaItens){
                foreach ($entradaItens as $entrada_items){
                    //Salvando item na tabela entrada_items
                    $entrada_item = new EntradaItem();
                    $entrada_item->entrada_id = $entrada->id;
                    $entrada_item->item_id = $entrada_items["id"];
                    $entrada_item->quantidade = $entrada_items["quantidade"];
                    $entrada_item->save();

                    //lógica para adicionar a quantidade dos itens de entrada no inventario
                    $inventario = Inventario::where('departamento_id','=',$entrada->departamento_id)
                                            ->where('local_id','=',$entrada->local_id)
                                            ->where('item_id','=',$entrada_items["id"])
                                            ->first();

                    if ($inventario) {
                        $inventario->quantidade += $entrada_items["quantidade"];
                        $inventario->save();
                    } else {
                        $inventario = new Inventario();
                        $inventario->departamento_id = $entrada->departamento_id;
                        $inventario->item_id = $entrada_items["id"];
                        $inventario->local_id = $entrada->local_id;
                        $inventario->quantidade = $entrada_items["quantidade"];
                        $inventario->qtd_alerta = 0;
                        $inventario->save();
                    }
                }
            }
            return new EntradaResource($entrada);
        }
    }

    /**
     * Mostra uma entrada específica
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "local_id": 2,
     *         "data_entrada": "2022-08-11",
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "files/entrada_294-20220909055100.pdf",
     *         "arquivo_nota_fiscal_url": "http://localhost:8000/storage/files/entrada_294-20220909055100.pdf"
     *     }
     * }
     */
    public function show($id)
    {
        $entrada = Entrada::findOrFail($id);
        return new EntradaResource($entrada);
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
     * Edita uma entrada
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do departamento. Example: 2
     * @bodyParam local_id integer ID do local. Example: 2
     * @bodyParam data_entrada date required Data do serviço. Example: "2022-08-11"
     * @bodyParam processo_sei string required Processo SEI. Example: 0123000134569000
     * @bodyParam numero_contrato string required Número do contrato. Example: 2343rbte67b63
     * @bodyParam numero_nota_fiscal string required Número da Nota Fiscal. Example: 1234
     * @bodyParam arquivo_nota_fiscal string required Arquivo da Nota Fiscal. Example: DANFE?
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "local_id": 2,
     *         "data_entrada": "2022-08-11",
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "DANFE?"
     *     }
     * }
     */
    public function update(EntradaUpdateFormRequest $request, $id)
    {
        //dd($request);
        $entrada = Entrada::findOrFail($id);
        $entrada->departamento_id = $request->input('departamento_id');
        $entrada->local_id = $request->input('local_id');
        $entrada->data_entrada = $request->input('data_entrada');
        $entrada->processo_sei = $request->input('processo_sei');
        $entrada->numero_contrato = $request->input('numero_contrato');
        $entrada->numero_nota_fiscal = $request->input('numero_nota_fiscal');

        // Lidando com o upload de arquivo
        if ($request->hasFile('arquivo_nota_fiscal')){
            if($entrada->arquivo_nota_fiscal){
                Storage::delete($entrada->arquivo_nota_fiscal);
            }

            $file = $request->file('arquivo_nota_fiscal');
            $extension = $file->extension();

            $upload = $request->file('arquivo_nota_fiscal')->storeAs('files','entrada_'.$entrada->id.'-'.date('Ymdhis').'.'.$extension);
            $entrada->arquivo_nota_fiscal = $upload;
        }

        if ($entrada->save()) {
            // Lidando com os itens adicionados
            $entradaItens = $request->input('entrada_items');
            if ($entradaItens){
                foreach ($entradaItens as $entrada_items){
                    // Atualizando item na tabela entrada_items
                    $entrada_item = EntradaItem::query()->where('item_id','=',$entrada_items["item_id"])->first();

                    if ($entrada_item) {
                        $entrada_item->entrada_id = $entrada->id;
                        $entrada_item->item_id = $entrada_items["item_id"];
                        $entrada_item->quantidade = $entrada_items["quantidade"];
                        $entrada_item->save();
                    } else {
                        // Criando item na tabela entrada_items
                        $entrada_item = new EntradaItem();
                        $entrada_item->entrada_id = $entrada->id;
                        $entrada_item->item_id = $entrada_items["item_id"];
                        $entrada_item->quantidade = $entrada_items["quantidade"];
                        $entrada_item->save();
                    }

                    // lógica para atualizar ou adicionar a quantidade dos itens no inventario
                    $inventario = Inventario::where('departamento_id','=',$entrada->departamento_id)
                                            ->where('local_id','=',$entrada->local_id)
                                            ->where('item_id','=',$entrada_items["item_id"])
                                            ->first();

                    if ($inventario) {
                        $inventario->quantidade = $entrada_items["quantidade"];
                        $inventario->save();
                    } else {
                        $inventario = new Inventario();
                        $inventario->departamento_id = $entrada->departamento_id;
                        $inventario->item_id = $entrada_items["item_id"];
                        $inventario->local_id = $entrada->local_id;
                        $inventario->quantidade = $entrada_items["quantidade"];
                        $inventario->qtd_alerta = 0;
                        $inventario->save();
                    }
                }
            }

            return new EntradaResource($entrada);
        }
    }

    /**
     * Deleta uma entrada
     * @authenticated
     *
     *
     * @urlParam id integer required ID da entrada que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "entrada deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "local_id": 2,
     *         "data_entrada": "2022-08-11",
     *         "processo_sei": "0123000134569000",
     *         "numero_contrato": "2343rbte67b63",
     *         "numero_nota_fiscal": "1234",
     *         "arquivo_nota_fiscal": "files/entrada_294-20220909055100.pdf",
     *         "arquivo_nota_fiscal_url": "http://localhost:8000/storage/files/entrada_294-20220909055100.pdf"
     *     }
     * }
     */
    public function destroy($id)
    {
        $entrada = Entrada::findOrFail($id);
        /**
         * O seguinte código é para remoção "lógica", apenas setamos o ativo=0 e reduzimos a quantidade do item no inventário
         */
        if ($entrada->ativo == 0){
            return response()->json([
                'message' => 'Entrada inativa na base de dados do sistema; a mesma foi removida anteriormente.'
            ], 410);
        }
        $entrada->ativo = 0;
        $entrada->save();

        $entrada_items = EntradaItem::where('entrada_id','=',$id)->get();
        //dd($entrada_items);
        foreach($entrada_items as $item){
            //lógica para retirar a quantidade dos itens no inventario
            $saida_inventario = Inventario::query()->where('local_id','=',$entrada->local_id)
                ->where('departamento_id','=',$entrada->departamento_id)
                ->where('item_id','=',$item->item_id)->first();

            if ($saida_inventario) {
                $saida_inventario->quantidade -= $item->quantidade;
                $saida_inventario->save();
            }
        }

        return response()->json([
            'message' => 'Entrada deletada com sucesso! Items referentes à entrada foram removidos do inventário',
            'data' => new EntradaResource($entrada)
        ]);

        /**
         * O código abaixo é para remoção "física", ou seja, os registros referentes a entrada serão apagados da base.
         * Por hora usaremos apenas o delete lógico, mas de qualquer forma é preciso remover os itens do inventário
         */
        // $entrada_items = EntradaItem::where('entrada_id','=',$id)->get();
        // foreach($entrada_items as $item){
        //     $item->delete();
        //
        // }
        // Storage::delete($entrada->arquivo_nota_fiscal);

        // if ($entrada->delete()) {
        //     return response()->json([
        //         'message' => 'Entrada deletada com sucesso!',
        //         'data' => new EntradaResource($entrada)
        //     ]);
        // }
    }
}
