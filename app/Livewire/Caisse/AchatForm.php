<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AchatForm extends Component
{
    public bool $isEditing  = false;
    public ?int $purchaseId = null;

    // Infos achat
    public ?int   $supplier_id    = null;
    public string $purchase_date  = '';
    public string $expected_date  = '';
    public string $status         = 'received';
    public string $payment_method = 'cash';
    public ?float $amount_paid    = 0;
    public string $notes          = '';

    // Recherche produit
    public string $searchProduct  = '';
    public array  $searchResults  = [];

    // Lignes d'achat
    public array $items = [];

    // Totaux
    public float $totalAmount = 0;
    public float $amountDue   = 0;

    protected function rules(): array
    {
        return [
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'status'        => 'required|in:ordered,received,partial,cancelled',
            'payment_method'=> 'nullable|string',
            'amount_paid'   => 'nullable|numeric|min:0',
            'items'         => 'required|array|min:1',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'purchase_date.required' => 'La date d\'achat est obligatoire.',
        'items.required' => 'Ajoutez au moins un produit.',
        'items.min'      => 'Ajoutez au moins un produit.',
    ];

    public function mount(?int $purchaseId = null): void
    {
        $this->purchase_date = now()->format('Y-m-d');

        if ($purchaseId) {
            $this->isEditing  = true;
            $this->purchaseId = $purchaseId;
            $this->loadPurchase($purchaseId);
        }
    }

    private function loadPurchase(int $id): void
    {
        $purchase = Purchase::with('items.product')->findOrFail($id);

        $this->supplier_id    = $purchase->supplier_id ?? null;
        $this->purchase_date  = $purchase->purchase_date->format('Y-m-d');
        $this->expected_date  = $purchase->expected_date?->format('Y-m-d') ?? '';
        $this->status         = $purchase->status;
        $this->payment_method = $purchase->payment_method ?? 'cash';
        $this->amount_paid    = (float) $purchase->amount_paid;
        $this->notes          = $purchase->notes ?? '';

        $this->items = $purchase->items->map(fn($item) => [
            'product_id'   => $item->product_id,
            'product_name' => $item->product_name,
            'unit_price'   => (float) $item->unit_price,
            'quantity'     => $item->quantity,
            'total_price'  => (float) $item->total_price,
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
            ->limit(6)
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'sku'            => $p->sku,
                'purchase_price' => (float) $p->purchase_price,
                'stock_quantity' => $p->stock_quantity,
            ])
            ->toArray();
    }

    // ── Ajouter produit ───────────────────────────
    public function addItem(int $productId): void
    {
        // Vérifier si déjà dans la liste
        foreach ($this->items as $item) {
            if ($item['product_id'] === $productId) {
                session()->flash('form_error', 'Produit déjà ajouté !');
                $this->searchProduct = '';
                $this->searchResults = [];
                return;
            }
        }

        $product = Product::findOrFail($productId);

        $this->items[] = [
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'unit_price'   => (float) $product->purchase_price,
            'quantity'     => 1,
            'total_price'  => (float) $product->purchase_price,
        ];

        $this->searchProduct = '';
        $this->searchResults = [];
        $this->recalculateTotals();
    }

    // ── Modifier quantité/prix ────────────────────
    public function updateItem(int $index, string $field, $value): void
    {
        $this->items[$index][$field] = $field === 'quantity'
            ? max(1, (int) $value)
            : max(0, (float) $value);

        $this->items[$index]['total_price'] = round(
            $this->items[$index]['unit_price'] * $this->items[$index]['quantity'], 2
        );

        $this->recalculateTotals();
    }

    // ── Supprimer ligne ───────────────────────────
    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->items = array_values($this->items);
        $this->recalculateTotals();
    }

    // ── Recalcul totaux ───────────────────────────
    public function recalculateTotals(): void
    {
        $this->totalAmount = round(array_sum(array_column($this->items, 'total_price')), 2);
        $this->amountDue   = round($this->totalAmount - ($this->amount_paid ?? 0), 2);
    }

    public function updatedAmountPaid(): void
    {
        $this->recalculateTotals();
    }

    // ── Sauvegarder ──────────────────────────────
    public function save(StockService $stockService): void
    {
        $this->validate();

        DB::transaction(function () use ($stockService) {

            $data = [
                'supplier_id'    => $this->supplier_id,
                'user_id'        => Auth::id(),
                'status'         => $this->status,
                'total_amount'   => $this->totalAmount,
                'amount_paid'    => $this->amount_paid ?? 0,
                'amount_due'     => max(0, $this->amountDue),
                'payment_method' => $this->payment_method ?: null,
                'purchase_date'  => $this->purchase_date,
                'expected_date'  => $this->expected_date ?: null,
                'notes'          => $this->notes ?: null,
            ];

            if ($this->isEditing) {
                $purchase = Purchase::findOrFail($this->purchaseId);

                // Reverser l'ancien stock si statut était received
                if ($purchase->status === 'received') {
                    foreach ($purchase->items as $oldItem) {
                        $product = Product::find($oldItem->product_id);
                        if ($product) {
                            $stockService->decrementStock(
                                $product,
                                $oldItem->quantity,
                                'adjustment',
                                $purchase->id,
                                'Annulation achat #' . $purchase->reference
                            );
                        }
                    }
                }

                $purchase->update($data);
                $purchase->items()->delete();

            } else {
                $data['reference'] = $this->generateReference();
                $purchase = Purchase::create($data);
            }

            // Créer les nouvelles lignes
            foreach ($this->items as $item) {
                PurchaseItem::create([
                    'purchase_id'  => $purchase->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'unit_price'   => $item['unit_price'],
                    'quantity'     => $item['quantity'],
                    'total_price'  => $item['total_price'],
                ]);

                // Incrémenter stock si reçu
                if ($this->status === 'received') {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $stockService->incrementStock(
                            $product,
                            $item['quantity'],
                            'purchase',
                            $purchase->id,
                            'Achat #' . $purchase->reference
                        );
                    }
                }
            }
        });

        session()->flash('success',
            $this->isEditing
                ? 'Achat modifié avec succès !'
                : 'Achat enregistré avec succès !'
        );

        $this->redirect(route('caisse.achats.index'));
    }

    private function generateReference(): string
    {
        $last = Purchase::withTrashed()->latest()->first();
        $num  = $last ? (intval(substr($last->reference, -4)) + 1) : 1;
        return 'ACH-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.caisse.achat-form', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}