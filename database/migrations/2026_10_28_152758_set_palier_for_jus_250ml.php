<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Met à jour tous les jus 250ml (hors packs) pour avoir le palier dès 25 unités au prix de 400 FCFA
        DB::table('produits')
            ->where(function ($q) {
                $q->where('id', 44)
                  ->orWhere(function ($sub) {
                      $sub->where('categorie_id', 4)
                          ->where('nom', 'like', '%250%')
                          ->where('nom', 'not like', '%pack%');
                  });
            })
            ->update([
                'quantite_palier' => 25,
                'prix_palier'     => 400.00,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir pour les variantes (laisser le produit générique 44)
        DB::table('produits')
            ->where('categorie_id', 4)
            ->where('nom', 'like', '%250%')
            ->where('id', '!=', 44)
            ->update([
                'quantite_palier' => null,
                'prix_palier'     => null,
            ]);
    }
};
