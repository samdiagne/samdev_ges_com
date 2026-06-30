<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Produit;
use App\Models\Parametre;

class CartDrawer extends Component
{
    // Variables alignées sur la vue
    public bool   $open   = false;
    public array  $panier = [];
    public float  $total  = 0;
    public int    $count  = 0;

    public function mount(): void
    {
        $this->syncCart();
    }

    #[On('open-cart')]
    public function openDrawer(): void
    {
        $this->syncCart();
        $this->open = true;
    }

    #[On('panier-mis-a-jour')]
    public function syncCart(): void
    {
        $this->panier = session('panier', []);
        $this->total  = round(collect($this->panier)->sum(fn($i) => $i['prix'] * $i['quantite']), 2);
        $this->count  = (int) collect($this->panier)->sum('quantite');
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function clear(): void
    {
        session(['panier' => []]);
        $this->syncCart();
        $this->dispatch('panier-mis-a-jour');
    }

    public function remove(string $key): void
    {
        $panier = session('panier', []);
        unset($panier[$key]);
        session(['panier' => $panier]);
        $this->syncCart();
        $this->dispatch('panier-mis-a-jour');
    }

    public function increment(string $key): void
    {
        $panier = session('panier', []);
        if (! isset($panier[$key])) return;

        $produit = Produit::find($panier[$key]['produit_id'] ?? null);
        if ($produit && $panier[$key]['quantite'] < $produit->stock) {
            $panier[$key]['quantite']++;
        }
        session(['panier' => $panier]);
        $this->syncCart();
        $this->dispatch('panier-mis-a-jour');
    }

    public function decrement(string $key): void
    {
        $panier = session('panier', []);
        if (! isset($panier[$key])) return;

        if ($panier[$key]['quantite'] <= 1) {
            unset($panier[$key]);
        } else {
            $panier[$key]['quantite']--;
        }
        session(['panier' => $panier]);
        $this->syncCart();
        $this->dispatch('panier-mis-a-jour');
    }

    public function render()
    {
        $parametres = Parametre::first();
        return view('livewire.shop.cart-drawer', compact('parametres'));
    }
}
