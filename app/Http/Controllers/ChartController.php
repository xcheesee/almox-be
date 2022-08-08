<?php

namespace App\Http\Controllers;

use App\Charts\ConsoleWarsGenre;
use App\Charts\MonthlyUsersChart;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, MonthlyUsersChart $chart1, ConsoleWarsGenre $chart2)
    {
        $mensagem = $request->session()->get('mensagem');
        $grafico = [
            'title' => 'Console Wars',
            'sub' => 'Vendas de Consoles na quarta geração (16-bits)',
            'data' => [45,30,10,8,7],
            'labels' => ['Super NES','Mega Drive','PC Engine','Neo Geo','Outros'],
        ];
        return view('chart.index', ['chart1' => $chart1->build($grafico),'chart2' => $chart2->build(),'mensagem'=>$mensagem]);
    }
}
