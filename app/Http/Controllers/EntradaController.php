<?php

namespace App\Http\Controllers;

use App\Helpers\BasesUsuariosHelper;
use App\Helpers\DepartamentoHelper;
use App\Helpers\LocalHelper;
use App\Helpers\TipoItemHelper;
use Illuminate\Http\Request;
use App\Http\Requests\EntradaFormRequest;
use App\Http\Requests\EntradaUpdateFormRequest;
use App\Models\Entrada;
use App\Models\EntradaItem;
use App\Models\Inventario;
use App\Http\Resources\Entrada as EntradaResource;
use App\Http\Resources\EntradaItem as EntradaItemResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\Historico;
use App\Models\Local;
use App\Models\TipoItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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
        ->allowedSorts('numero_nota_fiscal', 'processo_sei', 'data_entrada', 'tipo_items.nome', 'locais.nome', 'numero_contrato')
        ->paginate(15);

        return EntradaResource::collection($entradas);
    }

    public function index_web(Request $request)
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::ids_deptos($user);
        $filtros = array();
        $filtros['local'] = $request->query('f-local');
        $filtros['tipo'] = $request->query('f-tipo');
        $filtros['processo_sei'] = $request->query('f-processo_sei');
        $filtros['numero_contrato'] = $request->query('f-numero_contrato');
        $filtros['numero_nota_fiscal'] = $request->query('f-numero_nota_fiscal');
        $filtros['entrada_depois_de'] = $request->query('f-entrada_depois_de');
        $filtros['entrada_antes_de'] = $request->query('f-entrada_antes_de');

        $entradas = Entrada::query()
        ->select('locais.nome', 'tipo_items.nome', 'entradas.*')
        ->leftJoin('locais', 'locais.id', 'entradas.local_id')
        ->leftJoin('tipo_items', 'tipo_items.id', 'entradas.tipo_item_id')
        ->where('entradas.ativo','=',1)
        ->whereIn('entradas.departamento_id',$userDeptos)
        ->when($filtros['local'], function ($query, $val) {
            return $query->where('locais.nome','like','%'.$val.'%');
        })
        ->when($filtros['tipo'], function ($query, $val) {
            return $query->where('tipo_items.nome','like','%'.$val.'%');
        })
        ->when($filtros['processo_sei'], function ($query, $val) {
            return $query->where('entradas.processo_sei','like','%'.$val.'%');
        })
        ->when($filtros['numero_contrato'], function ($query, $val) {
            return $query->where('entradas.numero_contrato','like','%'.$val.'%');
        })
        ->when($filtros['numero_nota_fiscal'], function ($query, $val) {
            return $query->where('entradas.numero_nota_fiscal','like','%'.$val.'%');
        })
        ->when($filtros['entrada_depois_de'], function ($query, $val) {
            $date = Carbon::createFromFormat('d/m/Y', $val);
            $data = $date->format("Y-m-d");
            return $query->where('entradas.data_entrada','>=',$data);
        })
        ->when($filtros['entrada_antes_de'], function ($query, $val) {
            $date = Carbon::createFromFormat('d/m/Y', $val);
            $data = $date->format("Y-m-d");
            return $query->where('entradas.data_entrada','<=',$data);
        })
        ->sortable()
        ->paginate(15);

        $tipo_items = TipoItem::whereIn('departamento_id',$userDeptos)->get();

        $mensagem = $request->session()->get('mensagem');
        return view('entradas.index', compact('entradas','tipo_items','mensagem','filtros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $departamentos = DepartamentoHelper::deptosByUser($user,'nome');
        $locais = LocalHelper::dropDownList(DepartamentoHelper::ids_deptos($user),'base');
        $tipo_items = TipoItemHelper::dropDownList(DepartamentoHelper::ids_deptos($user));
        $mensagem = $request->session()->get('mensagem');
        return view ('entradas.create',compact('departamentos','locais','tipo_items','mensagem'));
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
        //dd($request);
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        $user = auth()->user();
        $localUser = BasesUsuariosHelper::ExibirIdsBasesUsuarios($user->id);
        
        $entrada = new Entrada();
        $entrada->departamento_id = $request->input('departamento_id');
        if (in_array($request->input('local_id'), $localUser)) {
            $entrada->local_id = $request->input('local_id');
        } else {
            return response()->json([
                'error' => "Você deve selecionar uma base em que esteja cadastrado."
            ]);
        }
        $entrada->data_entrada = $is_api_request ? $request->input('data_entrada') : Carbon::createFromFormat('d/m/Y', $request->input('data_entrada'));
        $entrada->processo_sei = str_replace([".","/","-"],"",$request->input('processo_sei'));
        $entrada->numero_contrato = str_replace([".","/","-"],"",$request->input('numero_contrato'));
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

        DB::beginTransaction();
        if ($entrada->save()) {
            // Lidando com os itens adicionados
            if($is_api_request){
                $entradaItens = json_decode($request->input('entrada_items'), true);
            }else{
                $entradaItens = $request->input('entrada_items');
            }


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

            // Salva na tabela historicos
            $historico = new Historico();
            $historico->nome_tabela = 'Entrada';
            $historico->data_acao = date("Y-m-d");
            $historico->tipo_acao = 'criacao';
            $historico->user_id = Auth::user()->id;
            $historico->registro = json_encode(new EntradaResource($entrada));
            $historico->save();

            DB::commit();

            if($is_api_request){
                return new EntradaResource($entrada);
            }
            $request->session()->flash('mensagem',"Entrada de Material #{$entrada->id} criada com sucesso");
            return redirect()->route('entradas');
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
    public function show(Request $request, $id)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        $entrada = Entrada::findOrFail($id);

        if($is_api_request){
            return new EntradaResource($entrada);
        }

        $entrada_items = EntradaItem::where('entrada_id','=',$id)->get();
        return response()->json([
            'entrada' => new EntradaResource($entrada),
            'entrada_items' => EntradaItemResource::collection($entrada_items)]
            , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $entrada = Entrada::findOrFail($id);
        $entrada_items = EntradaItem::query()->where('entrada_id','=',$id)->get();
        $itens_adicionados = array();
        foreach($entrada_items as $item){
            $itens_adicionados[]= $item->item_id;
        }
        $itens_adicionados = implode(",",$itens_adicionados);

        $user = auth()->user();
        $departamentos = DepartamentoHelper::deptosByUser($user,'nome');
        $locais = LocalHelper::dropDownList(DepartamentoHelper::ids_deptos($user),'base');
        $tipo_items = TipoItemHelper::dropDownList(DepartamentoHelper::ids_deptos($user));
        $mensagem = $request->session()->get('mensagem');
        return view ('entradas.edit',compact('entrada','entrada_items','itens_adicionados','departamentos','locais','tipo_items','mensagem'));
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
        $user = auth()->user();
        $localUser = BasesUsuariosHelper::ExibirIdsBasesUsuarios($user->id);
        $is_api_request = in_array('api', $request->route()->getAction('middleware'));
        
        $entrada = Entrada::findOrFail($id);
        $entrada->departamento_id = $request->input('departamento_id');
        if (in_array($request->input('local_id'), $localUser)) {
            $entrada->local_id = $request->input('local_id');
        } else {
            return response()->json([
                'error' => "Você deve selecionar uma base em que esteja cadastrado."
            ]);
        }
        $entrada->data_entrada = $is_api_request ? $request->input('data_entrada') : Carbon::createFromFormat('d/m/Y', $request->input('data_entrada'));
        $entrada->processo_sei = str_replace([".", "/", "-"], "", $request->input('processo_sei'));
        $entrada->numero_contrato = str_replace([".", "/", "-"], "", $request->input('numero_contrato'));
        $entrada->numero_nota_fiscal = $request->input('numero_nota_fiscal');

        // Lidando com o upload de arquivo
        if ($request->hasFile('arquivo_nota_fiscal')) {
            if ($entrada->arquivo_nota_fiscal) {
                Storage::delete($entrada->arquivo_nota_fiscal);
            }

            $file = $request->file('arquivo_nota_fiscal');
            $extension = $file->extension();

            $upload = $request->file('arquivo_nota_fiscal')->storeAs('files', 'entrada_' . $entrada->id . '-' . date('Ymdhis') . '.' . $extension);
            $entrada->arquivo_nota_fiscal = $upload;
        }

        DB::beginTransaction();
        if ($entrada->save()) {
            // deletando itens para readicionar a nova lista
            $entrada_items = EntradaItem::where('entrada_id', '=', $id)->get();

            //esta parte esta apagando todos os items da tabela entrada_items e devolvendo a quantidade para o iventario
            foreach ($entrada_items as $item) {
                //lógica para remover a quantidade dos itens no inventario
                $saida_inventario = Inventario::query()->where('local_id', '=', $entrada->local_id)
                    ->where('departamento_id', '=', $entrada->departamento_id)
                    ->where('item_id', '=', $item->item_id)->first();

                if ($saida_inventario) {
                    $saida_inventario->quantidade -= $item->quantidade;
                    $saida_inventario->save();
                }

                $item->delete();
            }

            // Lidando com os itens adicionados
            $entradaItens = json_decode($request->input('entrada_items'), true);
            if ($entradaItens) {
                foreach ($entradaItens as $entrada_items) {
                    //Salvando item na tabela entrada_items
                    $entrada_item = new EntradaItem();
                    $entrada_item->entrada_id = $entrada->id;
                    $entrada_item->item_id = $entrada_items["id"];
                    $entrada_item->quantidade = $entrada_items["quantidade"];
                    $entrada_item->save();

                    //lógica para remover a quantidade dos itens de entrada no inventario
                    $inventario = Inventario::where('departamento_id', '=', $entrada->departamento_id)
                        ->where('local_id', '=', $entrada->local_id)
                        ->where('item_id', '=', $entrada_items["id"])
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

            // Salva na tabela historicos
            $historico = new Historico();
            $historico->nome_tabela = 'Entrada';
            $historico->data_acao = date("Y-m-d");
            $historico->tipo_acao = 'atualizacao';
            $historico->user_id = Auth::user()->id;
            $historico->registro = json_encode(new EntradaResource($entrada));
            $historico->save();

            DB::commit();


            if ($is_api_request) {
                return new EntradaResource($entrada);
            }
            $request->session()->flash('mensagem', "Entrada de Material #{$entrada->id} editada com sucesso");
            return redirect()->route('entradas');
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

        // Salva na tabela historicos
        $historico = new Historico();
        $historico->nome_tabela = 'Entrada';
        $historico->data_acao = date("Y-m-d");
        $historico->tipo_acao = 'exclusao';
        $historico->user_id = Auth::user()->id;
        $historico->registro = json_encode(new EntradaResource($entrada));
        $historico->save();

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
