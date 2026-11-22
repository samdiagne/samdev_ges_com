<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\SessionCaisse;
use App\Services\VenteService;
use App\Livewire\Caisse\PointOfSale;
use App\Livewire\Caisse\VenteManager;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaiementMultipleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $caissier;
    private Produit $produit;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'gestionnaire', 'guard_name' => 'web']);

        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');

        $this->caissier = User::factory()->create(['is_active' => true]);
        $this->caissier->assignRole('gestionnaire');

        \App\Models\Parametre::firstOrCreate(['nom' => "Maf'food"]);

        $catIds = [4 => 'Jus', 5 => 'Boissons', 20 => 'Burgers', 23 => 'Tacos', 25 => 'Menus Fast-Food'];
        foreach ($catIds as $id => $nom) {
            \Illuminate\Support\Facades\DB::table('categories')->insertOrIgnore([
                'id'         => $id,
                'nom'        => $nom,
                'slug'       => \Illuminate\Support\Str::slug($nom),
                'est_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $cat = Categorie::firstOrCreate(['nom' => 'Plats', 'slug' => 'plats']);
        $this->produit = Produit::create([
            'categorie_id'       => $cat->id,
            'nom'                => 'Burger Spécial',
            'slug'               => 'burger-special',
            'description_courte' => 'Délicieux burger',
            'prix_achat'         => 1500,
            'prix_vente'         => 3000,
            'stock'              => 50,
            'sur_commande'       => false,
            'est_actif'          => true,
            'est_variante'       => false,
        ]);
    }

    public function test_vente_service_creer_vente_multiple_repartit_les_montants(): void
    {
        $this->actingAs($this->caissier);

        $session = SessionCaisse::create([
            'reference'                  => SessionCaisse::genererReference(),
            'user_id'                    => $this->caissier->id,
            'fond_caisse_ouverture'      => 50000,
            'fond_caisse_wave_ouverture' => 20000,
            'ouvert_le'                  => now(),
            'statut'                     => 'ouverte',
        ]);

        $panier = [
            [
                'produit_id'    => $this->produit->id,
                'nom'           => $this->produit->nom,
                'prix_unitaire' => 3000,
                'quantite'      => 3,
                'remise'        => 0,
                'total'         => 9000,
            ]
        ];

        // Client paie 4 000 F en Wave et donne 10 000 F en espèces (total reçu = 14 000 F)
        // Sur 9 000 F de total, la monnaie rendue en espèces est 5 000 F
        // La part nette espèces conservée est donc 10 000 - 5 000 = 5 000 F
        // Wave = 4 000 F. Somme nette = 5 000 + 4 000 = 9 000 F = total !
        $venteService = app(VenteService::class);
        $vente = $venteService->creerVente(
            panier: $panier,
            paiement: [
                'mode_paiement'       => 'multiple',
                'montant_recu'        => 14000,
                'montant_cash'        => 10000,
                'montant_wave'        => 4000,
                'montant_om'          => 0,
                'montant_carte'       => 0,
                'monnaie_rendue_wave' => 0,
                'remise'              => 0,
                'session_caisse_id'   => $session->id,
            ]
        );

        $this->assertEquals('multiple', $vente->mode_paiement);
        $this->assertEquals(9000, (float) $vente->total);
        $this->assertEquals(14000, (float) $vente->montant_recu);
        $this->assertEquals(5000, (float) $vente->monnaie_rendue);
        $this->assertEquals(5000, (float) $vente->monnaie_rendue_especes);
        $this->assertEquals(5000, (float) $vente->montant_cash);
        $this->assertEquals(4000, (float) $vente->montant_wave);
        $this->assertEquals(0, (float) $vente->montant_om);
        $this->assertEquals(0, (float) $vente->montant_carte);

        // Vérifier les totaux en direct de la session
        $totaux = $session->fresh()->totaux_en_direct;
        $this->assertEquals(5000, $totaux['ventes_cash']);
        $this->assertEquals(4000, $totaux['ventes_wave']);
        $this->assertEquals(9000, $totaux['total_ventes']);
        // Tiroir espèces : 50 000 fond + 5 000 vente cash = 55 000
        $this->assertEquals(55000, $totaux['theorique_cash']);
        // Compte Wave : 20 000 fond + 4 000 vente wave = 24 000
        $this->assertEquals(24000, $totaux['theorique_wave']);
    }

    public function test_pos_livewire_can_process_multiple_payment(): void
    {
        $this->actingAs($this->caissier);

        $session = SessionCaisse::create([
            'reference'                  => SessionCaisse::genererReference(),
            'user_id'                    => $this->caissier->id,
            'fond_caisse_ouverture'      => 40000,
            'fond_caisse_wave_ouverture' => 10000,
            'ouvert_le'                  => now(),
            'statut'                     => 'ouverte',
        ]);

        $component = Livewire::test(PointOfSale::class)
            ->call('addToCart', $this->produit->id) // 1x 3 000 F
            ->set('modePaiement', 'multiple')
            ->call('processPaymentMultiple', 2000, 1000, 0, 0); // 2000 cash + 1000 wave = 3000

        $vente = Vente::latest('id')->first();
        $this->assertNotNull($vente);
        $this->assertEquals('multiple', $vente->mode_paiement);
        $this->assertEquals(3000, (float) $vente->total);
        $this->assertEquals(2000, (float) $vente->montant_cash);
        $this->assertEquals(1000, (float) $vente->montant_wave);
        $this->assertEquals(0, (float) $vente->monnaie_rendue);
    }

    public function test_vente_manager_can_edit_sale_to_multiple_payment(): void
    {
        $this->actingAs($this->admin);

        // Créer une vente simple initialement en cash
        $venteService = app(VenteService::class);
        $vente = $venteService->creerVente(
            panier: [
                [
                    'produit_id'    => $this->produit->id,
                    'nom'           => $this->produit->nom,
                    'prix_unitaire' => 3000,
                    'quantite'      => 2,
                    'remise'        => 0,
                    'total'         => 6000,
                ]
            ],
            paiement: [
                'mode_paiement' => 'cash',
                'montant_recu'  => 6000,
            ]
        );

        $this->assertEquals('cash', $vente->mode_paiement);
        $this->assertEquals(6000, (float) $vente->montant_cash);

        // Modifier la vente en tant qu'admin vers un paiement mixte : 3000 cash, 2000 wave, 1000 om
        Livewire::test(VenteManager::class)
            ->call('edit', $vente->id)
            ->set('editModePaiement', 'multiple')
            ->set('editMontantCash', 3000)
            ->set('editMontantWave', 2000)
            ->set('editMontantOm', 1000)
            ->set('editMontantCarte', 0)
            ->call('updateVente');

        $vente->refresh();
        $this->assertEquals('multiple', $vente->mode_paiement);
        $this->assertEquals(6000, (float) $vente->total);
        $this->assertEquals(3000, (float) $vente->montant_cash);
        $this->assertEquals(2000, (float) $vente->montant_wave);
        $this->assertEquals(1000, (float) $vente->montant_om);
        $this->assertEquals(0, (float) $vente->montant_carte);
    }
}
