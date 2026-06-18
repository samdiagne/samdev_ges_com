<?php

namespace App\Imports;

use App\Models\Categorie;
use App\Models\Plat;
use App\Models\Fournisseur;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProduitsImport implements ToCollection
{
    public array $errors  = [];
    public int   $created = 0;
    public int   $updated = 0;

    private array $aliases = [
        'nom'           => ['nom', 'name', 'produit', 'plat', 'designation', 'désignation', 'libelle', 'libellé', 'article'],
        'categorie'     => ['categorie', 'catégorie', 'category', 'famille', 'type', 'rayon'],
        'fournisseur'   => ['fournisseur', 'supplier', 'fabricant'],
        'prix_achat'    => ['prix_achat', 'prix achat', 'cout', 'coût', 'pa', 'prix_revient'],
        'prix_vente'    => ['prix_vente', 'prix vente', 'prix de vente', 'price', 'prix', 'pv', 'tarif', 'vente'],
        'stock'         => ['stock', 'quantite', 'quantité', 'qty', 'qte', 'stock_initial', 'inventaire'],
        'seuil_alerte'  => ['seuil_alerte', 'seuil alerte', 'alerte', 'stock_min', 'minimum'],
        'description'   => ['description', 'desc', 'details', 'détails', 'observations', 'notes'],
        'ingredients'   => ['ingredients', 'ingrédients', 'allergenes', 'allergènes', 'composition'],
        'sur_commande'  => ['sur_commande', 'sur commande', 'on_demand', 'made_to_order'],
    ];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) return;

        $headers = $rows->first()->toArray();

        if ($this->isInstructionSheet($headers)) return;

        $map = $this->buildMap($headers);

        if (!isset($map['nom'])) {
            $this->errors[] = 'Colonne "nom/plat/désignation" introuvable. Colonnes détectées : ' . implode(', ', array_filter($headers));
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

            $categorie = Categorie::firstOrCreate(
                ['slug' => Str::slug($categorieName)],
                ['nom'  => $categorieName, 'est_active' => true]
            );

            $fournisseurId = null;
            $fournisseurName = trim($this->get($data, $map, 'fournisseur'));
            if ($fournisseurName) {
                $fournisseur   = Fournisseur::firstOrCreate(
                    ['nom' => $fournisseurName],
                    ['est_actif' => true]
                );
                $fournisseurId = $fournisseur->id;
            }

            $prixAchat   = $this->parseNumber($this->get($data, $map, 'prix_achat'));
            $stock       = (int) $this->get($data, $map, 'stock');
            $seuil       = (int) ($this->get($data, $map, 'seuil_alerte') ?: 5);
            $desc        = trim($this->get($data, $map, 'description')) ?: null;
            $ingredients = trim($this->get($data, $map, 'ingredients')) ?: null;

            $surCommandeRaw = trim($this->get($data, $map, 'sur_commande'));
            $surCommande    = in_array(mb_strtolower($surCommandeRaw), ['1', 'oui', 'yes', 'true', 'x'], true);

            $platData = [
                'categorie_id'        => $categorie->id,
                'nom'                 => $nom,
                'slug'                => Str::slug($nom),
                'description'         => $desc,
                'ingredients'         => $ingredients ? [$ingredients] : [],
                'prix_achat'          => $prixAchat,
                'prix_vente'          => $prixVente,
                'stock'               => $stock,
                'seuil_alerte_stock'  => $seuil,
                'sur_commande'        => $surCommande,
                'est_actif'           => true,
            ];

            $existing = Plat::withTrashed()->where('nom', $nom)->first();
            if ($existing) {
                $existing->restore();
                $existing->update($platData);
                $this->updated++;
            } else {
                Plat::create($platData);
                $this->created++;
            }
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

    private function isInstructionSheet(array $headers): bool
    {
        $first = strtolower(trim((string)($headers[0] ?? '')));
        return in_array($first, ['colonne', 'column', 'champ', 'field', 'instructions']);
    }

    private function parseNumber($value): float
    {
        $clean = preg_replace('/[^\d,.]/', '', str_replace(' ', '', (string) $value));
        return (float) str_replace(',', '.', $clean);
    }
}