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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();         // VNT-2025-0001
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained();   // Gestionnaire/caissier
            $table->string('source')->default('shop');     // shop / website
            $table->string('status')->default('completed'); // completed/pending/cancelled
            $table->decimal('subtotal_ht', 12, 2);
            $table->decimal('tva_amount', 12, 2)->default(0);
            $table->decimal('total_ttc', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('change_given', 12, 2)->default(0);
            $table->string('payment_method')->default('cash'); // cash/wave/om/card
            $table->text('notes')->nullable();
            $table->timestamp('sold_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
