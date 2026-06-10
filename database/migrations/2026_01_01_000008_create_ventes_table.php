<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();         // VNT-2025-0001
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('source')->default('pos');      // pos / en_ligne
            $table->string('statut')->default('completee'); // completee / en_attente / annulee
            $table->decimal('total', 12, 2);
            $table->decimal('remise', 12, 2)->default(0);
            $table->decimal('montant_recu', 12, 2);
            $table->decimal('monnaie_rendue', 12, 2)->default(0);
            $table->string('mode_paiement')->default('cash'); // cash / wave / om / carte
            $table->text('notes')->nullable();
            $table->timestamp('vendu_le');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vente_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->cascadeOnDelete();
            $table->foreignId('plat_id')->constrained('plats')->restrictOnDelete();
            $table->string('nom_plat');                    // Snapshot nom au moment de la vente
            $table->decimal('prix_unitaire', 12, 2);       // Prix au moment de la vente
            $table->integer('quantite');
            $table->decimal('remise', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vente_lignes');
        Schema::dropIfExists('ventes');
    }
};
