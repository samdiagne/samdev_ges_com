<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achats', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();         // ACH-2025-0001
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('statut')->default('recu');     // commande / recu / partiel / annule
            $table->decimal('montant_total', 12, 2);
            $table->decimal('montant_paye', 12, 2)->default(0);
            $table->decimal('montant_du', 12, 2)->default(0);
            $table->string('mode_paiement')->nullable();   // cash / wave / om / credit
            $table->date('date_achat');
            $table->date('date_livraison_prevue')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('achat_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achat_id')->constrained('achats')->cascadeOnDelete();
            $table->string('description');                 // Ex: "Riz 50kg", "Huile 5L"
            $table->decimal('prix_unitaire', 12, 2);
            $table->integer('quantite');
            $table->string('unite')->nullable();           // kg, L, sac, carton...
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achat_lignes');
        Schema::dropIfExists('achats');
    }
};
