<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProduitsImport implements ToCollection
{
    public array $errors  = [];
    public int   $created = 0;
    public int   $updated = 0;

    // Variantes acceptées pour chaque champ
    private array $aliases = [
        'nom'           => ['nom', 'name', 'produit', 'designation', 'désignation', 'libelle', 'libellé', 'article'],
        'categorie'     => ['categorie', 'catégorie', 'category', 'famille', 'type', 'rayon'],
        'fournisseur'   => ['fournisseur', 'supplier', 'fabricant', 'marque_fournisseur'],
        'prix_achat'    => ['prix_achat', 'prix achat', 'prix d achat', 'prix_d_achat', 'cout', 'coût', 'price_achat', 'achat', 'pa', 'prix_revient'],
        'prix_vente'    => ['prix_vente', 'prix vente', 'prix de vente', 'prix_de_vente', 'price', 'prix', 'pv', 'tarif', 'selling_price', 'vente'],
        'stock'         => ['stock', 'quantite', 'quantité', 'qty', 'qte', 'stock_initial', 'inventaire'],
        'seuil_alerte'  => ['seuil_alerte', 'seuil alerte', 'alerte', 'stock_min', 'minimum'],
        'unite'         => ['unite', 'unité', 'unit', 'uom', 'conditionnement'],
        'marque'        => ['marque', 'brand', 'fabricant'],
        'description'   => ['description', 'desc', 'details', 'détails', 'observations', 'notes'],
    ];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) return;

        // Première ligne = en-têtes
        $headers = $rows->first()->toArray();

        // Feuille Instructions — on l'ignore silencieusement
        if ($this->isInstructionSheet($headers)) return;

        $map = $this->buildMap($headers);

        if (!isset($map['nom'])) {
            $this->errors[] = 'Colonne "nom/désignation/produit" introuvable. Colonnes détectées : ' . implode(', ', array_filter($headers));
            return;
        }

        foreach ($rows->slice(1) as $index => $row) {
            $line = $index + 2;
            $data = $row->toArray();

            $nom = trim($this->get($data, $map, 'nom'));
            if (!$nom) {
                $this->errors[] = "Ligne {$line} ignorée : nom manquant.";
                continue;
            }

            $prixVente = $this->parseNumber($this->get($data, $map, 'prix_vente'));
            if ($prixVente <= 0) {
                $this->errors[] = "Ligne {$line} ({$nom}) ignorée : prix de vente manquant ou invalide.";
                continue;
            }

            $categorieName = trim($this->get($data, $map, 'categorie'));
            if (!$categorieName) {
                $this->errors[] = "Ligne {$line} ({$nom}) ignorée : catégorie manquante.";
                continue;
            }

            $category = Category::firstOrCreate(
                ['name' => $categorieName],
                ['slug' => Str::slug($categorieName)]
            );

            $supplierId = null;
            $fournisseurName = trim($this->get($data, $map, 'fournisseur'));
            if ($fournisseurName) {
                $supplier   = Supplier::firstOrCreate(['name' => $fournisseurName]);
                $supplierId = $supplier->id;
            }

            $prixAchat = $this->parseNumber($this->get($data, $map, 'prix_achat'));
            $unite     = trim($this->get($data, $map, 'unite')) ?: 'unité';
            $stock     = (int) $this->get($data, $map, 'stock');
            $seuil     = (int) ($this->get($data, $map, 'seuil_alerte') ?: 5);
            $marque    = trim($this->get($data, $map, 'marque')) ?: null;
            $desc      = trim($this->get($data, $map, 'description')) ?: null;

            $productData = [
                'category_id'           => $category->id,
                'supplier_id'           => $supplierId,
                'name'                  => $nom,
                'purchase_price'        => $prixAchat,
                'selling_price'         => $prixVente,
                'selling_price_ttc'     => $prixVente,
                'stock_quantity'        => $stock,
                'stock_alert_threshold' => $seuil,
                'unit'                  => $unite,
                'brand'                 => $marque,
                'description'           => $desc,
                'has_tva'               => false,
                'is_active'             => true,
            ];

            $existing = Product::withTrashed()->where('name', $nom)->first();
            if ($existing) {
                $existing->restore();
                $existing->update($productData);
                $this->updated++;
            } else {
                Product::create($productData);
                $this->created++;
            }
        }
    }

    // Construit un tableau [champ => index_colonne] en matchant les en-têtes
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

    private function isInstructionSheet(array $headers): bool
    {
        $first = strtolower(trim((string)($headers[0] ?? '')));
        return in_array($first, ['colonne', 'column', 'champ', 'field', 'instructions']);
    }

    private function parseNumber($value): float
    {
        // Supprime tout sauf chiffres, virgule et point (gère "450 000 FCFA", etc.)
        $clean = preg_replace('/[^\d,.]/', '', str_replace(' ', '', (string) $value));
        return (float) str_replace(',', '.', $clean);
    }
}
