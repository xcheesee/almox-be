<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function index()
    {
        return view ('login.index');
    }

    public function entrar(Request $request)
    {
        if (!Auth::attempt($request->only(['email','password']))){
            return redirect()->back()->withErrors('Usuário e/ou senha incorretos');
        }
        //Auth::user();
        return redirect()->route('home');
    }
}

