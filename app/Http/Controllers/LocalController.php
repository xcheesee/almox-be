<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Http\Resources\Local as LocalResource;

/**
 * @group Local
 *
 * APIs para listar, cadastrar, editar e remover dados de locais
 */

class LocalController extends Controller
{
    /**
     * Lista os locais
     * @authenticated
     *
     */
    public function index()
    {
        $locais = Local::paginate(15);
            return LocalResource::collection($locais);
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
     * Cadastra um local
     * @authenticated
     *
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam nome string required Nome. Example: "teste"
     * @bodyParam tipo enum required ('base', 'parque', 'autarquia', 'secretaria', 'subprefeitura') Tipo. Example: base
     * @bodyParam cep string nullable Cep. Example: 12345-678
     * @bodyParam logradouro string nullable Logradouro. Example: Rua do Paraiso
     * @bodyParam numero string nullable Numero. Example: 387
     * @bodyParam bairro string nullable Bairro. Example: Paraiso
     * @bodyParam cidade string nullable Cidade. Example: São Paulo
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function store(Request $request)
    {
        $local = new Local();
        $local->departamento_id = $request->input('departamento_id');
        $local->nome = $request->input('nome');
        $local->tipo = $request->input('tipo');
        $local->cep = $request->input('cep');
        $local->logradouro = $request->input('logradouro');
        $local->numero = $request->input('numero');
        $local->bairro = $request->input('bairro');
        $local->cidade = $request->input('cidade');

        if ($local->save()) {
            return new LocalResource($local);
        }
    }

    /**
     * Mostra um local
     * @authenticated
     *
     * @urlParam id integer required ID de local. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function show($id)
    {
        $local= Local::findOrFail($id);
        return new LocalResource($local);
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
     * Edita um Local
     * @authenticated
     *
     *
     * @urlParam id integer required ID do local que deseja editar. Example: 1
     *
     * @bodyParam departamento_id integer ID do Departamento. Example: 2
     * @bodyParam nome string required Nome. Example: "teste"
     * @bodyParam tipo enum required ('base', 'parque', 'autarquia', 'secretaria', 'subprefeitura') Tipo. Example: base
     * @bodyParam cep string nullable Cep. Example: 12345-678
     * @bodyParam logradouro string nullable Logradouro. Example: Rua do Paraiso
     * @bodyParam numero string nullable Numero. Example: 387
     * @bodyParam bairro string nullable Bairro. Example: Paraiso
     * @bodyParam cidade string nullable Cidade. Example: São Paulo
     *  
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function update(Request $request, $id)
    {
        $local = Item::findOrFail($id);
        $local->departamento_id = $request->input('departamento_id');
        $local->nome = $request->input('nome');
        $local->tipo = $request->input('tipo');
        $local->cep = $request->input('cep');
        $local->logradouro = $request->input('logradouro');
        $local->numero = $request->input('numero');
        $local->bairro = $request->input('bairro');
        $local->cidade = $request->input('cidade');

        if ($local->save()) {
            return new LocalResource($local);
        }
    }

    /**
     * Deleta um local
     * @authenticated
     *
     *
     * @urlParam id integer required ID do local que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "item deletado com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": 2,
     *         "nome": "teste",
     *         "tipo": "base",
     *         "cep": "12345-678",
     *         "logradouro": "Rua do Paraiso",
     *         "numero": "387",
     *         "bairro": "Paraiso",
     *         "cidade": "São Paulo"
     *     }
     * }
     */
    public function destroy($id)
    {
        $local = Local::findOrFail($id);

        if ($local->delete()) {
            return response()->json([
                'message' => 'Local deletado com sucesso!',
                'data' => new LocalResource($local)
            ]);
        }
    }    
}