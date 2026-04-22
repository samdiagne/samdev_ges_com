<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AchatsImport implements ToCollection
{
    public array $errors  = [];
    public int   $created = 0;

    private array $aliases = [
        'date'          => ['date', 'date_achat', 'date achat', 'jour', 'day'],
        'fournisseur'   => ['fournisseur', 'supplier', 'fabricant', 'vendeur', 'provider'],
        'produit'       => ['produit', 'product', 'article', 'designation', 'désignation', 'libelle', 'libellé', 'nom', 'name'],
        'quantite'      => ['quantite', 'quantité', 'qty', 'qte', 'quantity', 'nb', 'nombre'],
        'prix_unitaire' => ['prix_unitaire', 'prix unitaire', 'prix', 'price', 'pu', 'cout_unitaire', 'coût unitaire', 'pa', 'prix_achat'],
        'montant_paye'  => ['montant_paye', 'montant payé', 'montant paye', 'paye', 'payé', 'paid', 'acompte', 'versement'],
        'mode_paiement' => ['mode_paiement', 'mode paiement', 'paiement', 'payment', 'moyen_paiement', 'reglement'],
        'notes'         => ['notes', 'note', 'observations', 'commentaire', 'remarques', 'details', 'détails'],
    ];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) return;

        $headers = $rows->first()->toArray();

        // Feuille Instructions — on l'ignore silencieusement
        if ($this->isInstructionSheet($headers)) return;

        $map = $this->buildMap($headers);

        $required = ['date', 'fournisseur', 'produit'];
        $missing  = array_filter($required, fn($f) => !isset($map[$f]));
        if (!empty($missing)) {
            $this->errors[] = 'Colonnes introuvables : ' . implode(', ', $missing)
                            . '. Colonnes détectées : ' . implode(', ', array_filter($headers));
            return;
        }

        $groups = [];
        foreach ($rows->slice(1) as $index => $row) {
            $line = $index + 2;
            $data = $row->toArray();

            $date        = trim($this->get($data, $map, 'date'));
            $fournisseur = trim($this->get($data, $map, 'fournisseur'));
            $produit     = trim($this->get($data, $map, 'produit'));

            if (!$date || !$fournisseur || !$produit) {
                $this->errors[] = "Ligne {$line} ignorée : date, fournisseur ou produit manquant.";
                continue;
            }

            $parsedDate = $this->parseDate($date);
            if (!$parsedDate) {
                $this->errors[] = "Ligne {$line} ignorée : date invalide ({$date}).";
                continue;
            }

            $key = $parsedDate->format('Y-m-d') . '|' . $fournisseur;
            $groups[$key][] = ['data' => $data, 'line' => $line, 'date' => $parsedDate];
        }

        DB::transaction(function () use ($groups, $map) {
            foreach ($groups as $key => $items) {
                $firstData    = $items[0]['data'];
                $purchaseDate = $items[0]['date'];

                $fournisseurName = trim($this->get($firstData, $map, 'fournisseur'));
                $supplier = Supplier::firstOrCreate(
                    ['name' => $fournisseurName],
                    ['is_active' => true]
                );

                $lines = [];
                $total = 0;

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

                    $totalLine = $prix * $qty;
                    $total    += $totalLine;

                    $lines[] = [
                        'product'      => $product,
                        'product_name' => $product->name,
                        'unit_price'   => $prix,
                        'quantity'     => $qty,
                        'total_price'  => $totalLine,
                    ];
                }

                if (empty($lines)) continue;

                // Sommer montant_paye sur toutes les lignes du groupe
                $amountPaid = array_sum(array_map(
                    fn($item) => $this->parseNumber($this->get($item['data'], $map, 'montant_paye') ?: 0),
                    $items
                ));
                if ($amountPaid <= 0) $amountPaid = $total;

                $purchase = Purchase::create([
                    'reference'      => $this->generateReference(),
                    'supplier_id'    => $supplier->id,
                    'user_id'        => Auth::id(),
                    'status'         => 'received',
                    'total_amount'   => $total,
                    'amount_paid'    => $amountPaid,
                    'amount_due'     => max(0, $total - $amountPaid),
                    'payment_method' => strtolower(trim($this->get($firstData, $map, 'mode_paiement') ?: 'cash')) ?: 'cash',
                    'purchase_date'  => $purchaseDate->toDateString(),
                    'notes'          => trim($this->get($firstData, $map, 'notes')) ?: null,
                ]);

                $stockService = app(StockService::class);

                foreach ($lines as $line) {
                    PurchaseItem::create([
                        'purchase_id'  => $purchase->id,
                        'product_id'   => $line['product']->id,
                        'product_name' => $line['product_name'],
                        'unit_price'   => $line['unit_price'],
                        'quantity'     => $line['quantity'],
                        'total_price'  => $line['total_price'],
                    ]);

                    $stockService->incrementStock(
                        $line['product'],
                        $line['quantity'],
                        'purchase',
                        $purchase->id,
                        'Import achat ' . $purchase->reference
                    );
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
        $last = Purchase::withTrashed()->latest()->first();
        $num  = $last ? (intval(substr($last->reference, -4)) + 1) : 1;
        return 'ACH-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function parseNumber($value): float
    {
        return (float) str_replace([' ', ','], ['', '.'], (string) $value);
    }
}
