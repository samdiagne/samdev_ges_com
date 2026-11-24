<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Achat;
use App\Models\AchatReglement;
use App\Models\Parametre;
use App\Livewire\Dashboard\DashboardStats;
use App\Livewire\Rapports\RapportManager;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RapportEtDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Categorie $categorie;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');
        Parametre::firstOrCreate(['nom' => "Maf'food"]);

        $this->categorie = Categorie::create([
            'nom'        => 'Plats',
            'slug'       => 'plats',
            'est_active' => true,
        ]);
    }

    public function test_rapport_ventes_ventile_paiement_multiple_et_trie_par_jour(): void
    {
        $this->actingAs($this->admin);

        // Vente jour J-1 en multiple : 3000 cash + 2000 wave = 5000
        Vente::create([
            'reference'     => 'VNT-TEST-001',
            'user_id'       => $this->admin->id,
            'source'        => 'pos',
            'statut'        => 'completee',
            'total'         => 5000,
            'remise'        => 0,
            'montant_recu'  => 5000,
            'mode_paiement' => 'multiple',
            'montant_cash'  => 3000,
            'montant_wave'  => 2000,
            'montant_om'    => 0,
            'montant_carte' => 0,
            'vendu_le'      => now()->subDay()->setTime(12, 0),
        ]);

        // Vente aujourd'hui en cash : 4000
        Vente::create([
            'reference'     => 'VNT-TEST-002',
            'user_id'       => $this->admin->id,
            'source'        => 'pos',
            'statut'        => 'completee',
            'total'         => 4000,
            'remise'        => 0,
            'montant_recu'  => 4000,
            'mode_paiement' => 'cash',
            'montant_cash'  => 4000,
            'vendu_le'      => now()->setTime(10, 0),
        ]);

        Livewire::test(RapportManager::class)
            ->set('dateFrom', now()->subDays(2)->toDateString())
            ->set('dateTo', now()->toDateString())
            ->set('typeRapport', 'ventes')
            ->assertViewHas('rapport', function (array $rapport) {
                $cash = $rapport['par_mode']['cash']['total'] ?? 0;
                $wave = $rapport['par_mode']['wave']['total'] ?? 0;
                $jours = array_keys($rapport['par_jour']->toArray());

                return $rapport['total'] == 9000
                    && !isset($rapport['par_mode']['multiple'])
                    && $cash == 7000
                    && $wave == 2000
                    && $jours === [now()->subDay()->format('d/m/Y'), now()->format('d/m/Y')];
            });
    }

    public function test_rapport_tresorerie_comptabilise_achat_reglements_de_la_periode(): void
    {
        $this->actingAs($this->admin);

        // Achat fait le mois dernier, mais payé aujourd'hui
        $achat = Achat::create([
            'reference'     => 'ACH-TEST-001',
            'user_id'       => $this->admin->id,
            'date_achat'    => now()->subMonth()->toDateString(),
            'montant_total' => 25000,
            'montant_paye'  => 25000,
            'montant_du'    => 0,
            'statut'        => 'paye',
        ]);

        AchatReglement::create([
            'reference'      => 'REG-TEST-001',
            'achat_id'       => $achat->id,
            'user_id'        => $this->admin->id,
            'montant'        => 25000,
            'mode_paiement'  => 'cash',
            'date_reglement' => now(),
        ]);

        Livewire::test(RapportManager::class)
            ->set('dateFrom', now()->startOfMonth()->toDateString())
            ->set('dateTo', now()->toDateString())
            ->set('typeRapport', 'tresorerie')
            ->assertViewHas('rapport', function (array $rapport) {
                return (float) $rapport['achats'] === 25000.0;
            });
    }

    public function test_dashboard_stats_exclut_produits_inactifs_et_filtre_annee_top_produits(): void
    {
        $this->actingAs($this->admin);

        // Produit inactif en rupture : ne doit pas compter
        Produit::create([
            'categorie_id'       => $this->categorie->id,
            'nom'                => 'Produit Archivé',
            'slug'               => 'produit-archive',
            'prix_achat'         => 500,
            'prix_vente'         => 1000,
            'stock'              => 0,
            'seuil_alerte_stock' => 5,
            'sur_commande'       => false,
            'est_actif'          => false,
        ]);

        // Produit actif en rupture : doit compter
        $actif = Produit::create([
            'categorie_id'       => $this->categorie->id,
            'nom'                => 'Produit Actif',
            'slug'               => 'produit-actif',
            'prix_achat'         => 500,
            'prix_vente'         => 1000,
            'stock'              => 0,
            'seuil_alerte_stock' => 5,
            'sur_commande'       => false,
            'est_actif'          => true,
        ]);

        // Vente l'an dernier même mois : ne doit pas apparaître dans top_produits ce mois
        $venteAnDernier = Vente::create([
            'reference'     => 'VNT-OLD-001',
            'user_id'       => $this->admin->id,
            'source'        => 'pos',
            'statut'        => 'completee',
            'total'         => 10000,
            'remise'        => 0,
            'montant_recu'  => 10000,
            'mode_paiement' => 'cash',
            'montant_cash'  => 10000,
            'vendu_le'      => now()->subYear(),
        ]);

        VenteLigne::create([
            'vente_id'      => $venteAnDernier->id,
            'produit_id'    => $actif->id,
            'nom_produit'   => 'Produit Actif',
            'prix_unitaire' => 1000,
            'quantite'      => 10,
            'remise'        => 0,
            'total'         => 10000,
        ]);

        Livewire::test(DashboardStats::class)
            ->assertViewHas('stats', fn(array $stats) => $stats['stock_rupture'] === 1)
            ->assertViewHas('recent', fn(array $recent) => $recent['top_produits']->isEmpty());
    }
}
