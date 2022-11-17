<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use App\Http\Requests\ResponsaveisEmailFormRequest;
use App\Models\ResponsaveisEmail;
use App\Http\Resources\ResponsaveisEmail as ResponsaveisEmailResource;
use Illuminate\Http\Request;

class ResponsaveisEmailController extends Controller
{
    /**
     * Lista os responsaveis_emails
     * @authenticated
     *
     */
    public function index(Request $request)
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        if ($is_api_request){
            $responsaveis_emails = ResponsaveisEmail::get();
            return ResponsaveisEmailResource::collection($responsaveis_emails);
        }

        $responsaveis_emails = ResponsaveisEmail::query()->orderBy('id')->get();
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.responsaveis_emails.index', compact('responsaveis_emails','mensagem'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.responsaveis_emails.create',compact('mensagem','userDeptos'));
    }

    /**
     * Cadastra uma responsaveis_email
     * @authenticated
     *
     *
     * @bodyParam nome string required Nome do responsaveis_email. Example: João Pedro Silva
     * @bodyParam profissao string required Profissão/Cargo. Example: Arquiteto
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": "2",
     *         "departamento": "GABINETE/NDTIC",
     *         "nome": "João Pedro Silva",
     *         "email": "joaopedro@email.com"
     *     }
     * }
     */
    public function store(ResponsaveisEmailFormRequest $request)
    {
        $responsaveis_email = new ResponsaveisEmail();
        $responsaveis_email->departamento_id = $request->input('departamento_id');
        $responsaveis_email->nome = $request->input('nome');
        $responsaveis_email->email = $request->input('email');

        if ($responsaveis_email->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request) {
                return new ResponsaveisEmailResource($responsaveis_email);
            }

            $request->session()->flash('mensagem',"Responsável '{$responsaveis_email->nome}' - '{$responsaveis_email->email}' criado(a) com sucesso, ID {$responsaveis_email->id}.");
            return redirect()->route('cadaux-responsaveis_emails');
        }
    }

    /**
     * Mostra uma responsaveis_email
     * @authenticated
     *
     * @urlParam id integer required ID de responsaveis_email. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": "2",
     *         "departamento": "GABINETE/NDTIC",
     *         "nome": "João Pedro Silva",
     *         "email": "joaopedro@email.com"
     *     }
     * }
     */
    public function show($id)
    {
        $responsaveis_email= ResponsaveisEmail::findOrFail($id);
        return new ResponsaveisEmailResource($responsaveis_email);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $responsaveis_email = ResponsaveisEmail::findOrFail($id);
        $user = auth()->user();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');
        $mensagem = $request->session()->get('mensagem');
        return view ('cadaux.responsaveis_emails.edit',compact('mensagem','responsaveis_email','userDeptos'));
    }

    /**
     * Edita uma responsaveis_email
     * @authenticated
     *
     *
     * @urlParam id integer required ID da responsaveis_email que deseja editar. Example: 1
     *
     * @bodyParam profissao string required Tipo. Example: peça
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "departamento_id": "2",
     *         "departamento": "GABINETE/NDTIC",
     *         "nome": "João Pedro Silva",
     *         "email": "joaopedro@email.com"
     *     }
     * }
     */
    public function update(ResponsaveisEmailFormRequest $request, $id)
    {
        $responsaveis_email = ResponsaveisEmail::findOrFail($id);
        $responsaveis_email->departamento_id = $request->input('departamento_id');
        $responsaveis_email->nome = $request->input('nome');
        $responsaveis_email->email = $request->input('email');

        if ($responsaveis_email->save()) {
            $is_api_request = in_array('api',$request->route()->getAction('middleware'));
            if ($is_api_request){
                return new ResponsaveisEmailResource($responsaveis_email);
            }

            return response()->json(['mensagem' => "ResponsaveisEmail '{$responsaveis_email->nome}' - ID {$responsaveis_email->id} editado(a) com sucesso!"], 200);
        }
    }

    /**
     * Deleta uma responsaveis_email
     * @authenticated
     *
     *
     * @urlParam id integer required ID da responsaveis_email que deseja deletar. Example: 1
     *
     * @response 200 {
     *     "message": "responsaveis_email deletada com sucesso!",
     *     "data": {
     *         "id": 1,
     *         "departamento_id": "2",
     *         "departamento": "GABINETE/NDTIC",
     *         "nome": "João Pedro Silva",
     *         "email": "joaopedro@email.com"
     *     }
     * }
     */
    public function destroy($id)
    {
        $responsaveis_email= ResponsaveisEmail::findOrFail($id);

        if ($responsaveis_email->delete()) {
            return response()->json([
                'message' => 'ResponsaveisEmail deletado(a) com sucesso!',
                'data' => new ResponsaveisEmailResource($responsaveis_email)
            ]);
        }
    }
}
