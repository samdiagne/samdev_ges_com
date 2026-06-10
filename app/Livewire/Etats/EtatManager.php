<?php

namespace App\Livewire\Etats;

use Livewire\Component;

class EtatManager extends Component
{
    public string $dateFrom = '';
    public string $dateTo   = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.etats.etat-manager');
    }
}
