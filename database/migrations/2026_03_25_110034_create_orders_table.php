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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Client
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->text('delivery_address');
            $table->string('city')->default('Dakar');
            $table->text('notes')->nullable();

            // Montants
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Paiement
            $table->string('payment_method'); // wave/om/delivery/virement
            $table->string('payment_status')->default('pending'); // pending/paid/failed
            $table->string('paydunya_token')->nullable();
            $table->string('paydunya_invoice_token')->nullable();

            // Statut commande
            $table->string('status')->default('pending');
            // pending/confirmed/processing/shipped/delivered/cancelled

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
