<?php

namespace App\Livewire\Shop;

use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    #[On('cart-updated')]
    public function refresh(): void {}

    public function render()
    {
        $count = collect(session('cart', []))->sum('quantity');
        return view('livewire.shop.cart-icon', compact('count'));
    }
}