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
        Schema::table('plats', function (Blueprint $table) {
            $table->boolean('sur_commande')->default(false)->after('seuil_alerte_stock');
        });
    }

    public function down(): void
    {
        Schema::table('plats', function (Blueprint $table) {
            $table->dropColumn('sur_commande');
        });
    }
};
