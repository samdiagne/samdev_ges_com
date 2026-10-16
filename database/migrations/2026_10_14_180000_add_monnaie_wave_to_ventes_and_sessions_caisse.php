<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sur chaque vente : détail de la monnaie rendue par canal
        Schema::table('ventes', function (Blueprint $table) {
            $table->decimal('monnaie_rendue_especes', 12, 2)->default(0)->after('monnaie_rendue');
            $table->decimal('monnaie_rendue_wave',    12, 2)->default(0)->after('monnaie_rendue_especes');
        });

        // Sur chaque session : fond Wave initial + suivi Wave + bilan Wave
        Schema::table('sessions_caisse', function (Blueprint $table) {
            $table->decimal('fond_caisse_wave_ouverture',  12, 2)->default(0)->after('fond_caisse_ouverture');
            $table->decimal('total_monnaie_rendue_wave',   12, 2)->default(0)->after('total_ventes_carte');
            $table->decimal('solde_theorique_wave',        12, 2)->default(0)->after('solde_theorique_especes');
            $table->decimal('montant_reel_wave',           12, 2)->nullable()->after('montant_reel_especes');
            $table->decimal('ecart_wave',                  12, 2)->nullable()->after('ecart');
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['monnaie_rendue_especes', 'monnaie_rendue_wave']);
        });

        Schema::table('sessions_caisse', function (Blueprint $table) {
            $table->dropColumn([
                'fond_caisse_wave_ouverture',
                'total_monnaie_rendue_wave',
                'solde_theorique_wave',
                'montant_reel_wave',
                'ecart_wave',
            ]);
        });
    }
};
