<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $mensagem = $request->session()->get('mensagem');
        //return view ('series.index', compact('series','mensagem'));
        return view('home', compact('mensagem'));
        //return view('home');
    }

    public function welcome(Request $request)
    {
        $ee = $request->query('ee');
        if($ee == 'HORIZONS'){
            $ee = 1;
        }else{
            $ee = 0;
        }
        return view('welcome', compact('ee'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function admin(Request $request)
    {
        $mensagem = $request->session()->get('mensagem');
        //return view ('series.index', compact('series','mensagem'));
        return view('admin', compact('mensagem'));
        //return view('home');
    }

    /**
     * Show cadaux index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function cadaux(Request $request)
    {
        $mensagem = $request->session()->get('mensagem');
        return view('cadaux.index', compact('mensagem'));
    }
}
