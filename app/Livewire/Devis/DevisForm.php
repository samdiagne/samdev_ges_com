<?php

namespace App\Livewire\Devis;

use Livewire\Component;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DevisForm extends Component
{
    public bool $isEditing  = false;
    public ?int $quoteId    = null;

    // Client
    public ?int   $customerId         = null;
    public string $customerName       = '';
    public string $searchCustomer     = '';
    public array  $customerResults    = [];
    public bool   $useQuickClient     = false;
    public string $quickClientName    = '';
    public string $quickClientPhone   = '';

    // Infos devis
    public string $notes       = '';
    public string $valid_until = '';
    public float  $discount    = 0;
    public float  $tvaRate     = 18;

    // Recherche produit
    public string $searchProduct  = '';
    public array  $searchResults  = [];

    public string $filterCategorie = '';
    public array  $allProducts     = [];

    // Lignes
    public array $items = [];

    // Ligne libre
    public bool   $showFreeLineForm    = false;
    public string $freeLineDescription = '';
    public float  $freeLinePrice       = 0;
    public int    $freeLineQty         = 1;
    public bool   $freeLineTva         = true;

    // Totaux
    public float $subtotalHt = 0;
    public float $tvaAmount  = 0;
    public float $totalTtc   = 0;

    public function mount(?int $quoteId = null): void
    {
        $this->tvaRate     = 0;
        $this->valid_until = now()->addDays(30)->format('Y-m-d');
        $this->loadProducts();

        if ($quoteId) {
            $this->isEditing = true;
            $this->quoteId   = $quoteId;
            $this->loadQuote($quoteId);
        }
    }

    public function loadProducts(): void
    {
        $this->allProducts = \App\Models\Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->when($this->filterCategorie, fn($q) =>
                $q->where('category_id', $this->filterCategorie)
            )
            ->when($this->searchProduct, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('name', 'like', '%' . $this->searchProduct . '%')
                    ->orWhere('sku', 'like', '%' . $this->searchProduct . '%')
                )
            )
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'name'             => $p->name,
                'sku'              => $p->sku,
                'selling_price'    => (float) $p->selling_price,
                'stock_quantity'   => $p->stock_quantity,
                'image'            => $p->main_image,
                'category'         => $p->category?->name,
                'category_id'      => $p->category_id,
            ])
            ->toArray();
    }

    public function updatedFilterCategorie(): void { $this->loadProducts(); }

    private function loadQuote(int $id): void
    {
        $quote = Quote::with('items')->findOrFail($id);

        $this->customerId   = $quote->customer_id;
        $this->customerName = $quote->customer?->name ?? $quote->customer_name ?? '';
        $this->notes        = $quote->notes ?? '';
        $this->valid_until  = $quote->valid_until?->format('Y-m-d') ?? '';
        $this->discount     = (float) $quote->discount;

        $this->items = $quote->items->map(fn($item) => [
            'product_id'     => $item->product_id,
            'description'    => $item->description,
            'unit_price_ht'  => (float) $item->unit_price_ht,
            'unit_price_ttc' => (float) $item->unit_price_ttc,
            'tva_rate'       => (float) $item->tva_rate,
            'quantity'       => $item->quantity,
            'discount'       => (float) $item->discount,
            'total_ht'       => (float) $item->total_ht,
            'total_ttc'      => (float) $item->total_ttc,
            'is_free_line'   => $item->is_free_line,
        ])->toArray();

        $this->recalculateTotals();
    }

    // ── Recherche produit ─────────────────────────
    public function updatedSearchProduct(): void
    {
        if (strlen($this->searchProduct) < 2) {
            $this->searchResults = [];
            return;
        }
        $this->searchResults = Product::where('is_active', true)
            ->where(fn($q) =>
                $q->where('name', 'like', '%' . $this->searchProduct . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchProduct . '%')
            )
            ->limit(6)->get()
            ->map(fn($p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'sku'             => $p->sku,
                'selling_price'   => (float) $p->selling_price,
                'selling_price_ttc'=> (float) $p->selling_price_ttc,
                'has_tva'         => $p->has_tva,
                'stock_quantity'  => $p->stock_quantity,
            ])->toArray();
    }

    // ── Toggle produit (sélectionner / désélectionner) ──
    public function toggleProduct(int $productId): void
    {
        foreach ($this->items as $key => $item) {
            if ($item['product_id'] === $productId) {
                $this->removeItem($key);
                return;
            }
        }
        $this->addProductItem($productId);
    }

    // ── Ajouter produit catalogue ─────────────────
    public function addProductItem(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $tvaRate = $product->has_tva ? $this->tvaRate : 0;

        $this->items[] = [
            'product_id'     => $product->id,
            'description'    => $product->name,
            'unit_price_ht'  => (float) $product->selling_price,
            'unit_price_ttc' => (float) $product->selling_price_ttc,
            'tva_rate'       => $tvaRate,
            'quantity'       => 1,
            'discount'       => 0,
            'total_ht'       => (float) $product->selling_price,
            'total_ttc'      => (float) $product->selling_price_ttc,
            'is_free_line'   => false,
        ];

        $this->searchProduct = '';
        $this->searchResults = [];
        $this->recalculateTotals();
    }

    // ── Ajouter ligne libre ───────────────────────
    public function addFreeLine(): void
    {
        if (!$this->freeLineDescription || $this->freeLinePrice <= 0) {
            return;
        }

        $tvaRate  = $this->freeLineTva ? $this->tvaRate : 0;
        $priceHt  = $this->freeLinePrice;
        $priceTtc = $this->freeLineTva
            ? round($priceHt * (1 + $tvaRate / 100), 2)
            : $priceHt;

        $this->items[] = [
            'product_id'     => null,
            'description'    => $this->freeLineDescription,
            'unit_price_ht'  => $priceHt,
            'unit_price_ttc' => $priceTtc,
            'tva_rate'       => $tvaRate,
            'quantity'       => $this->freeLineQty,
            'discount'       => 0,
            'total_ht'       => round($priceHt * $this->freeLineQty, 2),
            'total_ttc'      => round($priceTtc * $this->freeLineQty, 2),
            'is_free_line'   => true,
        ];

        $this->freeLineDescription = '';
        $this->freeLinePrice       = 0;
        $this->freeLineQty         = 1;
        $this->freeLineTva         = true;
        $this->showFreeLineForm    = false;
        $this->recalculateTotals();
    }

    // ── Modifier quantité ─────────────────────────
    public function updateQuantity(int $index, int $qty): void
    {
        if ($qty <= 0) { $this->removeItem($index); return; }
        $this->items[$index]['quantity']  = $qty;
        $this->recalculateItem($index);
        $this->recalculateTotals();
    }

    // ── Supprimer ligne ───────────────────────────
    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->items = array_values($this->items);
        $this->recalculateTotals();
    }

    // ── Recalcul ligne ────────────────────────────
    private function recalculateItem(int $index): void
    {
        $item    = $this->items[$index];
        $totalHt = round($item['unit_price_ht'] * $item['quantity'] - $item['discount'], 2);
        $tvaMt   = $item['tva_rate'] > 0
            ? round($totalHt * $item['tva_rate'] / 100, 2) : 0;

        $this->items[$index]['total_ht']  = $totalHt;
        $this->items[$index]['total_ttc'] = round($totalHt + $tvaMt, 2);
    }

    // ── Recalcul totaux ───────────────────────────
    public function recalculateTotals(): void
    {
        $this->subtotalHt = round(array_sum(array_column($this->items, 'total_ht')), 2);
        $tva = 0;
        foreach ($this->items as $item) {
            $tva += $item['tva_rate'] > 0
                ? round($item['total_ht'] * $item['tva_rate'] / 100, 2) : 0;
        }
        $this->tvaAmount = round($tva, 2);
        $this->totalTtc  = round($this->subtotalHt + $this->tvaAmount - $this->discount, 2);
    }

    public function updatedDiscount(): void { $this->recalculateTotals(); }

    // ── Recherche client ──────────────────────────
    public function updatedSearchCustomer(): void
    {
        if (strlen($this->searchCustomer) < 2) {
            $this->customerResults = [];
            return;
        }
        $this->customerResults = Customer::where(fn($q) =>
            $q->where('name', 'like', '%' . $this->searchCustomer . '%')
              ->orWhere('phone', 'like', '%' . $this->searchCustomer . '%')
        )->limit(5)->get(['id', 'name', 'phone'])->toArray();
    }

    public function selectCustomer(int $id, string $name): void
    {
        $this->customerId    = $id;
        $this->customerName  = $name;
        $this->searchCustomer = '';
        $this->customerResults = [];
        $this->useQuickClient  = false;
    }

    public function clearCustomer(): void
    {
        $this->customerId      = null;
        $this->customerName    = '';
        $this->useQuickClient  = false;
        $this->quickClientName = '';
        $this->quickClientPhone= '';
    }

    // ── Sauvegarder ──────────────────────────────
    public function save(): void
    {
        if (empty($this->items)) {
            $this->addError('items', 'Ajoutez au moins une ligne.');
            return;
        }

        DB::transaction(function () {
            // Client rapide
            $customerId = $this->customerId;
            $clientName = $this->customerName;
            $clientPhone= null;

            if (!$customerId && $this->useQuickClient && $this->quickClientName) {
                $clientName  = $this->quickClientName;
                $clientPhone = $this->quickClientPhone;
            }

            $data = [
                'customer_id'    => $customerId,
                'user_id'        => Auth::id(),
                'customer_name'  => $clientName ?: null,
                'customer_phone' => $clientPhone ?: null,
                'subtotal_ht'    => $this->subtotalHt,
                'tva_amount'     => $this->tvaAmount,
                'total_ttc'      => $this->totalTtc,
                'discount'       => $this->discount,
                'tva_rate'       => $this->tvaRate,
                'notes'          => $this->notes ?: null,
                'valid_until'    => $this->valid_until ?: null,
                'quote_date'     => now(),
            ];

            if ($this->isEditing) {
                $quote = Quote::findOrFail($this->quoteId);
                // On conserve le statut existant lors d'une modification
                $quote->update($data);
                $quote->items()->delete();
            } else {
                $data['reference'] = Quote::generateReference();
                $data['status']    = 'draft';
                $quote = Quote::create($data);
            }

            foreach ($this->items as $item) {
                QuoteItem::create([
                    'quote_id'       => $quote->id,
                    'product_id'     => $item['product_id'],
                    'description'    => $item['description'],
                    'unit_price_ht'  => $item['unit_price_ht'],
                    'unit_price_ttc' => $item['unit_price_ttc'],
                    'quantity'       => $item['quantity'],
                    'tva_rate'       => $item['tva_rate'],
                    'discount'       => $item['discount'],
                    'total_ht'       => $item['total_ht'],
                    'total_ttc'      => $item['total_ttc'],
                    'is_free_line'   => $item['is_free_line'],
                ]);
            }
        });

        session()->flash('success',
            $this->isEditing ? 'Devis modifié !' : 'Devis enregistré avec succès !');
        $this->redirect(route('devis.index'));
    }

    public function render()
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->whereHas('products', fn($q) =>
                $q->where('is_active', true)->where('stock_quantity', '>', 0)
            )
            ->get(['id', 'name']);

        return view('livewire.devis.devis-form', [
            'tvaRate'    => $this->tvaRate,
            'categories' => $categories,
        ]);
    }
}