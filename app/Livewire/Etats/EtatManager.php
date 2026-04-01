<?php

namespace App\Livewire\Etats;

use Livewire\Component;
use Carbon\Carbon;

class EtatManager extends Component
{
    public string $dateFrom = '';
    public string $dateTo   = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public function setQuickPeriod(string $period): void
    {
        $this->dateFrom = match($period) {
            'today'     => now()->format('Y-m-d'),
            'week'      => now()->startOfWeek()->format('Y-m-d'),
            'month'     => now()->startOfMonth()->format('Y-m-d'),
            'last_month'=> now()->subMonth()->startOfMonth()->format('Y-m-d'),
            'year'      => now()->startOfYear()->format('Y-m-d'),
            default     => now()->startOfMonth()->format('Y-m-d'),
        };

        $this->dateTo = match($period) {
            'last_month'=> now()->subMonth()->endOfMonth()->format('Y-m-d'),
            default     => now()->format('Y-m-d'),
        };
    }

    public function render()
    {
        return view('livewire.etats.etat-manager');
    }
}