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

    // Suppléments pour sandwichs, burgers, tacos...
    public array  $selectedSupplements    = [];
    public array  $supplementsDisponibles = [];

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

        // Charger les suppléments si le produit est éligible (burger, sandwich, tacos...)
        if ($this->produit->peutAvoirSupplements()) {
            $this->supplementsDisponibles = Produit::whereHas('categorie', fn($q) => $q->where('slug', 'supplements')->orWhere('id', 27))
                ->where('est_actif', true)
                ->orderBy('prix_vente')
                ->get(['id', 'nom', 'prix_vente', 'image_principale'])
                ->map(fn($s) => [
                    'id'         => $s->id,
                    'nom'        => $s->nom,
                    'prix_vente' => (float) $s->prix_vente,
                    'image'      => $s->image_principale,
                ])
                ->toArray();
        }
    }

    public function toggleSupplement(int $id): void
    {
        if (isset($this->selectedSupplements[$id])) {
            unset($this->selectedSupplements[$id]);
        } else {
            $this->selectedSupplements[$id] = true;
        }
        $this->added = false;
    }

    public function getSupplementTotalUnitaireProperty(): float
    {
        $sum = 0.0;
        foreach ($this->selectedSupplements as $id => $val) {
            if ($val) {
                $item = collect($this->supplementsDisponibles)->firstWhere('id', (int) $id);
                if ($item) {
                    $sum += (float) $item['prix_vente'];
                }
            }
        }
        return $sum;
    }

    public function getPrixUnitaireTotalProperty(): float
    {
        return (float) $this->produit->prixPourQuantite($this->quantity) + $this->supplementTotalUnitaire;
    }

    public function getPrixTotalLigneProperty(): float
    {
        return $this->prixUnitaireTotal * $this->quantity;
    }

    public function incrementQty(): void
    {
        if ($this->produit->sur_commande || $this->quantity < $this->produit->stock) {
            $this->quantity++;
            $this->added = false;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->added = false;
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

        // Récupérer les suppléments sélectionnés
        $supplementsChoisis = [];
        $totalSupplements = 0.0;
        foreach ($this->selectedSupplements as $suppId => $selected) {
            if ($selected) {
                $supp = collect($this->supplementsDisponibles)->firstWhere('id', (int) $suppId);
                if ($supp) {
                    $supplementsChoisis[] = [
                        'id'   => $supp['id'],
                        'nom'  => $supp['nom'],
                        'prix' => (float) $supp['prix_vente'],
                    ];
                    $totalSupplements += (float) $supp['prix_vente'];
                }
            }
        }

        // Clé unique pour le panier selon la combinaison de suppléments
        $suppIds = array_column($supplementsChoisis, 'id');
        sort($suppIds);
        $cleSuffix = ! empty($suppIds) ? '_sup_' . implode('_', $suppIds) : '';
        $cle = 'produit_' . $this->produit->id . $cleSuffix;

        $details = '';
        if (! empty($supplementsChoisis)) {
            $details = 'Supplément(s) : ' . implode(', ', array_map(fn($s) => $s['nom'] . ' (+' . number_format($s['prix'], 0, ',', ' ') . ' F)', $supplementsChoisis));
        }

        $prixUnitaire = (float) $this->produit->prixPourQuantite($this->quantity) + $totalSupplements;

        if (isset($panier[$cle])) {
            $nouvelle = $panier[$cle]['quantite'] + $this->quantity;
            $panier[$cle]['quantite'] = $this->produit->sur_commande ? $nouvelle : min($nouvelle, $this->produit->stock);
        } else {
            $panier[$cle] = [
                'produit_id'   => $this->produit->id,
                'nom'          => $this->produit->nom,
                'prix'         => $prixUnitaire,
                'prix_base'    => (float) $this->produit->prix_vente,
                'image'        => $this->produit->image_principale,
                'slug'         => $this->produit->slug,
                'quantite'     => $this->quantity,
                'stock'        => $this->produit->stock,
                'sur_commande' => (bool) $this->produit->sur_commande,
                'supplements'  => $supplementsChoisis,
                'details'      => $details,
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
