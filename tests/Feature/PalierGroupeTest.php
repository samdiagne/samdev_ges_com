<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\PalierGroupe;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PalierGroupeTest extends TestCase
{
    use RefreshDatabase;

    private Categorie $catJus;
    private Produit $bissap;
    private Produit $bouye;
    private Produit $gingembre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->catJus = Categorie::firstOrCreate([
            'id' => 4,
            'nom' => 'Jus',
            'slug' => 'jus',
            'est_active' => true,
        ]);

        // Palier de groupe : à partir de 25 bouteilles de jus, 400 FCFA / unité
        PalierGroupe::create([
            'categorie_id'  => $this->catJus->id,
            'quantite_min'  => 25,
            'prix_unitaire' => 400.00,
            'label'         => 'Pack 25 jus 250ml',
            'est_actif'     => true,
        ]);

        $this->bissap = Produit::create([
            'categorie_id'       => $this->catJus->id,
            'nom'                => 'Jus de Bissap 250ml',
            'slug'               => 'jus-de-bissap-250ml',
            'prix_vente'         => 500.00,
            'stock'              => 100,
            'sur_commande'       => true,
            'est_actif'          => true,
        ]);

        $this->bouye = Produit::create([
            'categorie_id'       => $this->catJus->id,
            'nom'                => 'Jus de Bouye 250ml',
            'slug'               => 'jus-de-bouye-250ml',
            'prix_vente'         => 500.00,
            'stock'              => 100,
            'sur_commande'       => true,
            'est_actif'          => true,
        ]);

        $this->gingembre = Produit::create([
            'categorie_id'       => $this->catJus->id,
            'nom'                => 'Jus de Gingembre 250ml',
            'slug'               => 'jus-de-gingembre-250ml',
            'prix_vente'         => 500.00,
            'stock'              => 100,
            'sur_commande'       => true,
            'est_actif'          => true,
        ]);
    }

    public function test_group_pricing_applies_when_sum_of_different_varieties_reaches_threshold(): void
    {
        $cartSvc = app(CartService::class);

        // Scénario : 10 Bissap + 10 Bouye + 5 Gingembre = 25 bouteilles au total
        $panier = [
            'p_' . $this->bissap->id => [
                'produit_id'   => $this->bissap->id,
                'nom'          => $this->bissap->nom,
                'prix'         => 500.00,
                'prix_base'    => 500.00,
                'categorie_id' => $this->catJus->id,
                'quantite'     => 10,
            ],
            'p_' . $this->bouye->id => [
                'produit_id'   => $this->bouye->id,
                'nom'          => $this->bouye->nom,
                'prix'         => 500.00,
                'prix_base'    => 500.00,
                'categorie_id' => $this->catJus->id,
                'quantite'     => 10,
            ],
            'p_' . $this->gingembre->id => [
                'produit_id'   => $this->gingembre->id,
                'nom'          => $this->gingembre->nom,
                'prix'         => 500.00,
                'prix_base'    => 500.00,
                'categorie_id' => $this->catJus->id,
                'quantite'     => 5,
            ],
        ];

        $panierRecalcule = $cartSvc->recalculerPaliersGroupe($panier);

        // Toutes les variétés doivent bénéficier du tarif dégressif de 400 FCFA
        $this->assertEquals(400.00, $panierRecalcule['p_' . $this->bissap->id]['prix']);
        $this->assertTrue($panierRecalcule['p_' . $this->bissap->id]['palier_groupe_applique']);

        $this->assertEquals(400.00, $panierRecalcule['p_' . $this->bouye->id]['prix']);
        $this->assertTrue($panierRecalcule['p_' . $this->bouye->id]['palier_groupe_applique']);

        $this->assertEquals(400.00, $panierRecalcule['p_' . $this->gingembre->id]['prix']);
        $this->assertTrue($panierRecalcule['p_' . $this->gingembre->id]['palier_groupe_applique']);
    }

    public function test_group_pricing_does_not_apply_if_total_is_below_threshold(): void
    {
        $cartSvc = app(CartService::class);

        // Scénario : 10 Bissap + 10 Bouye + 4 Gingembre = 24 bouteilles (seuil = 25)
        $panier = [
            'p_' . $this->bissap->id => [
                'produit_id'   => $this->bissap->id,
                'nom'          => $this->bissap->nom,
                'prix'         => 500.00,
                'prix_base'    => 500.00,
                'categorie_id' => $this->catJus->id,
                'quantite'     => 10,
            ],
            'p_' . $this->bouye->id => [
                'produit_id'   => $this->bouye->id,
                'nom'          => $this->bouye->nom,
                'prix'         => 500.00,
                'prix_base'    => 500.00,
                'categorie_id' => $this->catJus->id,
                'quantite'     => 10,
            ],
            'p_' . $this->gingembre->id => [
                'produit_id'   => $this->gingembre->id,
                'nom'          => $this->gingembre->nom,
                'prix'         => 500.00,
                'prix_base'    => 500.00,
                'categorie_id' => $this->catJus->id,
                'quantite'     => 4,
            ],
        ];

        $panierRecalcule = $cartSvc->recalculerPaliersGroupe($panier);

        // Le prix normal de 500 FCFA doit être conservé
        $this->assertEquals(500.00, $panierRecalcule['p_' . $this->bissap->id]['prix']);
        $this->assertFalse($panierRecalcule['p_' . $this->bissap->id]['palier_groupe_applique']);

        $this->assertEquals(500.00, $panierRecalcule['p_' . $this->bouye->id]['prix']);
        $this->assertFalse($panierRecalcule['p_' . $this->bouye->id]['palier_groupe_applique']);

        $this->assertEquals(500.00, $panierRecalcule['p_' . $this->gingembre->id]['prix']);
        $this->assertFalse($panierRecalcule['p_' . $this->gingembre->id]['palier_groupe_applique']);
    }

    public function test_group_pricing_works_on_pos_cart(): void
    {
        $cartSvc = app(CartService::class);

        // Lignes du panier caisse POS (15 Bissap + 10 Bouye = 25 bouteilles)
        $panierPOS = [
            [
                'produit_id'    => $this->bissap->id,
                'nom'           => 'Jus 250 ml : Bissap',
                'prix_unitaire' => 500.00,
                'prix_base'     => 500.00,
                'categorie_id'  => $this->catJus->id,
                'quantite'      => 15,
                'remise'        => 0,
                'total'         => 7500.00,
                'is_menu'       => false,
            ],
            [
                'produit_id'    => $this->bouye->id,
                'nom'           => 'Jus 250 ml : Bouye',
                'prix_unitaire' => 500.00,
                'prix_base'     => 500.00,
                'categorie_id'  => $this->catJus->id,
                'quantite'      => 10,
                'remise'        => 0,
                'total'         => 5000.00,
                'is_menu'       => false,
            ],
        ];

        $panierRecalcule = $cartSvc->recalculerPaliersGroupePOS($panierPOS);

        // 15 * 400 = 6000
        $this->assertEquals(400.00, $panierRecalcule[0]['prix_unitaire']);
        $this->assertEquals(6000.00, $panierRecalcule[0]['total']);
        $this->assertTrue($panierRecalcule[0]['palier_groupe_applique']);

        // 10 * 400 = 4000
        $this->assertEquals(400.00, $panierRecalcule[1]['prix_unitaire']);
        $this->assertEquals(4000.00, $panierRecalcule[1]['total']);
        $this->assertTrue($panierRecalcule[1]['palier_groupe_applique']);
    }
}
