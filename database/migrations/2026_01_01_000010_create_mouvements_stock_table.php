<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plat_id')->constrained('plats')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type');                        // entree / sortie / ajustement
            $table->string('source');                      // vente / achat / ajustement / retour
            $table->unsignedBigInteger('source_id')->nullable();
            $table->integer('stock_avant');
            $table->integer('quantite_changement');        // + ou -
            $table->integer('stock_apres');
            $table->text('motif')->nullable();
            $table->timestamp('effectue_le');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
