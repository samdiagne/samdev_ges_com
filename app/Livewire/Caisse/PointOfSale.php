<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CompanySetting;
use App\Services\SaleService;

class PointOfSale extends Component
{
    // Recherche produits
    public string $searchProduct = '';
    public array  $searchResults = [];

    // Panier
    public array $cart = [];

    // Client
    public string $searchCustomer  = '';
    public array  $customerResults = [];
    public ?int   $selectedCustomerId   = null;
    public string $selectedCustomerName = '';

    // Client rapide (si pas dans la base)
    public string $quickClientName  = '';
    public string $quickClientPhone = '';
    public bool   $useQuickClient   = false;

    // Paiement
    public string $paymentMethod = 'cash';
    public float  $amountPaid    = 0;
    public float  $discount      = 0;
    public string $notes         = '';
    public string $saleDate      = '';

    // Modals
    public bool $showPaymentModal  = false;
    public bool $showSuccessModal  = false;

    // Dernière vente
    public ?int $lastSaleId     = null;
    public string $lastReference = '';
    public float  $changeGiven  = 0;

    // Computed
    public float $subtotalHt  = 0;
    public float $tvaAmount   = 0;
    public float $totalTtc    = 0;
    public float $tvaRate     = 18;

    public string $filterCategorie = '';
    public array  $products        = [];

    public function mount(): void
    {
        $this->loadProducts();
        $this->tvaRate  = CompanySetting::getSettings()->tva_rate ?? 18;
        $this->saleDate = now()->toDateString();
    }

