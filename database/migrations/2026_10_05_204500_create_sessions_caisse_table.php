<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions_caisse', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // SES-20261005-0001
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('fond_caisse_ouverture', 12, 2)->default(0);
            $table->timestamp('ouvert_le');
            $table->timestamp('ferme_le')->nullable();
            $table->string('statut')->default('ouverte'); // ouverte / fermee

            // Totaux des ventes calculés / figés à la fermeture
            $table->decimal('total_ventes_cash', 12, 2)->default(0);
            $table->decimal('total_ventes_wave', 12, 2)->default(0);
            $table->decimal('total_ventes_om', 12, 2)->default(0);
            $table->decimal('total_ventes_carte', 12, 2)->default(0);
            $table->decimal('total_ventes', 12, 2)->default(0);

            // Totaux des dépenses sorties de caisse
            $table->decimal('total_depenses_cash', 12, 2)->default(0);
            $table->decimal('total_depenses', 12, 2)->default(0);

            // Bilan espèces et écart
            $table->decimal('solde_theorique_especes', 12, 2)->default(0);
            $table->decimal('montant_reel_especes', 12, 2)->nullable();
            $table->decimal('ecart', 12, 2)->nullable(); // reel - theorique

            $table->text('notes_fermeture')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_caisse');
    }
};
