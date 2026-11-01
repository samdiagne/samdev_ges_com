<?php

namespace App\Livewire\Produits;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Produit;
use App\Models\Categorie;

class ProduitManager extends Component
{
    use WithPagination;

    public string $search          = '';
    public string $filtreCategorie = '';
    public string $filtreStatut    = '';
    public string $filtreStock     = '';
    public string $triPar          = 'created_at';
    public string $triDir          = 'desc';

    public bool   $afficherModalSuppression = false;
    public ?int   $suppressionId            = null;

    protected $queryString = [
        'search'          => ['except' => ''],
        'filtreCategorie' => ['except' => ''],
        'filtreStatut'    => ['except' => ''],
        'filtreStock'     => ['except' => ''],
        'page'            => ['except' => 1],
    ];

    public function mount(): void
    {
        // Restauration depuis la session si les paramètres ne sont pas passés dans la requête
        if (! request()->has('search') && session()->has('produits_filtres.search')) {
            $this->search = (string) session('produits_filtres.search');
        }
        if (! request()->has('filtreCategorie') && session()->has('produits_filtres.filtreCategorie')) {
            $this->filtreCategorie = (string) session('produits_filtres.filtreCategorie');
        }
        if (! request()->has('filtreStatut') && session()->has('produits_filtres.filtreStatut')) {
            $this->filtreStatut = (string) session('produits_filtres.filtreStatut');
        }
        if (! request()->has('filtreStock') && session()->has('produits_filtres.filtreStock')) {
            $this->filtreStock = (string) session('produits_filtres.filtreStock');
        }
    }

    public function updatingSearch(): void          { $this->resetPage(); }
    public function updatingFiltreCategorie(): void { $this->resetPage(); }
    public function updatingFiltreStatut(): void    { $this->resetPage(); }
    public function updatingFiltreStock(): void     { $this->resetPage(); }

    public function updatedSearch(): void          { $this->sauvegarderFiltres(); }
    public function updatedFiltreCategorie(): void { $this->sauvegarderFiltres(); }
    public function updatedFiltreStatut(): void    { $this->sauvegarderFiltres(); }
    public function updatedFiltreStock(): void     { $this->sauvegarderFiltres(); }

    private function sauvegarderFiltres(): void
    {
        session([
            'produits_filtres' => [
                'search'          => $this->search,
                'filtreCategorie' => $this->filtreCategorie,
                'filtreStatut'    => $this->filtreStatut,
                'filtreStock'     => $this->filtreStock,
            ],
            'produits_return_url' => $this->getUrlRetour(),
        ]);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filtreCategorie', 'filtreStatut', 'filtreStock']);
        $this->resetPage();
        session()->forget(['produits_filtres', 'produits_return_url']);
    }

    public function getUrlRetour(): string
    {
        $params = array_filter([
            'search'          => $this->search ?: null,
            'filtreCategorie' => $this->filtreCategorie ?: null,
            'filtreStatut'    => $this->filtreStatut !== '' ? $this->filtreStatut : null,
            'filtreStock'     => $this->filtreStock ?: null,
            'page'            => $this->getPage() > 1 ? $this->getPage() : null,
        ]);

        return route('produits.index', $params);
    }

    public function sortBy(string $champ): void
    {
        if ($this->triPar === $champ) {
            $this->triDir = $this->triDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->triPar = $champ;
            $this->triDir = 'asc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->suppressionId            = $id;
        $this->afficherModalSuppression = true;
    }

    public function delete(): void
    {
        Produit::findOrFail($this->suppressionId)->delete();
        session()->flash('success', 'Produit supprimé avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $produit = Produit::findOrFail($id);
        $produit->update(['est_actif' => ! $produit->est_actif]);
        session()->flash('success', 'Statut mis à jour !');
    }

    public function render()
    {
        $produits = Produit::with('categorie')
            ->when($this->search, fn($q) =>
                $q->where('nom', 'like', '%' . $this->search . '%')
            )
            ->when($this->filtreCategorie, fn($q) =>
                $q->where('categorie_id', $this->filtreCategorie)
            )
            ->when($this->filtreStatut !== '', fn($q) =>
                $q->where('est_actif', $this->filtreStatut === '1')
            )
            ->when($this->filtreStock === 'faible', fn($q) =>
                $q->whereColumn('stock', '<=', 'seuil_alerte_stock')->where('stock', '>', 0)->where('sur_commande', false)
            )
            ->when($this->filtreStock === 'rupture', fn($q) =>
                $q->where('stock', '<=', 0)->where('sur_commande', false)
            )
            ->when($this->filtreStock === 'ok', fn($q) =>
                $q->where(fn($sq) => $sq->whereColumn('stock', '>', 'seuil_alerte_stock')->orWhere('sur_commande', true))
            )
            ->orderBy($this->triPar, $this->triDir)
            ->paginate(12);

        $categories = Categorie::where('est_active', true)->orderBy('nom')->get();

        $stats = [
            'total'   => Produit::count(),
            'actifs'  => Produit::where('est_actif', true)->count(),
            'faible'  => Produit::whereColumn('stock', '<=', 'seuil_alerte_stock')->where('stock', '>', 0)->where('sur_commande', false)->count(),
            'rupture' => Produit::where('stock', '<=', 0)->where('sur_commande', false)->count(),
        ];

        session(['produits_return_url' => $this->getUrlRetour()]);

        $returnUrl = $this->getUrlRetour();

        return view('livewire.produits.produit-manager', compact('produits', 'categories', 'stats', 'returnUrl'));
    }
}
