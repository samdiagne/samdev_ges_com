<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Plat;
use App\Models\Parametre;

class FichePlat extends Component
{
    public Plat $plat;

    // Variables alignées sur la vue
    public int    $quantity    = 1;
    public int    $activeImage = 0;
    public bool   $added       = false;
    public array  $allImages   = [];

    public function mount(Plat $plat): void
    {
        $this->plat = $plat;

        // Construire la liste de toutes les images (principale + galerie)
        $images = [];
        if ($plat->image_principale) {
            $images[] = $plat->image_principale;
        }
        foreach ($plat->images as $img) {
            $images[] = $img->chemin;
        }
        $this->allImages = $images;
    }

    public function incrementQty(): void
    {
        if ($this->plat->sur_commande || $this->quantity < $this->plat->stock) {
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
        if (! $this->plat->sur_commande && $this->plat->stock <= 0) return;

        $panier = session('panier', []);
        $cle    = 'plat_' . $this->plat->id;

        if (isset($panier[$cle])) {
            $nouvelle = $panier[$cle]['quantite'] + $this->quantity;
            $panier[$cle]['quantite'] = $this->plat->sur_commande ? $nouvelle : min($nouvelle, $this->plat->stock);
        } else {
            $panier[$cle] = [
                'plat_id'  => $this->plat->id,
                'nom'      => $this->plat->nom,
                'prix'     => (float) $this->plat->prix_vente,
                'image'    => $this->plat->image_principale,
                'slug'     => $this->plat->slug,
                'quantite' => $this->quantity,
                'stock'    => $this->plat->stock,
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
        return view('livewire.shop.fiche-plat', compact('parametres'));
    }
}
