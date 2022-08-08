<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class ConsoleWarsGenre
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\BarChart
    {
        return $this->chart->barChart()
            ->setTitle('Genres by Consoles')
            ->setSubtitle('Gêneros de jogos por console da quarta geração (16-bit)')
            ->addData('SNES', [6, 9, 3, 4, 10, 5])
            ->addData('MD', [7, 3, 8, 8, 6, 10])
            ->addData('PCE', [5, 8, 10, 3, 5, 6])
            ->setXAxis(['Platform', 'RPG', 'Shmup', 'Sports', 'Fighting', 'Arcade']);
    }
}
