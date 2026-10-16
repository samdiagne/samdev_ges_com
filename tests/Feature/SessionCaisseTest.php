<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SessionCaisse;
use App\Models\Produit;
use App\Models\Categorie;
use App\Services\VenteService;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionCaisseTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les rôles si non existants
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'gestionnaire', 'guard_name' => 'web']);

        $this->user = User::factory()->create([
            'is_active' => true,
        ]);
        $this->user->assignRole('gestionnaire');

        \App\Models\Parametre::firstOrCreate([
            'nom' => "Maf'food",
        ]);
    }

    public function test_can_open_and_close_session_with_ecart_calculation(): void
    {
        $this->actingAs($this->user);

        // 1. Ouvrir une session
        $session = SessionCaisse::create([
            'reference'             => SessionCaisse::genererReference(),
            'user_id'               => $this->user->id,
            'fond_caisse_ouverture' => 50000,
            'ouvert_le'             => now(),
            'statut'                => 'ouverte',
        ]);

        $this->assertDatabaseHas('sessions_caisse', [
            'id'                    => $session->id,
            'fond_caisse_ouverture' => 50000,
            'statut'                => 'ouverte',
        ]);

        // 2. Créer un produit et une vente
        $cat = Categorie::firstOrCreate(['nom' => 'Test Cat', 'slug' => 'test-cat']);
        $produit = Produit::create([
            'categorie_id'       => $cat->id,
            'nom'                => 'Plat Test',
            'slug'               => 'plat-test',
            'description_courte' => 'Court',
            'prix_achat'         => 1000,
            'prix_vente'         => 2500,
            'stock'              => 20,
            'sur_commande'       => false,
            'est_actif'          => true,
        ]);

        $venteService = app(VenteService::class);
        $vente = $venteService->creerVente(
            panier: [
                [
                    'produit_id'    => $produit->id,
                    'nom'           => $produit->nom,
                    'prix_unitaire' => 2500,
                    'quantite'      => 2,
                    'remise'        => 0,
                    'total'         => 5000,
                ],
            ],
            paiement: [
                'mode_paiement' => 'cash',
                'montant_recu'  => 5000,
            ]
        );

        // Vérifier que la vente est automatiquement liée à la session
        $this->assertEquals($session->id, $vente->session_caisse_id);

        // 3. Totaux en direct
        $totaux = $session->totaux_en_direct;
        $this->assertEquals(5000, $totaux['ventes_cash']);
        $this->assertEquals(55000, $totaux['theorique_cash']); // 50000 fond + 5000 vente

        // 4. Clôture avec écart
        $reelCompte = 54000; // manquant de 1000
        $ecart = $reelCompte - $totaux['theorique_cash'];

        $session->update([
            'ferme_le'                => now(),
            'statut'                  => 'fermee',
            'total_ventes_cash'       => $totaux['ventes_cash'],
            'total_ventes'            => $totaux['total_ventes'],
            'solde_theorique_especes' => $totaux['theorique_cash'],
            'montant_reel_especes'    => $reelCompte,
            'ecart'                   => $ecart,
            'notes_fermeture'         => 'Erreur rendu monnaie 1000F',
        ]);

        $this->assertDatabaseHas('sessions_caisse', [
            'id'                   => $session->id,
            'statut'               => 'fermee',
            'montant_reel_especes' => 54000,
            'ecart'                => -1000,
        ]);

        // 5. Test page rapport journalier
        $response = $this->get(route('caisse.rapport-journalier'));
        $response->assertStatus(200);

        // 6. Test PDF Ticket Z
        $responsePdf = $this->get(route('caisse.sessions.ticket-z', $session->id));
        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('content-type', 'application/pdf');

        // 7. Test PDF Rapport Journalier
        $responseRapportPdf = $this->get(route('caisse.rapport-journalier.pdf', ['date' => now()->toDateString()]));
        $responseRapportPdf->assertStatus(200);
        $responseRapportPdf->assertHeader('content-type', 'application/pdf');
    }

    public function test_point_of_sale_livewire_can_open_and_close_session(): void
    {
        $this->actingAs($this->user);

        \Livewire\Livewire::test(\App\Livewire\Caisse\PointOfSale::class)
            ->assertSet('sessionCaisseId', null)
            ->assertSet('afficherModalOuverture', true)
            ->set('fondCaisseOuverture', '30000')
            ->call('ouvrirSession')
            ->assertSet('afficherModalOuverture', false)
            ->assertNotSet('sessionCaisseId', null)
            ->call('preparerCloture')
            ->assertSet('afficherModalCloture', true)
            ->set('montantReelEspeces', '30000')
            ->call('cloturerSession')
            ->assertSet('afficherModalCloture', false)
            ->assertSet('afficherModalSuccesCloture', true);

        $this->assertDatabaseHas('sessions_caisse', [
            'user_id'               => $this->user->id,
            'fond_caisse_ouverture' => 30000,
            'montant_reel_especes'  => 30000,
            'ecart'                 => 0,
            'statut'                => 'fermee',
        ]);
    }

    public function test_rapport_journalier_livewire_component_renders(): void
    {
        $this->actingAs($this->user);

        \Livewire\Livewire::test(\App\Livewire\Caisse\RapportJournalier::class)
            ->assertStatus(200)
            ->assertSee('Rapport Quotidien de Caisse');
    }
}
