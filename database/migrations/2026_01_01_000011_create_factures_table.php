<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->restrictOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('numero_facture')->unique();    // FAC-2025-0001
            $table->string('statut')->default('emise');   // emise / envoyee / payee
            $table->decimal('total', 12, 2);
            $table->string('chemin_pdf')->nullable();
            $table->boolean('envoye_whatsapp')->default(false);
            $table->boolean('envoye_email')->default(false);
            $table->timestamp('emise_le');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
