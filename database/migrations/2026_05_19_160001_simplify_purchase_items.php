<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            // Supprimer la FK product_id
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            // Renommer product_name → description (texte libre)
            $table->renameColumn('product_name', 'description');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->renameColumn('description', 'product_name');

            $table->foreignId('product_id')
                  ->nullable()
                  ->after('purchase_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }
};