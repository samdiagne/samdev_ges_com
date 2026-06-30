<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer les doublons : garder l'enregistrement avec le plus petit id pour chaque nom
        DB::statement('
            DELETE FROM depense_categories
            WHERE id NOT IN (
                SELECT MIN(id) FROM depense_categories GROUP BY nom
            )
        ');

        Schema::table('depense_categories', function (Blueprint $table) {
            $table->unique('nom');
        });
    }

    public function down(): void
    {
        Schema::table('depense_categories', function (Blueprint $table) {
            $table->dropUnique(['nom']);
        });
    }
};
