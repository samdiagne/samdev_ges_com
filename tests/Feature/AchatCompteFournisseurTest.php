<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Achat;
use App\Models\Fournisseur;
use App\Models\SessionCaisse;
use App\Models\Parametre;
use App\Livewire\Caisse\AchatForm;
use App\Livewire\Caisse\AchatManager;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AchatCompteFournisseurTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Fournisseur $fournisseur;
    private SessionCaisse $session;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');
        Parametre::firstOrCreate(['nom' => "Maf'food"]);

        $this->fournisseur = Fournisseur::create([
            'nom'       => 'Cheikh',
            'est_actif' => true,
        ]);

        $this->session = SessionCaisse::create([
            'reference'                  => 'SES-TEST-001',
            'user_id'                    => $this->admin->id,
            'statut'                     => 'ouverte',
            'fond_caisse_ouverture'      => 100000,
            'fond_caisse_wave_ouverture' => 50000,
            'ouvert_le'                  => now(),
        ]);
    }

    public function test_achat_form_versement_global_solde_achat_du_jour_et_impute_anciennes_factures_fifo(): void
    {
        $this->actingAs($this->admin);

        // Deux anciennes factures impayées totalisant 107 600 F
        $achat1 = Achat::create([
            'reference'      => 'ACH-OLD-001',
            'fournisseur_id' => $this->fournisseur->id,
            'user_id'        => $this->admin->id,
            'statut'         => 'recu',
            'montant_total'  => 60000,
            'montant_paye'   => 0,
            'montant_du'     => 60000,
            'date_achat'     => now()->subDays(5)->toDateString(),
        ]);

        $achat2 = Achat::create([
            'reference'      => 'ACH-OLD-002',
            'fournisseur_id' => $this->fournisseur->id,
            'user_id'        => $this->admin->id,
            'statut'         => 'recu',
            'montant_total'  => 47600,
            'montant_paye'   => 0,
            'montant_du'     => 47600,
            'date_achat'     => now()->subDays(2)->toDateString(),
        ]);

        // Nouvel achat du jour de 12 675 F avec versement global de 25 000 F
        Livewire::test(AchatForm::class)
            ->set('fournisseur_id', $this->fournisseur->id)
            ->set('date_achat', now()->toDateString())
            ->set('mode_paiement', 'cash')
            ->set('lignes', [
                ['description' => 'Marchandise du jour', 'quantity' => 1, 'unit' => '', 'unit_price' => 12675],
            ])
            ->call('recalcTotals')
            ->set('montant_paye', 25000)
            ->call('save')
            ->assertHasNoErrors();

        $nouvelAchat = Achat::where('reference', 'not like', 'ACH-OLD-%')->firstOrFail();

        // L'achat du jour vaut bien 12 675 F (pas de double comptage de dette) et est soldé
        $this->assertEquals(12675.0, (float) $nouvelAchat->montant_total);
        $this->assertEquals(12675.0, (float) $nouvelAchat->montant_paye);
        $this->assertEquals(0.0, (float) $nouvelAchat->montant_du);

        // Le surplus (25 000 - 12 675 = 12 325 F) a réduit la plus ancienne facture (achat1)
        $achat1->refresh();
        $achat2->refresh();
        $this->assertEquals(12325.0, (float) $achat1->montant_paye);
        $this->assertEquals(47675.0, (float) $achat1->montant_du);
        $this->assertEquals(0.0, (float) $achat2->montant_paye);
        $this->assertEquals(47600.0, (float) $achat2->montant_du);
    }

    public function test_achat_manager_reglement_global_fournisseur_fifo(): void
    {
        $this->actingAs($this->admin);

        $achat1 = Achat::create([
            'reference'      => 'ACH-OLD-001',
            'fournisseur_id' => $this->fournisseur->id,
            'user_id'        => $this->admin->id,
            'statut'         => 'partiel',
            'montant_total'  => 47675,
            'montant_paye'   => 0,
            'montant_du'     => 47675,
            'date_achat'     => now()->subDays(5)->toDateString(),
        ]);

        $achat2 = Achat::create([
            'reference'      => 'ACH-OLD-002',
            'fournisseur_id' => $this->fournisseur->id,
            'user_id'        => $this->admin->id,
            'statut'         => 'recu',
            'montant_total'  => 47600,
            'montant_paye'   => 0,
            'montant_du'     => 47600,
            'date_achat'     => now()->subDays(2)->toDateString(),
        ]);

        Livewire::test(AchatManager::class)
            ->call('ouvrirReglementFournisseur', $this->fournisseur->id)
            ->set('fournisseurReglementMontant', 50000)
            ->set('fournisseurReglementMode', 'cash')
            ->call('validerReglementFournisseur')
            ->assertHasNoErrors();

        $achat1->refresh();
        $achat2->refresh();

        $this->assertEquals(0.0, (float) $achat1->montant_du);
        $this->assertEquals('recu', $achat1->statut);
        $this->assertEquals(2325.0, (float) $achat2->montant_paye);
        $this->assertEquals(45275.0, (float) $achat2->montant_du);
    }
}
