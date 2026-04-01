<?php

namespace App\Livewire\Shop;

use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $open = false;

    #[On('open-cart')]
    public function openDrawer(): void
    {
        $this->open = true;
    }

    #[On('cart-updated')]
    public function refresh(): void {}

    public function close(): void
    {
        $this->open = false;
    }

    public function increment(string $key): void
    {
        $cart = session('cart', []);
        if (!isset($cart[$key])) return;

        if ($cart[$key]['quantity'] < $cart[$key]['stock']) {
            $cart[$key]['quantity']++;
        }

        session(['cart' => $cart]);
    }

    public function decrement(string $key): void
    {
        $cart = session('cart', []);
        if (!isset($cart[$key])) return;

        if ($cart[$key]['quantity'] > 1) {
            $cart[$key]['quantity']--;
        } else {
            unset($cart[$key]);
        }

        session(['cart' => $cart]);
        $this->dispatch('cart-updated');
    }

    public function remove(string $key): void
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);
        $this->dispatch('cart-updated');
    }

    public function clear(): void
    {
        session(['cart' => []]);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $cart  = session('cart', []);
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $count = collect($cart)->sum('quantity');

        return view('livewire.shop.cart-drawer', compact('cart', 'total', 'count'));
    }
}
