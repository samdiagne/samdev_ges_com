<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('produits', 'est_variante')) {
            Schema::table('produits', function (Blueprint $table) {
                $table->boolean('est_variante')->default(false)->after('est_mis_en_avant');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('produits', 'est_variante')) {
            Schema::table('produits', function (Blueprint $table) {
                $table->dropColumn('est_variante');
            });
        }
    }
};
