<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Invoice;
use App\Models\CompanySetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class VentesImport implements ToCollection
{
    public array $errors  = [];
    public int   $created = 0;

    private array $aliases = [
        'date'             => ['date', 'date_vente', 'date vente', 'jour', 'day'],
        'client'           => ['client', 'nom_client', 'nom client', 'customer', 'acheteur', 'nom'],
        'telephone_client' => ['telephone_client', 'telephone client', 'telephone', 'tel', 'phone', 'mobile', 'contact'],
        'mode_paiement'    => ['mode_paiement', 'mode paiement', 'paiement', 'payment', 'moyen_paiement', 'moyen paiement', 'reglement'],
        'produit'          => ['produit', 'product', 'article', 'designation', 'désignation', 'libelle', 'libellé', 'nom', 'name'],
        'quantite'         => ['quantite', 'quantité', 'qty', 'qte', 'quantity', 'nb', 'nombre'],
        'prix_unitaire'    => ['prix_unitaire', 'prix unitaire', 'prix', 'price', 'pu', 'tarif', 'prix_vente', 'pv'],
        'remise'           => ['remise', 'discount', 'reduction', 'réduction', 'rabais', 'remise_ligne'],
    ];

    private array $paymentMap = [
        'wave'         => 'wave',
        'orange money' => 'om',
        'orange_money' => 'om',
        'om'           => 'om',
        'cash'         => 'cash',
        'especes'      => 'cash',
        'espèces'      => 'cash',
        'liquide'      => 'cash',
        'mix'          => 'mix',
        'mixx'         => 'mix',
        'carte'        => 'card',
        'card'         => 'card',
        'visa'         => 'card',
        'mastercard'   => 'card',
    ];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) return;

        $headers = $rows->first()->toArray();

        // Feuille Instructions — on l'ignore silencieusement
        if ($this->isInstructionSheet($headers)) return;

        $map = $this->buildMap($headers);

        if (!isset($map['date']) || !isset($map['produit'])) {
            $missing = [];
            if (!isset($map['date']))    $missing[] = 'date';
            if (!isset($map['produit'])) $missing[] = 'produit';
            $this->errors[] = 'Colonnes introuvables : ' . implode(', ', $missing)
                            . '. Colonnes détectées : ' . implode(', ', array_filter($headers));
            return;
        }

        $groups = [];
        foreach ($rows->slice(1) as $index => $row) {
            $line = $index + 2;
            $data = $row->toArray();

            $date    = trim($this->get($data, $map, 'date'));
            $produit = trim($this->get($data, $map, 'produit'));

            if (!$date || !$produit) {
                $this->errors[] = "Ligne {$line} ignorée : date ou produit manquant.";
                continue;
            }

            $parsedDate = $this->parseDate($date);
            if (!$parsedDate) {
                $this->errors[] = "Ligne {$line} ignorée : date invalide ({$date}).";
                continue;
            }

            $key = $parsedDate->format('Y-m-d')
                 . '|' . trim($this->get($data, $map, 'client'))
                 . '|' . strtolower(trim($this->get($data, $map, 'mode_paiement') ?: 'cash'));

            $groups[$key][] = ['data' => $data, 'line' => $line, 'date' => $parsedDate];
        }

        $settings = CompanySetting::first();

        DB::transaction(function () use ($groups, $settings, $map) {
            foreach ($groups as $key => $items) {
                $firstData = $items[0]['data'];
                $soldAt    = $items[0]['date'];

                $customerId = null;
                $clientNom  = trim($this->get($firstData, $map, 'client'));
                $clientTel  = trim($this->get($firstData, $map, 'telephone_client'));
                if ($clientNom) {
                    $customer   = Customer::firstOrCreate(
                        ['name' => $clientNom],
                        ['phone' => $clientTel ?: null]
                    );
                    $customerId = $customer->id;
                }

                $rawMethod = strtolower(trim($this->get($firstData, $map, 'mode_paiement') ?: 'cash'));
                $method    = $this->paymentMap[$rawMethod] ?? 'cash';

                $cartLines = [];
                $subtotal  = 0;

                foreach ($items as $item) {
                    $data    = $item['data'];
                    $line    = $item['line'];
                    $nomProd = trim($this->get($data, $map, 'produit'));
                    $qty     = max(1, (int) ($this->get($data, $map, 'quantite') ?: 1));
                    $prix    = $this->parseNumber($this->get($data, $map, 'prix_unitaire') ?: 0);

                    if ($prix <= 0) {
                        $this->errors[] = "Ligne {$line} ({$nomProd}) ignorée : prix invalide.";
                        continue;
                    }

                    $product = Product::where('name', $nomProd)->first();
                    if (!$product) {
                        $this->errors[] = "Ligne {$line} : produit \"{$nomProd}\" introuvable — ignoré.";
                        continue;
                    }

                    $remise    = $this->parseNumber($this->get($data, $map, 'remise') ?: 0);
                    $totalLine = ($prix * $qty) - $remise;
                    $subtotal += $totalLine;

                    $cartLines[] = [
                        'product_id'     => $product->id,
                        'product_name'   => $product->name,
                        'unit_price_ht'  => $prix,
                        'unit_price_ttc' => $prix,
                        'quantity'       => $qty,
                        'discount'       => $remise,
                        'total_ht'       => $totalLine,
                        'total_ttc'      => $totalLine,
                    ];
                }

                if (empty($cartLines)) continue;

                $sale = Sale::create([
                    'reference'      => $this->generateReference(),
                    'customer_id'    => $customerId,
                    'user_id'        => Auth::id(),
                    'source'         => 'shop',
                    'status'         => 'completed',
                    'subtotal_ht'    => $subtotal,
                    'tva_amount'     => 0,
                    'total_ttc'      => $subtotal,
                    'discount'       => 0,
                    'amount_paid'    => $subtotal,
                    'change_given'   => 0,
                    'payment_method' => $method,
                    'sold_at'        => $soldAt,
                ]);

                $stockService = app(StockService::class);

                foreach ($cartLines as $cartLine) {
                    SaleItem::create(array_merge($cartLine, ['sale_id' => $sale->id]));

                    $product = Product::find($cartLine['product_id']);
                    $stockService->decrementStock(
                        $product,
                        $cartLine['quantity'],
                        'sale',
                        $sale->id,
                        'Import vente ' . $sale->reference
                    );
                }

                if ($settings) {
                    Invoice::create([
                        'sale_id'        => $sale->id,
                        'customer_id'    => $customerId,
                        'invoice_number' => $settings->invoice_prefix
                                         . '-' . date('Y') . '-'
                                         . str_pad($settings->invoice_counter, 4, '0', STR_PAD_LEFT),
                        'status'         => 'issued',
                        'subtotal_ht'    => $subtotal,
                        'tva_rate'       => 0,
                        'tva_amount'     => 0,
                        'total_ttc'      => $subtotal,
                        'issued_at'      => $soldAt,
                    ]);
                    $settings->increment('invoice_counter');
                }

                $this->created++;
            }
        });
    }

    private function isInstructionSheet(array $headers): bool
    {
        $first = strtolower(trim((string)($headers[0] ?? '')));
        return in_array($first, ['colonne', 'column', 'champ', 'field', 'instructions']);
    }

    private function parseDate($value): ?Carbon
    {
        // Numéro de série Excel
        if (is_numeric($value) && $value > 1000) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (\Exception $e) {
                return null;
            }
        }

        $str = trim((string) $value);
        if (!$str) return null;

        // DD/MM/YYYY ou DD/MM/YY (format français courant)
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{2,4})$#', $str)) {
            try { return Carbon::createFromFormat(strlen(explode('/', $str)[2]) === 2 ? 'd/m/y' : 'd/m/Y', $str); } catch (\Exception $e) {}
        }
        // DD-MM-YYYY
        if (preg_match('#^(\d{1,2})-(\d{1,2})-(\d{4})$#', $str)) {
            try { return Carbon::createFromFormat('d-m-Y', $str); } catch (\Exception $e) {}
        }
        // DD.MM.YYYY
        if (preg_match('#^(\d{1,2})\.(\d{1,2})\.(\d{4})$#', $str)) {
            try { return Carbon::createFromFormat('d.m.Y', $str); } catch (\Exception $e) {}
        }

        try {
            return Carbon::parse($str);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function buildMap(array $headers): array
    {
        $map = [];
        foreach ($this->aliases as $field => $variants) {
            foreach ($headers as $index => $header) {
                $normalized = $this->normalize((string) $header);
                foreach ($variants as $variant) {
                    if ($normalized === $this->normalize($variant)) {
                        $map[$field] = $index;
                        break 2;
                    }
                }
            }
        }
        return $map;
    }

    private function get(array $data, array $map, string $field): mixed
    {
        if (!isset($map[$field])) return '';
        return $data[$map[$field]] ?? '';
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = str_replace(['é','è','ê','ë'], 'e', $value);
        $value = str_replace(['à','â'], 'a', $value);
        $value = str_replace(['ù','û'], 'u', $value);
        $value = str_replace(['î','ï'], 'i', $value);
        $value = str_replace(['ô'], 'o', $value);
        $value = str_replace(['ç'], 'c', $value);
        $value = preg_replace('/[\s_\-]+/', '_', $value);
        return $value;
    }

    private function generateReference(): string
    {
        $last = Sale::withTrashed()->latest()->first();
        $num  = $last ? (intval(substr($last->reference, -4)) + 1) : 1;
        return 'VNT-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function parseNumber($value): float
    {
        return (float) str_replace([' ', ','], ['', '.'], (string) $value);
    }
}
