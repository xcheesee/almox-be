<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\DepartamentoHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * @group Autenticação
 *
 * APIs para login e logout do sistema
 */
class AuthController extends Controller
{
    /**
     * Cria um novo usuário no sistema
     * @authenticated
     *
     *
     * @bodyParam email string required E-mail corporativo do usuário. Example: tisvma@prefeitura.sp.gov.br
     * @bodyParam password string required Senha do usuário. Example: Teste!123
     *
     * @response 200 {
     *     "data": {
     *         "name": "Teste Silva",
     *         "email": "teste@prefeitura.sp.gov.br",
     *         "updated_at": "2022-05-20T16:00:22.000000Z",
     *         "created_at": "2022-05-20T16:00:22.000000Z",
     *         "id": 2
     *     },
     *     "access_token": "3|LWV2yqNvqdIztktLYlvKehKMFn4aCOvKWc7xqMGf",
     *     "token_type": "Bearer"
     * }
     */
    public function cadastrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Efetua login no sistema
     * @unauthenticated
     *
     *
     * @bodyParam email string required E-mail corporativo do usuário. Example: tisvma@prefeitura.sp.gov.br
     * @bodyParam password string required Senha do usuário. Example: Teste!123
     *
     * @response 200 {
     *     "message": "Oi username, bem-vindo!",
     *     "username": "username",
     *     "id": 1,
     *     "access_token": "1|IEXWeQ8KFQCu3d3giZbTJ7dOTNf9dSACPypztMB3",
     *     "token_type": "Bearer",
     *     "departamentos": {
     *         "3": "CGPABI",
     *         "4": "CGPABI/DIPO",
     *         "5": "CGPABI/DFS",
     *     }
     * }
     *
     * @response 401 {
     *     "message": "E-mail ou senha está incorreto"
     * }
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'E-mail ou senha está incorreto'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome');

        return response()->json([
            'message' => 'Oi '.$user->name.', bem-vindo!',
            'username' => $user->name,
            'id' => $user->id,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'departamentos' => $userDeptos,
        ]);
    }

    /**
     * Altera a senha do usuário
     * @authenticated
     *
     * @header Authorization Bearer 5|02KLXZaRYzgJybyy2rMTRKXKIOuuE3EylnT7JQVv
     *
     * @bodyParam email string required E-mail corporativo do usuário. Example: tisvma@prefeitura.sp.gov.br
     * @bodyParam password string required Senha atual do usuário. Example: Teste!123
     * @bodyParam newpassword string required Senha nova do usuário. Example: Teste!456
     * @bodyParam password_confirmation string required Confirmação de nova senha do usuário. Example: Teste!456
     *
     * @response 200 {
     *     "message": "Senha alterada com sucesso!"
     * }
     *
     * @response 401 {
     *     "message":"Unauthenticated."
     * }
     */
    public function alterar_senha(Request $request)
    {
        //dd($request); exit();
        if (!Auth::attempt($request->only(['email','password']))){
            return response()->json(['message' => 'Senha Atual incorreta'], 401);
        }

        if($request->newpassword !== $request->password_confirmation){
            return response()->json(['message' => 'A confirmação da nova senha não confere'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $user->password = Hash::make($request->newpassword);
        $user->save();
        return response()->json(['message' => 'Senha alterada com sucesso'], 200);
    }

    /**
     * Efetua logout no sistema e remove os tokens associados ao usuário
     * @authenticated
     *
     * @header Authorization Bearer 5|02KLXZaRYzgJybyy2rMTRKXKIOuuE3EylnT7JQVv
     *
     * @response 200 {
     *     "message": "Você realizou o logout com sucesso e o token foi deletado com sucesso!"
     * }
     *
     * @response 401 {
     *     "message":"Unauthenticated."
     * }
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Você realizou o logout com sucesso e o token foi deletado com sucesso!'
        ];
    }
}
