<?php

namespace App\Console\Commands;

use App\Models\Categorie;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Parametre;
use App\Models\Plat;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\StockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportVentes extends Command
{
    protected $signature = 'import:ventes';
    protected $description = 'Importe les ventes initiales de jus MAFFOOD';

    public function handle(StockService $stockService): int
    {
        $admin = \App\Models\User::role('admin')->first();
        if (! $admin) {
            $this->error('Aucun utilisateur admin trouvé.');
            return self::FAILURE;
        }

        Auth::login($admin);

        DB::beginTransaction();

        try {
            $produits = $this->creerProduits();
            $this->info('Produits créés : ' . count($produits));

            $clients = $this->creerClients();
            $this->info('Clients créés : ' . count($clients));

            $ventesCount = $this->creerVentes($produits, $clients, $admin->id, $stockService);
            $this->info("Ventes créées : {$ventesCount}");

            DB::commit();
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Erreur : {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }

    private function creerProduits(): array
    {
        $catJus = Categorie::where('nom', 'Jus')->first();

        $definitions = [
            'jus_250ml' => [
                'nom' => 'Jus 250ml',
                'prix_vente' => 500,
                'quantite_palier' => 25,
                'prix_palier' => 400,
                'stock' => 1000,
            ],
            'pack_7_1l' => [
                'nom' => 'Pack 7 Jus 1L',
                'prix_vente' => 15000,
                'stock' => 100,
            ],
            'bouye_1l' => [
                'nom' => 'Bouye 1L',
                'prix_vente' => 2000,
                'stock' => 100,
            ],
            'bissap_rouge_1l' => [
                'nom' => 'Bissap Rouge 1L',
                'prix_vente' => 2000,
                'stock' => 100,
            ],
            'bissap_blanc_1l' => [
                'nom' => 'Bissap Blanc 1L',
                'prix_vente' => 2000,
                'stock' => 100,
            ],
            'gingembre_ananas_1l' => [
                'nom' => 'Gingembre Ananas 1L',
                'prix_vente' => 2000,
                'stock' => 100,
            ],
            'orange_1l' => [
                'nom' => 'Orange 1L',
                'prix_vente' => 2000,
                'stock' => 100,
            ],
            'lait_vanille_1l' => [
                'nom' => 'Lait à la Vanille 1L',
                'prix_vente' => 2500,
                'stock' => 100,
            ],
            'lait_menthe_1l' => [
                'nom' => 'Lait à la Menthe 1L',
                'prix_vente' => 2500,
                'stock' => 100,
            ],
        ];

        $produits = [];
        foreach ($definitions as $cle => $def) {
            $produits[$cle] = Plat::firstOrCreate(
                ['nom' => $def['nom']],
                array_merge($def, [
                    'categorie_id' => $catJus->id,
                    'est_actif' => true,
                ])
            );
        }

        return $produits;
    }

    private function creerClients(): array
    {
        $noms = [
            'ya_faty' => 'Ya Faty',
            'cheikhouna_seck' => 'Cheikhouna Seck',
            'djily_mbaye' => 'Djily Mbaye',
            'seydou_sano' => 'Seydou Sano',
            'khadim_ndiaye' => 'Khadim Ndiaye',
            'mame_diarra_seck' => 'Mame Diarra Seck',
            'ndeye_sohna_mbaye' => 'Ndeye Sohna Mbaye',
            'sohna_seck' => 'Sohna Seck',
            'mame_cheikh' => 'Mame Cheikh',
            'mouhamed' => 'Mouhamed',
            'ndeye_fatou' => 'Ndeye Fatou',
            'fatou_seck' => 'Fatou Seck',
        ];

        $clients = [];
        foreach ($noms as $cle => $nom) {
            $clients[$cle] = Client::firstOrCreate(['nom' => $nom]);
        }

        return $clients;
    }

    private function creerVentes(array $produits, array $clients, int $userId, StockService $stockService): int
    {
        $parametres = Parametre::first();

        // Veille de Tabaski 2026
        $veilleTabaski = '2026-06-06';
        // Dates variées ce mois-ci (juin 2026)
        $juin10 = '2026-06-10';
        $juin12 = '2026-06-12';
        $juin14 = '2026-06-14';
        $juin15 = '2026-06-15';

        $ventes = [
            // --- Veille de Tabaski ---
            [
                'client' => 'ya_faty',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 75, 'prix' => 400],
                ],
            ],
            [
                'client' => 'cheikhouna_seck',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 50, 'prix' => 400],
                ],
            ],
            [
                'client' => 'djily_mbaye',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 50, 'prix' => 400],
                ],
            ],
            [
                'client' => 'seydou_sano',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 100, 'prix' => 400],
                ],
            ],
            [
                'client' => 'khadim_ndiaye',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 100, 'prix' => 400],
                    ['produit' => 'pack_7_1l', 'quantite' => 1, 'prix' => 15000],
                ],
            ],
            [
                'client' => 'mame_diarra_seck',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 100, 'prix' => 400],
                ],
            ],
            [
                'client' => 'mouhamed',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 50, 'prix' => 400],
                    ['produit' => 'pack_7_1l', 'quantite' => 1, 'prix' => 15000],
                ],
            ],
            [
                'client' => 'ndeye_fatou',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'pack_7_1l', 'quantite' => 1, 'prix' => 15000],
                ],
            ],
            [
                'client' => 'fatou_seck',
                'date' => $veilleTabaski,
                'lignes' => [
                    ['produit' => 'pack_7_1l', 'quantite' => 1, 'prix' => 15000],
                ],
            ],
            // --- Ce mois-ci (juin 2026) ---
            [
                'client' => 'ndeye_sohna_mbaye',
                'date' => $juin10,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 25, 'prix' => 400],
                ],
            ],
            [
                'client' => 'sohna_seck',
                'date' => $juin10,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 25, 'prix' => 400],
                ],
            ],
            [
                'client' => 'mame_cheikh',
                'date' => $juin12,
                'lignes' => [
                    ['produit' => 'lait_vanille_1l', 'quantite' => 2, 'prix' => 2500],
                    ['produit' => 'lait_menthe_1l', 'quantite' => 2, 'prix' => 2500],
                    ['produit' => 'bouye_1l', 'quantite' => 1, 'prix' => 2000],
                    ['produit' => 'bissap_rouge_1l', 'quantite' => 1, 'prix' => 2000],
                ],
            ],
            [
                'client' => 'mame_cheikh',
                'date' => $juin14,
                'lignes' => [
                    ['produit' => 'bissap_rouge_1l', 'quantite' => 1, 'prix' => 2000],
                ],
            ],
            [
                'client' => 'mame_cheikh',
                'date' => $juin15,
                'lignes' => [
                    ['produit' => 'bissap_rouge_1l', 'quantite' => 3, 'prix' => 2000],
                ],
            ],
            [
                'client' => null,
                'date' => $juin14,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 50, 'prix' => 400],
                ],
            ],
            [
                'client' => null,
                'date' => $juin15,
                'lignes' => [
                    ['produit' => 'jus_250ml', 'quantite' => 50, 'prix' => 400],
                ],
            ],
        ];

        $count = 0;
        foreach ($ventes as $venteData) {
            $clientId = null;
            if ($venteData['client'] && isset($clients[$venteData['client']])) {
                $clientId = $clients[$venteData['client']]->id;
            }

            $total = 0;
            foreach ($venteData['lignes'] as $ligne) {
                $total += $ligne['prix'] * $ligne['quantite'];
            }

            $vente = Vente::create([
                'reference' => $this->genererReference(),
                'client_id' => $clientId,
                'user_id' => $userId,
                'source' => 'pos',
                'statut' => 'completee',
                'total' => $total,
                'remise' => 0,
                'montant_recu' => $total,
                'monnaie_rendue' => 0,
                'mode_paiement' => 'cash',
                'vendu_le' => $venteData['date'],
            ]);

            foreach ($venteData['lignes'] as $ligne) {
                $plat = $produits[$ligne['produit']];
                $ligneTotal = $ligne['prix'] * $ligne['quantite'];

                VenteLigne::create([
                    'vente_id' => $vente->id,
                    'plat_id' => $plat->id,
                    'nom_plat' => $plat->nom,
                    'prix_unitaire' => $ligne['prix'],
                    'quantite' => $ligne['quantite'],
                    'remise' => 0,
                    'total' => $ligneTotal,
                ]);

                $stockService->decrementerStock(
                    $plat,
                    $ligne['quantite'],
                    'vente',
                    $vente->id,
                    'Vente #' . $vente->reference
                );
            }

            Facture::create([
                'vente_id' => $vente->id,
                'client_id' => $clientId,
                'numero_facture' => $this->genererNumeroFacture($parametres),
                'statut' => 'emise',
                'total' => $total,
                'emise_le' => $venteData['date'],
            ]);
            $parametres?->increment('compteur_facture');

            $count++;
        }

        return $count;
    }

    private function genererReference(): string
    {
        $derniere = Vente::withTrashed()->latest('id')->first();
        $num = $derniere ? (intval(substr($derniere->reference, -4)) + 1) : 1;
        return 'VNT-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function genererNumeroFacture(?Parametre $parametres): string
    {
        $prefixe = $parametres?->prefixe_facture ?? 'FAC';
        $compteur = $parametres?->compteur_facture ?? 1;
        return $prefixe . '-' . date('Y') . '-' . str_pad($compteur, 4, '0', STR_PAD_LEFT);
    }
}
