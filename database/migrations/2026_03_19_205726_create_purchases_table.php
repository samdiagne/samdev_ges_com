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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();         // ACH-2025-0001
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('status')->default('received'); // ordered/received/partial/cancelled
            $table->decimal('total_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_due', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->date('purchase_date');
            $table->date('expected_date')->nullable();     // Date livraison prévue
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
