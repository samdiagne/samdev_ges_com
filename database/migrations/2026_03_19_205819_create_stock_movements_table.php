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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('type');                        // in / out / adjustment
            $table->string('source');                      // sale / purchase / adjustment / return
            $table->unsignedBigInteger('source_id')->nullable(); // ID vente ou achat
            $table->integer('quantity_before');
            $table->integer('quantity_change');            // + ou -
            $table->integer('quantity_after');
            $table->text('reason')->nullable();
            $table->timestamp('moved_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
