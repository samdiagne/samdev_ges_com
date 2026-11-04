<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('commande_lignes', 'details')) {
            Schema::table('commande_lignes', function (Blueprint $table) {
                $table->text('details')->nullable()->after('nom_produit');
            });
        }

        // S'assurer que le supplément Fromage (ID 88) est disponible (sur_commande = true)
        DB::table('produits')->where('id', 88)->update([
            'sur_commande' => true,
            'est_actif'    => true,
        ]);

        // Ajouter quelques suppléments classiques s'ils n'existent pas encore
        $catSupplements = DB::table('categories')->where('slug', 'supplements')->orWhere('id', 27)->first();
        if ($catSupplements) {
            $supps = [
                ['nom' => 'Oeuf', 'prix_vente' => 500, 'slug' => 'supplement-oeuf'],
                ['nom' => 'Steak / Viande hachée', 'prix_vente' => 1000, 'slug' => 'supplement-steak'],
                ['nom' => 'Sauce fromagère', 'prix_vente' => 500, 'slug' => 'supplement-sauce-fromagere'],
            ];

            foreach ($supps as $s) {
                $existe = DB::table('produits')->where('nom', $s['nom'])->where('categorie_id', $catSupplements->id)->first();
                if (! $existe) {
                    DB::table('produits')->insert([
                        'nom'                => $s['nom'],
                        'slug'               => $s['slug'],
                        'categorie_id'       => $catSupplements->id,
                        'prix_vente'         => $s['prix_vente'],
                        'stock'              => 100,
                        'seuil_alerte_stock' => 5,
                        'sur_commande'       => true,
                        'est_actif'          => true,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('commande_lignes', 'details')) {
            Schema::table('commande_lignes', function (Blueprint $table) {
                $table->dropColumn('details');
            });
        }
    }
};
