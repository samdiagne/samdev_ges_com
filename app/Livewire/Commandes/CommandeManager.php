<?php

namespace App\Livewire\Commandes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\CompanySetting;
use App\Services\StockService;
use App\Mail\OrderDeliveredMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommandeManager extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public ?int   $viewingId    = null;

    public function updatingSearch(): void { $this->resetPage(); }

    public function view(int $id): void
    {
        $this->viewingId = $id;
    }

    public function updateStatus(int $id, string $status): void
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);

        // Mail de remerciement au client à la livraison
        if ($status === 'delivered' && $order->customer_email) {
            try {
                Mail::to($order->customer_email)->send(new OrderDeliveredMail($order));
            } catch (\Exception $e) {
                Log::error('Mail livraison : ' . $e->getMessage());
            }
        }

        // Déclenchement auto si payé + livré
        if ($status === 'delivered' && $order->payment_status === 'paid' && !$order->sale_id) {
            $this->convertToSale($id);
            return;
        }

        session()->flash('success', 'Statut mis à jour !');
    }

    public function updatePaymentStatus(int $id, string $paymentStatus): void
    {
        $order = Order::findOrFail($id);
        $order->update(['payment_status' => $paymentStatus]);

        // Déclenchement auto si payé + livré
        if ($paymentStatus === 'paid' && $order->status === 'delivered' && !$order->sale_id) {
            $this->convertToSale($id);
            return;
        }

        session()->flash('success', 'Statut paiement mis à jour !');
    }

    public function convertToSale(int $orderId): void
    {
        $order = Order::with('items.product')->findOrFail($orderId);

        if ($order->sale_id) {
            session()->flash('info', 'Cette commande a déjà été convertie en vente.');
            return;
        }

        if ($order->status !== 'delivered' || $order->payment_status !== 'paid') {
            session()->flash('error', 'La commande doit être livrée et payée pour être convertie.');
            return;
        }

        try {
            DB::transaction(function () use ($order) {
                $settings  = CompanySetting::getSettings();
                $stockSvc  = app(StockService::class);

                // Trouver ou créer le client
                $customer = Customer::firstOrCreate(
                    ['phone' => $order->customer_phone],
                    [
                        'name'  => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone,
                    ]
                );

                // Créer la vente
                $sale = Sale::create([
                    'reference'      => $this->generateSaleReference(),
                    'customer_id'    => $customer->id,
                    'user_id'        => auth()->id(),
                    'source'         => 'online',
                    'status'         => 'completed',
                    'subtotal_ht'    => $order->subtotal,
                    'tva_amount'     => 0,
                    'total_ttc'      => $order->total,
                    'discount'       => 0,
                    'amount_paid'    => $order->total,
                    'change_given'   => 0,
                    'payment_method' => $order->payment_method,
                    'notes'          => 'Commande en ligne ' . $order->reference,
                    'sold_at'        => $order->updated_at,
                ]);

                // Créer les lignes + décrémenter stock
                foreach ($order->items as $item) {
                    SaleItem::create([
                        'sale_id'        => $sale->id,
                        'product_id'     => $item->product_id,
                        'product_name'   => $item->product_name,
                        'unit_price_ht'  => $item->unit_price,
                        'unit_price_ttc' => $item->unit_price,
                        'quantity'       => $item->quantity,
                        'discount'       => 0,
                        'total_ht'       => $item->total,
                        'total_ttc'      => $item->total,
                    ]);

                    if ($item->product) {
                        $stockSvc->decrementStock(
                            $item->product,
                            $item->quantity,
                            'sale',
                            $sale->id,
                            'Vente en ligne #' . $order->reference
                        );
                    }
                }

                // Générer la facture
                Invoice::create([
                    'sale_id'        => $sale->id,
                    'customer_id'    => $customer->id,
                    'invoice_number' => $this->generateInvoiceNumber($settings),
                    'status'         => 'issued',
                    'subtotal_ht'    => $order->subtotal,
                    'tva_rate'       => 0,
                    'tva_amount'     => 0,
                    'total_ttc'      => $order->total,
                    'issued_at'      => now(),
                ]);

                $settings->increment('invoice_counter');

                // Mettre à jour total achats client
                $customer->increment('total_purchases', $order->total);

                // Lier la commande à la vente
                $order->update(['sale_id' => $sale->id]);
            });

            session()->flash('success', 'Commande convertie en vente et facture générée !');

        } catch (\Exception $e) {
            Log::error('Conversion commande→vente : ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de la conversion : ' . $e->getMessage());
        }
    }

    private function generateSaleReference(): string
    {
        $last = Sale::withTrashed()->latest()->first();
        $num  = $last ? (intval(substr($last->reference, -4)) + 1) : 1;
        return 'VNT-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function generateInvoiceNumber(CompanySetting $settings): string
    {
        return $settings->invoice_prefix . '-' . date('Y') . '-'
             . str_pad($settings->invoice_counter, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $orders = Order::with(['items', 'sale'])
            ->when($this->search, fn($q) =>
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterStatus, fn($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->latest()
            ->paginate(15);

        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        $viewing = $this->viewingId
            ? Order::with(['items', 'sale.invoice'])->find($this->viewingId)
            : null;

        return view('livewire.commandes.commande-manager',
            compact('orders', 'stats', 'viewing'));
    }
}
