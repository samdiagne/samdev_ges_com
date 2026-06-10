<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\On;

class CartIcon extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('panier-mis-a-jour')]
    public function refreshCount(): void
    {
        $this->count = (int) collect(session('panier', []))->sum('quantite');
    }

    public function render()
    {
        return view('livewire.shop.cart-icon');
    }
}
