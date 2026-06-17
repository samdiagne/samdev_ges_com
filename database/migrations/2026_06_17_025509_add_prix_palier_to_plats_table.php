<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plats', function (Blueprint $table) {
            $table->integer('quantite_palier')->nullable()->after('ancien_prix');
            $table->decimal('prix_palier', 12, 2)->nullable()->after('quantite_palier');
        });
    }

    public function down(): void
    {
        Schema::table('plats', function (Blueprint $table) {
            $table->dropColumn(['quantite_palier', 'prix_palier']);
        });
    }
};