    // ── Recherche produits ────────────────────────
    public function loadProducts(): void
    {
        $this->products = \App\Models\Product::where('is_active', true)
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
                'id'            => $p->id,
                'name'          => $p->name,
                'sku'           => $p->sku,
                'selling_price' => (float) $p->selling_price,
                'stock'         => $p->stock_quantity,
                'image'         => $p->main_image,
                'category'      => $p->category?->name,
            ])
            ->toArray();
    }

    public function updatedFilterCategorie(): void { $this->loadProducts(); }
    public function updatedSearchProduct(): void   { $this->loadProducts(); }

    // ── Toggle produit (sélectionner / désélectionner) ──
    public function toggleProduct(int $productId): void
    {
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] === $productId) {
                $this->removeFromCart($key);
                return;
            }
        }
        $this->addToCart($productId);
    }

    // ── Ajouter au panier ─────────────────────────
    public function addToCart(int $productId): void
    {
        $existingKey = null;
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] === $productId) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            // Vérifier stock disponible
            $product = Product::find($productId);
            if ($this->cart[$existingKey]['quantity'] >= $product->stock_quantity) {
                session()->flash('pos_error', 'Stock insuffisant !');
                return;
            }
            $this->cart[$existingKey]['quantity']++;
            $this->recalculateItem($existingKey);
        } else {
            $product = Product::find($productId);
            if (!$product || $product->stock_quantity <= 0) {
                session()->flash('pos_error', 'Produit indisponible !');
                return;
            }

            $tvaAmount = $product->has_tva
                ? ($product->selling_price * $this->tvaRate / 100)
                : 0;

            $this->cart[] = [
                'product_id'     => $product->id,
                'name'           => $product->name,
                'sku'            => $product->sku,
                'unit_price_ht'  => (float) $product->selling_price,
                'unit_price_ttc' => (float) $product->selling_price_ttc,
                'tva_rate'       => 0,
                'quantity'       => 1,
                'discount'       => 0,
                'tva_amount'     => 0,
                'total_ht'       => (float) $product->selling_price,
                'total_ttc'      => (float) $product->selling_price_ttc,
                'stock_max'      => $product->stock_quantity,
                'product'        => $product,
            ];
        }

        $this->recalculateTotals();
    }

    // ── Modifier quantité ─────────────────────────
    public function updateQuantity(int $index, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeFromCart($index);
            return;
        }

        if ($quantity > $this->cart[$index]['stock_max']) {
            $quantity = $this->cart[$index]['stock_max'];
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->recalculateItem($index);
        $this->recalculateTotals();
    }

    // ── Modifier remise ligne ─────────────────────
    public function updateItemDiscount(int $index, float $discount): void
    {
        $this->cart[$index]['discount'] = max(0, $discount);
        $this->recalculateItem($index);
        $this->recalculateTotals();
    }

    // ── Supprimer du panier ───────────────────────
    public function removeFromCart(int $index): void
    {
        array_splice($this->cart, $index, 1);
        $this->cart = array_values($this->cart);
        $this->recalculateTotals();
    }

    // ── Vider le panier ───────────────────────────
    public function clearCart(): void
    {
        $this->cart               = [];
        $this->subtotalHt         = 0;
        $this->tvaAmount          = 0;
        $this->totalTtc           = 0;
        $this->discount           = 0;
        $this->selectedCustomerId = null;
        $this->selectedCustomerName = '';
        $this->notes              = '';
    }

    // ── Recalcul ligne ────────────────────────────
    private function recalculateItem(int $index): void
    {
        $item     = $this->cart[$index];
        $qty      = $item['quantity'];
        $priceHt  = $item['unit_price_ht'];
        $discount = $item['discount'];

        $totalHt      = ($priceHt * $qty) - $discount;
        $tvaAmount    = $item['tva_rate'] > 0
                        ? ($totalHt * $item['tva_rate'] / 100)
                        : 0;
        $totalTtc     = $totalHt + $tvaAmount;

        $this->cart[$index]['total_ht']   = round($totalHt, 2);
        $this->cart[$index]['tva_amount'] = round($tvaAmount, 2);
        $this->cart[$index]['total_ttc']  = round($totalTtc, 2);
    }

    // ── Recalcul totaux ───────────────────────────
    private function recalculateTotals(): void
    {
        $this->subtotalHt = round(array_sum(array_column($this->cart, 'total_ht')), 2);
        $this->tvaAmount  = 0;
        $this->totalTtc   = round($this->subtotalHt + $this->tvaAmount - $this->discount, 2);
        $this->amountPaid = $this->totalTtc;
    }

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
        )
        ->limit(5)
        ->get(['id', 'name', 'phone'])
        ->toArray();
    }

    public function selectCustomer(int $id, string $name): void
    {
        $this->selectedCustomerId   = $id;
        $this->selectedCustomerName = $name;
        $this->searchCustomer       = '';
        $this->customerResults      = [];
        $this->useQuickClient       = false;
        $this->quickClientName      = '';
        $this->quickClientPhone     = '';
    }

    public function clearCustomer(): void
    {
        $this->selectedCustomerId   = null;
        $this->selectedCustomerName = '';
        $this->useQuickClient       = false;
        $this->quickClientName      = '';
        $this->quickClientPhone     = '';
    }

    // ── Ouvrir modal paiement ─────────────────────
    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            session()->flash('pos_error', 'Le panier est vide !');
            return;
        }
        $this->amountPaid      = $this->totalTtc;
        $this->showPaymentModal = true;
    }

    // ── Mettre à jour remise globale ──────────────
    public function updatedDiscount(): void
    {
        $this->discount = max(0, $this->discount);
        $this->totalTtc = round($this->subtotalHt + $this->tvaAmount - $this->discount, 2);
        $this->amountPaid = $this->totalTtc;
    }

    // ── Valider la vente ──────────────────────────
    public function completeSale(SaleService $saleService): void
    {
        if (empty($this->cart)) return;

        if ($this->amountPaid < $this->totalTtc) {
            $this->addError('amountPaid', 'Montant insuffisant !');
            return;
        }

        try {
            // Créer client rapide si renseigné
            $customerId = $this->selectedCustomerId;

            if (!$customerId && $this->useQuickClient && $this->quickClientName) {
                $customer = \App\Models\Customer::create([
                    'name'    => $this->quickClientName,
                    'phone'   => $this->quickClientPhone ?: null,
                    'whatsapp'=> $this->quickClientPhone ?: null,
                ]);
                $customerId = $customer->id;
            }

            $sale = $saleService->createSale(
                cart: $this->cart,
                payment: [
                    'method'      => $this->paymentMethod,
                    'amount_paid' => $this->amountPaid,
                    'discount'    => $this->discount,
                    'notes'       => $this->notes,
                    'sale_date'   => $this->saleDate ?: now()->toDateString(),
                ],
                customerId: $customerId
            );

            $this->lastSaleId    = $sale->id;
            $this->lastReference = $sale->reference;
            $this->changeGiven   = $sale->change_given;

            $this->showPaymentModal = false;
            $this->showSuccessModal = true;
            $this->clearCart();

        } catch (\Exception $e) {
            session()->flash('pos_error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = \App\Models\Category::where('is_active', true)
        ->whereHas('products', fn($q) =>
            $q->where('is_active', true)->where('stock_quantity', '>', 0)
        )
        ->get(['id', 'name']);

        return view('livewire.caisse.point-of-sale',
        array_merge(compact('categories'), [/* autres vars */]));
    }
}