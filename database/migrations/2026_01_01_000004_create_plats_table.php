<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->restrictOnDelete();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_courte')->nullable();
            $table->json('ingredients')->nullable();       // Ingrédients / allergènes (optionnel)
            $table->decimal('prix_achat', 12, 2)->default(0);  // Coût de revient
            $table->decimal('prix_vente', 12, 2);              // Prix de vente
            $table->decimal('ancien_prix', 12, 2)->nullable();  // Ancien prix (promotion)
            $table->integer('stock')->default(0);               // Portions disponibles
            $table->integer('seuil_alerte_stock')->default(5);
            $table->boolean('est_actif')->default(true);
            $table->boolean('est_mis_en_avant')->default(false); // Mis en avant sur le site
            $table->string('image_principale')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plats');
    }
};
