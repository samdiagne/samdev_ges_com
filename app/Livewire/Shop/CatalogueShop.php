<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class CatalogueShop extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $categorie   = '';
    public string $sort        = 'latest';
    public int    $minPrice    = 0;
    public int    $maxPrice    = 5000000;
    public bool   $inStockOnly = false;
    public bool   $showFilters = false;

    protected $queryString = [
        'search'    => ['except' => ''],
        'categorie' => ['except' => ''],
        'sort'      => ['except' => 'latest'],
    ];

    public function mount(): void
    {
        $this->search    = request('q', '');
        $this->categorie = request('categorie', '');
        $this->sort      = request('sort', 'latest');
    }

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingCategorie(): void { $this->resetPage(); }
    public function updatingSort(): void     { $this->resetPage(); }
    public function updatingMinPrice(): void  { $this->resetPage(); }
    public function updatingMaxPrice(): void  { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->search      = '';
        $this->categorie   = '';
        $this->sort        = 'latest';
        $this->minPrice    = 0;
        $this->maxPrice    = 5000000;
        $this->inStockOnly = false;
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('description', 'like', '%' . $this->search . '%')
                       ->orWhere('brand', 'like', '%' . $this->search . '%')
                       ->orWhere('sku', 'like', '%' . $this->search . '%')
                )
            )
            ->when($this->categorie, fn($q) =>
                $q->whereHas('category', fn($q2) =>
                    $q2->where('slug', $this->categorie)
                )
            )
            ->when($this->minPrice > 0, fn($q) =>
                $q->where('selling_price', '>=', $this->minPrice)
            )
            ->when($this->maxPrice < 5000000, fn($q) =>
                $q->where('selling_price', '<=', $this->maxPrice)
            )
            ->when($this->inStockOnly, fn($q) =>
                $q->where('stock_quantity', '>', 0)
            )
            ->when($this->sort === 'latest',     fn($q) => $q->latest())
            ->when($this->sort === 'price_asc',  fn($q) => $q->orderBy('selling_price'))
            ->when($this->sort === 'price_desc', fn($q) => $q->orderByDesc('selling_price'))
            ->when($this->sort === 'name',       fn($q) => $q->orderBy('name'))
            ->paginate(12);

        $categories = Category::where('is_active', true)
            ->withCount('activeProducts')
            ->having('active_products_count', '>', 0)
            ->get();

        $totalProducts = $products->total();

        return view('livewire.shop.catalogue-shop',
            compact('products', 'categories', 'totalProducts'));
    }
}