<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Invoice;
use App\Models\CompanySetting;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(private StockService $stockService) {}

    public function createSale(array $cart, array $payment, ?int $customerId = null): Sale
    {
        return DB::transaction(function () use ($cart, $payment, $customerId) {

            $settings   = CompanySetting::getSettings();
            $subtotalHt = 0;

            // Calculer les totaux sans TVA
            foreach ($cart as $item) {
                $subtotalHt += $item['total_ht'];
            }

            $discount    = $payment['discount'] ?? 0;
            $totalTtc    = $subtotalHt - $discount; // Total = sous-total - remise
            $amountPaid  = $payment['amount_paid'];
            $changeGiven = max(0, $amountPaid - $totalTtc);

            // Résoudre la date/heure de la vente
            $saleDate = $payment['sale_date'] ?? null;
            $soldAt   = $saleDate && $saleDate !== now()->toDateString()
                        ? \Carbon\Carbon::parse($saleDate)->startOfDay()
                        : now();

            // Créer la vente
            $sale = Sale::create([
                'reference'      => $this->generateReference(),
                'customer_id'    => $customerId,
                'user_id'        => Auth::id(),
                'source'         => 'shop',
                'status'         => 'completed',
                'subtotal_ht'    => $subtotalHt,
                'tva_amount'     => 0,
                'total_ttc'      => $totalTtc,
                'discount'       => $discount,
                'amount_paid'    => $amountPaid,
                'change_given'   => $changeGiven,
                'payment_method' => $payment['method'],
                'notes'          => $payment['notes'] ?? null,
                'sold_at'        => $soldAt,
            ]);

            // Créer les lignes de vente + décrémenter stock
            foreach ($cart as $item) {
                SaleItem::create([
                    'sale_id'        => $sale->id,
                    'product_id'     => $item['product_id'],
                    'product_name'   => $item['name'],
                    'unit_price_ht'  => $item['unit_price_ht'],
                    'unit_price_ttc' => $item['unit_price_ht'], // TTC = HT sans TVA
                    'quantity'       => $item['quantity'],
                    'discount'       => $item['discount'] ?? 0,
                    'total_ht'       => $item['total_ht'],
                    'total_ttc'      => $item['total_ht'], // TTC = HT sans TVA
                ]);

                // Stock Engine
                $this->stockService->decrementStock(
                    $item['product'],
                    $item['quantity'],
                    'sale',
                    $sale->id,
                    'Vente #' . $sale->reference
                );
            }

            // Générer la facture
            $invoice = Invoice::create([
                'sale_id'        => $sale->id,
                'customer_id'    => $customerId,
                'invoice_number' => $this->generateInvoiceNumber($settings),
                'status'         => 'issued',
                'subtotal_ht'    => $subtotalHt,
                'tva_rate'       => 0,
                'tva_amount'     => 0,
                'total_ttc'      => $totalTtc,
                'issued_at'      => $soldAt,
            ]);

            // Incrémenter compteur facture
            $settings->increment('invoice_counter');

            // Mettre à jour total achats client
            if ($customerId) {
                Customer::where('id', $customerId)
                    ->increment('total_purchases', $totalTtc);
            }

            return $sale->load(['items', 'invoice', 'customer']);
        });
    }

    private function generateReference(): string
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
}