<?php

namespace App\Http\Controllers;

use App\Helpers\DepartamentoHelper;
use App\Models\Departamento;
use App\Models\DepartamentoUsuario;
use App\Models\Local;
use App\Models\local_users;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
        //  $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        //  $this->middleware('permission:user-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::query()->where('ativo','=',1)->orderBy('id', 'asc')->paginate(7);

        $mensagem = $request->session()->get('mensagem');
        return view('users.index', compact('data','mensagem'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        $departamentos = Departamento::pluck('nome', 'id')->all();
        $locais = Local::all();

        return view('users.create', compact('roles', 'departamentos', 'locais'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        //salvando os departamentos do usuário
        foreach($input['departamentos'] as $departamento){
            $depto_user = new DepartamentoUsuario();
            $depto_user->departamento_id = $departamento;
            $depto_user->user_id = $user->id;
            $depto_user->save();
        }

        $localUsuario = new local_users();

        $localUsuario->local_id = $request->input('local_usuario');
        $localUsuario->user_id = $user->id;

        $localUsuario->save();

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $userRole = $user->roles->pluck('name')->all();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'nome',false);

        $localUsers = DB::table('locais')
                    ->join('local_users', 'local_users.local_id', '=', 'locais.id')
                    ->where('user_id', $user->id)
                    ->select('nome')
                    ->first();
                    
        return view('users.show', compact('user','userRole','userDeptos', 'localUsers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $departamentos = Departamento::pluck('nome', 'id')->all();
        $localUsers = local_users::where('user_id', $user->id)->first();
        $locais = Local::all();
        $userDeptos = DepartamentoHelper::deptosByUser($user,'id',false);

        return view('users.edit', compact('user', 'roles', 'userRole', 'departamentos', 'userDeptos', 'locais', 'localUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'confirmed',
            'roles' => 'required'
        ]);

        $input = $request->all();

        if(!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = User::find($id);
        $user->update($input);

        DB::table('model_has_roles')
            ->where('model_id', $id)
            ->delete();

        $user->assignRole($request->input('roles'));

        //salvando os departamentos do usuário
        DB::table('departamento_usuarios')
            ->where('user_id', $id)
            ->delete();
        foreach($input['departamentos'] as $departamento){
            $depto_user = new DepartamentoUsuario();
            $depto_user->departamento_id = $departamento;
            $depto_user->user_id = $id;
            $depto_user->save();
        }


        $localUsers = local_users::where('user_id', $user->id)->first();

        if($localUsers == null){
            $localUsuario = new local_users();

            $localUsuario->local_id = $request->input('local_usuario');
            $localUsuario->user_id = $user->id;

            $localUsuario->save();
        } else {
            $localUsers->local_id = $request->input('local_usuario');
    
            $localUsers->save();
        }
        

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //User::find($id)->delete();
        $user = User::find($id);
        $user->ativo = 0;
        $user->save();

        DB::table('departamento_usuarios')
            ->where('user_id', $id)
            ->delete();

        DB::table('local_users')
        ->where('user_id', $user->id)
        ->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Exibe formulário de troca de senha
     *
     * @return \view\login\index.blade.php
     */
    public function trocasenha()
    {
        return view ('users.trocasenha');
    }

    /**
     * Exibe formulário de login
     *
     * @param  Request $request
     * @return redirect home
     */
    public function alterarsenha(Request $request)
    {
        //dd($request); exit();
        if (!Auth::attempt($request->only(['email','password']))){
            return redirect()->back()->withErrors('Senha Atual incorreta');
        }

        if($request->newpassword !== $request->password_confirmation){
            return redirect()->back()->withErrors('A confirmação da nova senha não confere');
        }

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->newpassword);
        $user->save();
        $request->session()->flash('mensagem',"Senha alterada com sucesso");
        return redirect()->route('home');
    }
}
