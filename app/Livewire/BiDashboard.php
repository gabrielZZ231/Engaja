<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\BI\KpiService;
use App\Services\BI\ResponsesService;
use App\Services\BI\AnalfabetismoService;


class BiDashboard extends Component
{
    public int $period = 30;

    public function render()
    {
        return view('livewire.bi-dashboard', [
            'kpis' => KpiService::overview($this->period),
            'chartData' => ResponsesService::byDay($this->period),
            'analfabetismoChart' => AnalfabetismoService::taxaPorMunicipio(),
        ]);
    }

    public function updatedPeriod()
    {
        $data = ResponsesService::byDay($this->period);

        $this->dispatch('refreshChart', $data);
    }
}
