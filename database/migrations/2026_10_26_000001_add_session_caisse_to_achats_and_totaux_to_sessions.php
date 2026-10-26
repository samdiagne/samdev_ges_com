<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            $table->foreignId('session_caisse_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('sessions_caisse')
                  ->nullOnDelete();
        });

        // Ajouter les colonnes de totaux achats dans sessions_caisse
        Schema::table('sessions_caisse', function (Blueprint $table) {
            $table->decimal('total_achats_cash', 12, 2)->default(0)->after('total_depenses');
            $table->decimal('total_achats_wave', 12, 2)->default(0)->after('total_achats_cash');
            $table->decimal('total_achats', 12, 2)->default(0)->after('total_achats_wave');
        });
    }

    public function down(): void
    {
        Schema::table('sessions_caisse', function (Blueprint $table) {
            $table->dropColumn(['total_achats_cash', 'total_achats_wave', 'total_achats']);
        });

        Schema::table('achats', function (Blueprint $table) {
            $table->dropForeign(['session_caisse_id']);
            $table->dropColumn('session_caisse_id');
        });
    }
};
