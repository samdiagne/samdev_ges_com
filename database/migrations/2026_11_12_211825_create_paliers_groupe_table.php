<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paliers_groupe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->integer('quantite_min');           // seuil déclencheur (ex: 25)
            $table->decimal('prix_unitaire', 12, 2);   // prix réduit (ex: 400)
            $table->string('label')->nullable();        // "Jus 250ml ≥ 25 bouteilles"
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });

        // Palier initial : jus 250ml (categorie_id = 4), 25 bouteilles → 400 FCFA
        if (DB::table('categories')->where('id', 4)->exists()) {
            DB::table('paliers_groupe')->insert([
                'categorie_id'  => 4,
                'quantite_min'  => 25,
                'prix_unitaire' => 400.00,
                'label'         => 'Jus 250ml ≥ 25 bouteilles',
                'est_actif'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('paliers_groupe');
    }
};
