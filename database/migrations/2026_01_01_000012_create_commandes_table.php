<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();         // CMD-2025-0001
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vente_id')->nullable()->constrained('ventes')->nullOnDelete(); // Lié si traité en caisse

            // Infos client
            $table->string('nom_client');
            $table->string('email_client')->nullable();
            $table->string('telephone_client');
            $table->text('adresse_livraison');
            $table->string('ville')->default('Dakar');
            $table->text('notes')->nullable();             // Instructions spéciales (sans oignon, etc.)

            // Montants
            $table->decimal('sous_total', 12, 2)->default(0);
            $table->decimal('frais_livraison', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Paiement
            $table->string('mode_paiement');               // wave / om / cash_livraison
            $table->string('statut_paiement')->default('en_attente'); // en_attente / paye / echoue
            $table->string('paydunya_token')->nullable();
            $table->string('paydunya_invoice_token')->nullable();

            // Statut commande
            $table->string('statut')->default('en_attente');
            // en_attente / confirmee / en_preparation / prete / livree / annulee

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('commande_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->foreignId('plat_id')->nullable()->constrained('plats')->nullOnDelete();
            $table->string('nom_plat');                    // Snapshot
            $table->decimal('prix_unitaire', 12, 2);
            $table->integer('quantite');
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_lignes');
        Schema::dropIfExists('commandes');
    }
};
