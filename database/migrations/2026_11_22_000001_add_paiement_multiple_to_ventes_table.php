<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->decimal('montant_cash', 12, 2)->default(0)->after('mode_paiement');
            $table->decimal('montant_wave', 12, 2)->default(0)->after('montant_cash');
            $table->decimal('montant_om', 12, 2)->default(0)->after('montant_wave');
            $table->decimal('montant_carte', 12, 2)->default(0)->after('montant_om');
        });

        // Rétrocompatibilité : initialiser les montants pour les ventes déjà enregistrées
        DB::statement("UPDATE ventes SET montant_cash = total WHERE mode_paiement = 'cash'");
        DB::statement("UPDATE ventes SET montant_wave = total WHERE mode_paiement = 'wave'");
        DB::statement("UPDATE ventes SET montant_om = total WHERE mode_paiement = 'om'");
        DB::statement("UPDATE ventes SET montant_carte = total WHERE mode_paiement IN ('carte', 'card')");
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['montant_cash', 'montant_wave', 'montant_om', 'montant_carte']);
        });
    }
};
