<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Produit;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('produits', 'est_variante')) {
            Schema::table('produits', function (Blueprint $table) {
                $table->boolean('est_variante')->default(false)->after('est_mis_en_avant');
            });
        }

        // 1. Configurer le produit générique Jus 250 ml (ID 44)
        $jus250 = Produit::find(44);
        if ($jus250) {
            $jus250->update([
                'nom'                => 'Jus 250 ml',
                'slug'               => 'jus-250-ml',
                'prix_vente'         => 500.00,
                'est_actif'          => true,
                'sur_commande'       => true,
                'est_variante'       => false,
                'description_courte' => 'Bouteille 250ml de jus naturel au choix',
            ]);
        }

        // 2. Créer ou mettre à jour le produit générique Jus 1 L (dans Catégorie 4)
        if (\Illuminate\Support\Facades\DB::table('categories')->where('id', 4)->exists()) {
            $jus1L = Produit::where('categorie_id', 4)->where('slug', 'jus-1-l')->first();
            if (! $jus1L) {
                $jus1L = Produit::create([
                    'categorie_id'       => 4,
                    'nom'                => 'Jus 1 L',
                    'slug'               => 'jus-1-l',
                    'prix_vente'         => 2000.00,
                    'est_actif'          => true,
                    'sur_commande'       => true,
                    'est_variante'       => false,
                    'description_courte' => 'Bouteille 1L de jus naturel au choix',
                ]);
            } else {
                $jus1L->update([
                    'nom'                => 'Jus 1 L',
                    'prix_vente'         => 2000.00,
                    'est_actif'          => true,
                    'sur_commande'       => true,
                    'est_variante'       => false,
                ]);
            }
        }

        // 3. Configurer le produit générique Boisson gazeuse (ID 71)
        $boissonGazeuse = Produit::find(71);
        if ($boissonGazeuse) {
            $boissonGazeuse->update([
                'nom'                => 'Boisson gazeuse',
                'slug'               => 'boisson-gazeuse',
                'prix_vente'         => 500.00,
                'est_actif'          => true,
                'sur_commande'       => true,
                'est_variante'       => false,
                'description_courte' => 'Canette 33cl au choix fraîche',
            ]);
        }

        // 4. Marquer les parfums individuels de jus comme variantes (Catégorie 4)
        $jusVarianteIds = [
            4, 5, 6, 7, 8, 9, 10, 25, 85, 86, // 1L
            26, 27, 28, 29, 30, 31, 32, 34, 74 // 250ml
        ];
        Produit::whereIn('id', $jusVarianteIds)->update([
            'est_variante' => true,
            'est_actif'    => true,
        ]);

        // 5. Créer les options de boissons gazeuses dans la Catégorie 5 (Boissons)
        $sodas = [
            ['nom' => 'Coca-Cola (33 cl)',       'slug' => 'coca-cola-33cl'],
            ['nom' => 'Coca-Cola Zéro (33 cl)',  'slug' => 'coca-cola-zero-33cl'],
            ['nom' => 'Fanta Orange (33 cl)',    'slug' => 'fanta-orange-33cl'],
            ['nom' => 'Fanta Cocktail (33 cl)',  'slug' => 'fanta-cocktail-33cl'],
            ['nom' => 'Sprite (33 cl)',          'slug' => 'sprite-33cl'],
            ['nom' => 'Schweppes (33 cl)',       'slug' => 'schweppes-33cl'],
            ['nom' => 'Vimto (33 cl)',           'slug' => 'vimto-33cl'],
        ];

        if (\Illuminate\Support\Facades\DB::table('categories')->where('id', 5)->exists()) {
            foreach ($sodas as $soda) {
                Produit::updateOrCreate(
                    ['slug' => $soda['slug']],
                    [
                        'categorie_id'       => 5,
                        'nom'                => $soda['nom'],
                        'prix_vente'         => 500.00,
                        'est_actif'          => true,
                        'sur_commande'       => true,
                        'est_variante'       => true,
                        'description_courte' => 'Canette fraîche 33cl',
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('produits', 'est_variante')) {
            Schema::table('produits', function (Blueprint $table) {
                $table->dropColumn('est_variante');
            });
        }
    }
};
