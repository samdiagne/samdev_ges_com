<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('status')->default('draft');
            $table->string('client_nom')->nullable();
            $table->string('client_telephone')->nullable();
            $table->decimal('sous_total', 12, 2)->default(0);
            $table->decimal('remise', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('valide_jusqua')->nullable();
            $table->boolean('envoye_whatsapp')->default(false);
            $table->timestamp('date_devis');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('plat_id')->nullable()->constrained('plats')->nullOnDelete();
            $table->string('description');
            $table->decimal('prix_unitaire', 12, 2);
            $table->integer('quantite')->default(1);
            $table->decimal('remise', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->boolean('is_free_line')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};
