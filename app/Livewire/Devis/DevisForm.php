<?php

namespace App\Livewire\Devis;

use Livewire\Component;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Categorie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DevisForm extends Component
{
    public bool $isEditing  = false;
    public ?int $quoteId    = null;

    public ?int   $clientId         = null;
    public string $clientName       = '';
    public string $searchClient     = '';
    public array  $clientResults    = [];
    public bool   $useQuickClient   = false;
    public string $quickClientName  = '';
    public string $quickClientPhone = '';

    public string $notes       = '';
    public string $valid_until = '';
    public float  $discount    = 0;

    public string $searchProduit   = '';
    public string $filterCategorie = '';
    public array  $allProduits     = [];

    public array $items = [];

    public bool   $showFreeLineModal         = false;
    public bool   $showOptionsModal          = false;
    public bool   $afficherConfirmationVider = false;
    public string $freeLineDescription       = '';
    public float  $freeLinePrice             = 0;
    public int    $freeLineQty               = 1;

    public float $sousTotal = 0;
    public float $total     = 0;

    public function mount(?int $quoteId = null): void
    {
        $this->valid_until = now()->addDays(30)->format('Y-m-d');
        $this->loadProduits();

        if ($quoteId) {
            $this->isEditing = true;
            $this->quoteId   = $quoteId;
            $this->loadQuote($quoteId);
        }
    }

    public function loadProduits(): void
    {
        $this->allProduits = Produit::where('est_actif', true)
            ->where(fn($q) => $q->where('stock', '>', 0)->orWhere('sur_commande', true))
            ->with('categorie')
            ->when($this->filterCategorie, fn($q) =>
                $q->where('categorie_id', $this->filterCategorie)
            )
            ->when($this->searchProduit, fn($q) =>
                $q->where('nom', 'like', '%' . $this->searchProduit . '%')
            )
            ->orderBy('nom')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'nom'          => $p->nom,
                'prix_vente'   => (float) $p->prix_vente,
                'stock'        => $p->stock,
                'sur_commande' => (bool) $p->sur_commande,
                'image'        => $p->image_principale,
                'categorie'    => $p->categorie?->nom,
                'categorie_id' => $p->categorie_id,
            ])
            ->toArray();
    }

    public function updatedFilterCategorie(): void { $this->loadProduits(); }
    public function updatedSearchProduit(): void { $this->loadProduits(); }

    private function loadQuote(int $id): void
    {
        $quote = Quote::with('items')->findOrFail($id);

        $this->clientId    = $quote->client_id;
        $this->clientName  = $quote->client?->nom ?? $quote->client_nom ?? '';
        $this->notes       = $quote->notes ?? '';
        $this->valid_until = $quote->valide_jusqua ? $quote->valide_jusqua->format('Y-m-d') : '';
        $this->discount    = (float) $quote->remise;

        $this->items = $quote->items->map(fn($item) => [
            'produit_id'    => $item->produit_id,
            'description'   => $item->description,
            'prix_unitaire' => (float) $item->prix_unitaire,
            'quantite'      => (int) $item->quantite,
            'remise'        => (float) $item->remise,
            'total'         => (float) $item->total,
            'is_free_line'  => (bool) $item->is_free_line,
        ])->toArray();

        $this->recalculateTotals();
    }

    public function addToQuote(int $produitId): void
    {
        foreach ($this->items as $index => $item) {
            if (($item['produit_id'] ?? null) === $produitId) {
                $this->updateQuantity($index, $item['quantite'] + 1);
                return;
            }
        }
        $this->addProduitItem($produitId);
    }

    public function toggleProduit(int $produitId): void
    {
        $this->addToQuote($produitId);
    }

    public function addProduitItem(int $produitId): void
    {
        $produit = Produit::findOrFail($produitId);

        $this->items[] = [
            'produit_id'    => $produit->id,
            'description'   => $produit->nom,
            'prix_unitaire' => (float) $produit->prix_vente,
            'quantite'      => 1,
            'remise'        => 0,
            'total'         => (float) $produit->prix_vente,
            'is_free_line'  => false,
        ];

        $this->recalculateTotals();
    }

    public function openFreeLineModal(): void
    {
        $this->freeLineDescription = '';
        $this->freeLinePrice       = 0;
        $this->freeLineQty         = 1;
        $this->showFreeLineModal   = true;
    }

    public function closeFreeLineModal(): void
    {
        $this->showFreeLineModal = false;
    }

    public function addFreeLine(): void
    {
        if (trim($this->freeLineDescription) === '' || $this->freeLinePrice <= 0) {
            return;
        }

        $qty = max(1, (int) $this->freeLineQty);

        $this->items[] = [
            'produit_id'    => null,
            'description'   => trim($this->freeLineDescription),
            'prix_unitaire' => (float) $this->freeLinePrice,
            'quantite'      => $qty,
            'remise'        => 0,
            'total'         => round((float) $this->freeLinePrice * $qty, 2),
            'is_free_line'  => true,
        ];

        $this->freeLineDescription = '';
        $this->freeLinePrice       = 0;
        $this->freeLineQty         = 1;
        $this->showFreeLineModal   = false;
        $this->recalculateTotals();
    }

    public function updateQuantity(int $index, $qty): void
    {
        $qty = (int) $qty;
        if ($qty <= 0) {
            $this->removeItem($index);
            return;
        }

        if (isset($this->items[$index])) {
            $this->items[$index]['quantite'] = $qty;
            $this->recalculateItem($index);
            $this->recalculateTotals();
        }
    }

    public function removeItem(int $index): void
    {
        if (isset($this->items[$index])) {
            array_splice($this->items, $index, 1);
            $this->items = array_values($this->items);
            $this->recalculateTotals();
        }
    }

    public function clearQuote(): void
    {
        $this->items                     = [];
        $this->sousTotal                 = 0;
        $this->total                     = 0;
        $this->discount                  = 0;
        $this->afficherConfirmationVider = false;
    }

    private function recalculateItem(int $index): void
    {
        if (!isset($this->items[$index])) return;

        $item = $this->items[$index];
        $this->items[$index]['total'] = round(
            $item['prix_unitaire'] * $item['quantite'] - ($item['remise'] ?? 0), 2
        );
    }

    public function recalculateTotals(): void
    {
        $this->sousTotal = round(array_sum(array_column($this->items, 'total')), 2);
        $this->total     = max(0, round($this->sousTotal - $this->discount, 2));
    }

    public function updatedDiscount(): void { $this->recalculateTotals(); }

    public function updatedSearchClient(): void
    {
        if (strlen($this->searchClient) < 2) {
            $this->clientResults = [];
            return;
        }
        $this->clientResults = Client::where(fn($q) =>
            $q->where('nom', 'like', '%' . $this->searchClient . '%')
              ->orWhere('telephone', 'like', '%' . $this->searchClient . '%')
        )->limit(5)->get(['id', 'nom', 'telephone'])->toArray();
    }

    public function selectClient(int $id, string $nom): void
    {
        $this->clientId       = $id;
        $this->clientName     = $nom;
        $this->searchClient   = '';
        $this->clientResults  = [];
        $this->useQuickClient = false;
    }

    public function clearClient(): void
    {
        $this->clientId         = null;
        $this->clientName       = '';
        $this->useQuickClient   = false;
        $this->quickClientName  = '';
        $this->quickClientPhone = '';
    }

    public function save(): void
    {
        if (empty($this->items)) {
            $this->addError('items', 'Ajoutez au moins une ligne au devis.');
            return;
        }

        DB::transaction(function () {
            $clientId    = $this->clientId;
            $clientName  = $this->clientName;
            $clientPhone = null;

            if (!$clientId && $this->useQuickClient && $this->quickClientName) {
                $clientName  = $this->quickClientName;
                $clientPhone = $this->quickClientPhone;
            }

            $data = [
                'client_id'        => $clientId,
                'user_id'          => Auth::id(),
                'client_nom'       => $clientName ?: null,
                'client_telephone' => $clientPhone ?: null,
                'sous_total'       => $this->sousTotal,
                'remise'           => $this->discount,
                'total'            => $this->total,
                'notes'            => $this->notes ?: null,
                'valide_jusqua'    => $this->valid_until ?: null,
                'date_devis'       => now(),
            ];

            if ($this->isEditing) {
                $quote = Quote::findOrFail($this->quoteId);
                $quote->update($data);
                $quote->items()->delete();
            } else {
                $data['reference'] = Quote::generateReference();
                $data['status']    = 'draft';
                $quote = Quote::create($data);
            }

            foreach ($this->items as $item) {
                QuoteItem::create([
                    'quote_id'      => $quote->id,
                    'produit_id'    => $item['produit_id'] ?? null,
                    'description'   => $item['description'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'quantite'      => $item['quantite'],
                    'remise'        => $item['remise'] ?? 0,
                    'total'         => $item['total'],
                    'is_free_line'  => $item['is_free_line'] ?? false,
                ]);
            }
        });

        session()->flash('success',
            $this->isEditing ? 'Devis modifié avec succès !' : 'Devis enregistré avec succès !');
        $this->redirect(route('devis.index'));
    }

    public function render()
    {
        $categories = Categorie::where('est_active', true)
            ->whereHas('produits', fn($q) =>
                $q->where('est_actif', true)->where(fn($sq) => $sq->where('stock', '>', 0)->orWhere('sur_commande', true))
            )
            ->get(['id', 'nom']);

        return view('livewire.devis.devis-form', compact('categories'));
    }
}
