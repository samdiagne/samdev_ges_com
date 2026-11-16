<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Produit;
use App\Models\Parametre;
use App\Services\CartService;

class CartDrawer extends Component
{
    public bool   $open   = false;
    public array  $panier = [];
    public float  $total  = 0;
    public int    $count  = 0;

    // Infos paliers groupe pour l'affichage (bannière)
    public array $infoPaliers = [];

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
        $panier = session('panier', []);

        // Rafraîchit stock et sur_commande depuis la base
        $produits = Produit::whereIn('id', collect($panier)->pluck('produit_id')->filter())
            ->get(['id', 'stock', 'sur_commande', 'categorie_id'])
            ->keyBy('id');

        foreach ($panier as $key => $item) {
            $produit = $produits->get($item['produit_id'] ?? null);
            if ($produit) {
                $panier[$key]['stock']        = $produit->stock;
                $panier[$key]['sur_commande'] = (bool) $produit->sur_commande;
                // S'assurer que categorie_id est présent (nécessaire pour paliers groupe)
                if (! isset($panier[$key]['categorie_id'])) {
                    $panier[$key]['categorie_id'] = $produit->categorie_id;
                }
            }
        }

        // ── Recalcul des paliers groupe ──────────────────────────────────────
        $cartSvc = app(CartService::class);
        $panier  = $cartSvc->recalculerPaliersGroupe($panier);

        // Infos paliers pour affichage bannière
        $this->infoPaliers = $cartSvc->infoPaliersGroupe($panier);

        // Sauvegarder le panier mis à jour en session
        session(['panier' => $panier]);

        $this->panier = $panier;
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
        if ($produit && ($produit->sur_commande || $panier[$key]['quantite'] < $produit->stock)) {
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
