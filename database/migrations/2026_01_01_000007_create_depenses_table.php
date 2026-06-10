<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                         // Loyer, Électricité, Salaires, Emballages...
            $table->timestamps();
        });

        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depense_categorie_id')->constrained('depense_categories');
            $table->foreignId('user_id')->constrained('users');
            $table->string('description');
            $table->decimal('montant', 12, 2);
            $table->date('date_depense');
            $table->string('mode_paiement')->default('cash'); // cash / wave / om
            $table->string('justificatif')->nullable();    // Photo reçu
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
        Schema::dropIfExists('depense_categories');
    }
};
