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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();    // FAC-2025-0001
            $table->string('status')->default('issued');   // issued/sent/paid
            $table->decimal('subtotal_ht', 12, 2);
            $table->decimal('tva_rate', 5, 2)->default(18);
            $table->decimal('tva_amount', 12, 2);
            $table->decimal('total_ttc', 12, 2);
            $table->string('pdf_path')->nullable();
            $table->boolean('sent_whatsapp')->default(false);
            $table->boolean('sent_email')->default(false);
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
