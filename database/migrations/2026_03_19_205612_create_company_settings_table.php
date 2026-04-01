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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Al-Adab Tech Store
            $table->string('ninea')->nullable();           // Numéro NINEA
            $table->string('rc')->nullable();              // Registre de commerce
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->decimal('tva_rate', 5, 2)->default(18.00);
            $table->string('currency')->default('FCFA');
            $table->string('invoice_prefix')->default('FAC');
            $table->integer('invoice_counter')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
