<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class MonthlyUsersChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build($chart)
    {
        return $this->chart->polarAreaChart()
            ->setTitle($chart['title'])
            ->setSubtitle($chart['sub'])
            ->addData($chart['data'])
            ->setLabels($chart['labels']);
    }
}
