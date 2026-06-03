<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AchatForm extends Component
{
    public bool $isEditing = false;
    public ?int $purchaseId = null;

    // Infos achat
    public ?int $supplier_id = null;
    public string $purchase_date = '';
    public string $status = 'received';
    public string $payment_method = 'cash';
    public ?float $amount_paid = 0;
    public string $notes = '';

    // Lignes
    public array $items = [];

    // Totaux
    public float $totalAmount = 0;
    public float $amountDue = 0;

    protected function rules(): array
    {
        return [
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'status' => 'required|in:ordered,received,partial,cancelled',
            'payment_method' => 'nullable|string',
            'amount_paid' => 'nullable|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'purchase_date.required' => 'La date d\'achat est obligatoire.',
        'items.required' => 'Ajoutez au moins une ligne.',
        'items.min' => 'Ajoutez au moins une ligne.',
        'items.*.description.required' => 'La description est obligatoire.',
        'items.*.quantity.required' => 'La quantité est obligatoire.',
        'items.*.unit_price.required' => 'Le montant est obligatoire.',
    ];

    public function mount(?int $purchaseId = null): void
    {
        $this->purchase_date = now()->format('Y-m-d');

        if ($purchaseId) {
            $this->isEditing = true;
            $this->purchaseId = $purchaseId;
            $this->loadPurchase($purchaseId);
        } else {
            $this->addItem();
        }
    }

    private function loadPurchase(int $id): void
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        $this->supplier_id = $purchase->supplier_id;
        $this->purchase_date = $purchase->purchase_date->format('Y-m-d');
        $this->status = $purchase->status;
        $this->payment_method = $purchase->payment_method ?? 'cash';
        $this->amount_paid = (float) $purchase->amount_paid;
        $this->notes = $purchase->notes ?? '';

        $this->items = $purchase->items->map(function ($item) {
            return [
                'description' => $item->description,
                'quantity' => $item->quantity,

                // le montant est stocké ici
                'unit_price' => (float) $item->total_price,
            ];
        })->toArray();

        $this->recalculateTotals();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function updateItem(int $index, string $field, $value): void
    {
        if ($field === 'quantity') {
            $this->items[$index]['quantity'] = max(1, (int) $value);
        }

        if ($field === 'unit_price') {
            $this->items[$index]['unit_price'] = max(0, (float) $value);
        }

        if ($field === 'description') {
            $this->items[$index]['description'] = $value;
        }

        $this->recalculateTotals();
    }

    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);

        if (empty($this->items)) {
            $this->addItem();
        }

        $this->items = array_values($this->items);

        $this->recalculateTotals();
    }

    public function recalculateTotals(): void
    {
        $this->totalAmount = round(
            array_sum(array_column($this->items, 'unit_price')),
            2
        );

        $this->amountDue = round(
            $this->totalAmount - ($this->amount_paid ?? 0),
            2
        );
    }

    public function updatedAmountPaid(): void
    {
        $this->recalculateTotals();
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {

            $data = [
                'supplier_id' => $this->supplier_id,
                'user_id' => Auth::id(),
                'status' => $this->status,
                'total_amount' => $this->totalAmount,
                'amount_paid' => $this->amount_paid ?? 0,
                'amount_due' => max(0, $this->amountDue),
                'payment_method' => $this->payment_method ?: null,
                'purchase_date' => $this->purchase_date,
                'notes' => $this->notes ?: null,
            ];

            if ($this->isEditing) {

                $purchase = Purchase::findOrFail($this->purchaseId);

                $purchase->update($data);

                $purchase->items()->delete();

            } else {

                $data['reference'] = $this->generateReference();

                $purchase = Purchase::create($data);
            }

            foreach ($this->items as $item) {

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],

                    // montant saisi directement
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'],
                ]);
            }
        });

        session()->flash(
            'success',
            $this->isEditing
                ? 'Achat modifié avec succès !'
                : 'Achat enregistré avec succès !'
        );

        $this->redirect(route('caisse.achats.index'));
    }

    private function generateReference(): string
    {
        $last = Purchase::withTrashed()->latest()->first();

        $num = $last
            ? intval(substr($last->reference, -4)) + 1
            : 1;

        return 'ACH-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.caisse.achat-form', [
            'suppliers' => Supplier::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }
}