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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('status')->default('draft'); // draft/sent/accepted/refused/expired
            $table->string('customer_name')->nullable();  // Client rapide
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal_ht', 12, 2)->default(0);
            $table->decimal('tva_amount', 12, 2)->default(0);
            $table->decimal('total_ttc', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tva_rate', 5, 2)->default(18);
            $table->text('notes')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('sent_whatsapp')->default(false);
            $table->timestamp('quote_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
