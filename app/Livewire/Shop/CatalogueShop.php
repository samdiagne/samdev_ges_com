<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Plat;
use App\Models\Categorie;

class CatalogueShop extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $categorie   = '';
    public string $sort        = 'latest';
    public bool   $inStockOnly = false;
    public bool   $showFilters = false;
    public string $budget      = '';

    public const BUDGETS = [
        'eco'      => ['label' => 'Moins de 2 000F',       'min' => 0,     'max' => 2000],
        'standard' => ['label' => '2 000 — 5 000F',        'min' => 2000,  'max' => 5000],
        'premium'  => ['label' => '5 000 — 10 000F',       'min' => 5000,  'max' => 10000],
        'gastro'   => ['label' => 'Plus de 10 000F',        'min' => 10000, 'max' => null],
    ];

    protected $queryString = [
        'search'    => ['except' => ''],
        'categorie' => ['except' => ''],
        'sort'      => ['except' => 'latest'],
    ];

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingCategorie(): void { $this->resetPage(); }
    public function updatingBudget(): void    { $this->resetPage(); }

    public function toggleBudget(string $key): void
    {
        $this->budget = $this->budget === $key ? '' : $key;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search      = '';
        $this->categorie   = '';
        $this->sort        = 'latest';
        $this->inStockOnly = false;
        $this->budget      = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Plat::where('est_actif', true)->with('categorie');

        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('nom', 'like', '%' . $this->search . '%')
                  ->orWhere('description_courte', 'like', '%' . $this->search . '%')
            );
        }

        if ($this->categorie) {
            $query->whereHas('categorie', fn($q) =>
                $q->where('slug', $this->categorie)
            );
        }

        if ($this->inStockOnly) {
            $query->where('stock', '>', 0);
        }

        if ($this->budget && isset(self::BUDGETS[$this->budget])) {
            $range = self::BUDGETS[$this->budget];
            if ($range['min'] > 0) {
                $query->where('prix_vente', '>=', $range['min']);
            }
            if ($range['max'] !== null) {
                $query->where('prix_vente', '<=', $range['max']);
            }
        }

        match ($this->sort) {
            'price_asc'  => $query->orderBy('prix_vente', 'asc'),
            'price_desc' => $query->orderBy('prix_vente', 'desc'),
            'name'       => $query->orderBy('nom', 'asc'),
            default      => $query->latest(),
        };

        $plats         = $query->paginate(12);
        $totalProducts = $plats->total();

        $categories = Categorie::where('est_active', true)
            ->withCount(['platsActifs as plats_actifs_count'])
            ->having('plats_actifs_count', '>', 0)
            ->orderBy('ordre')
            ->get();

        return view('livewire.shop.catalogue-shop',
            compact('plats', 'categories', 'totalProducts'));
    }
}
