<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductManager extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filterCategory = '';
    public string $filterStatus   = '';
    public string $filterStock    = '';
    public string $sortBy         = 'created_at';
    public string $sortDir        = 'desc';

    public bool $showDeleteModal  = false;
    public ?int $deletingId       = null;

    protected $queryString = [
        'search'         => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterStatus'   => ['except' => ''],
        'filterStock'    => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterStock(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $field;
            $this->sortDir = 'asc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $product = Product::findOrFail($this->deletingId);
        $product->delete();
        session()->flash('success', 'Produit supprimé avec succès !');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        session()->flash('success', 'Statut mis à jour !');
    }

    public function render()
    {
        $products = Product::with(['category', 'supplier'])
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('brand', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterCategory, fn($q) =>
                $q->where('category_id', $this->filterCategory)
            )
            ->when($this->filterStatus !== '', fn($q) =>
                $q->where('is_active', $this->filterStatus === '1')
            )
            ->when($this->filterStock === 'low', fn($q) =>
                $q->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                  ->where('stock_quantity', '>', 0)
            )
            ->when($this->filterStock === 'out', fn($q) =>
                $q->where('stock_quantity', '<=', 0)
            )
            ->when($this->filterStock === 'ok', fn($q) =>
                $q->whereColumn('stock_quantity', '>', 'stock_alert_threshold')
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(12);

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total'    => Product::count(),
            'active'   => Product::where('is_active', true)->count(),
            'low'      => Product::whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                                 ->where('stock_quantity', '>', 0)->count(),
            'out'      => Product::where('stock_quantity', '<=', 0)->count(),
        ];

        return view('livewire.products.product-manager', compact('products', 'categories', 'stats'));
    }
}