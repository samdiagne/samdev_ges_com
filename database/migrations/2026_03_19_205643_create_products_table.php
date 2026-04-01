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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();               // Code produit
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('purchase_price', 12, 2);      // Prix d'achat HT
            $table->decimal('selling_price', 12, 2);       // Prix de vente HT
            $table->decimal('selling_price_ttc', 12, 2);   // Prix TTC (calculé)
            $table->integer('stock_quantity')->default(0);
            $table->integer('stock_alert_threshold')->default(5);
            $table->string('unit')->default('unité');      // unité, paire, lot...
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Mis en avant sur le site
            $table->boolean('has_tva')->default(true);
            $table->string('main_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
