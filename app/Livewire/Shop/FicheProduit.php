<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Produit;
use App\Models\Parametre;

class FicheProduit extends Component
{
    public Produit $produit;

    public int    $quantity    = 1;
    public int    $activeImage = 0;
    public bool   $added       = false;
    public array  $allImages   = [];

    public function mount(Produit $produit): void
    {
        $this->produit = $produit;

        $images = [];
        if ($produit->image_principale) {
            $images[] = $produit->image_principale;
        }
        foreach ($produit->images as $img) {
            $images[] = $img->chemin;
        }
        $this->allImages = $images;
    }

    public function incrementQty(): void
    {
        if ($this->produit->sur_commande || $this->quantity < $this->produit->stock) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function setImage(int $index): void
    {
        $count = count($this->allImages);
        if ($count === 0) return;
        $this->activeImage = (($index % $count) + $count) % $count;
    }

    public function addToCart(): void
    {
        if (! $this->produit->sur_commande && $this->produit->stock <= 0) return;

        $panier = session('panier', []);
        $cle    = 'produit_' . $this->produit->id;

        if (isset($panier[$cle])) {
            $nouvelle = $panier[$cle]['quantite'] + $this->quantity;
            $panier[$cle]['quantite'] = $this->produit->sur_commande ? $nouvelle : min($nouvelle, $this->produit->stock);
        } else {
            $panier[$cle] = [
                'produit_id' => $this->produit->id,
                'nom'        => $this->produit->nom,
                'prix'       => (float) $this->produit->prix_vente,
                'image'      => $this->produit->image_principale,
                'slug'       => $this->produit->slug,
                'quantite'   => $this->quantity,
                'stock'      => $this->produit->stock,
            ];
        }

        session(['panier' => $panier]);

        $this->added = true;
        $this->dispatch('open-cart');
        $this->dispatch('panier-mis-a-jour');
    }

    public function render()
    {
        $parametres = Parametre::first();
        return view('livewire.shop.fiche-produit', compact('parametres'));
    }
}
