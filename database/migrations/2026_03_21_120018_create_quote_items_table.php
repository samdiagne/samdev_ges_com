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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');           // Nom produit ou ligne libre
            $table->decimal('unit_price_ht', 12, 2);
            $table->decimal('unit_price_ttc', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('tva_rate', 5, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_ht', 12, 2);
            $table->decimal('total_ttc', 12, 2);
            $table->boolean('is_free_line')->default(false); // Ligne libre
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
