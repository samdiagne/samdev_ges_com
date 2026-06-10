<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                         // Nom du restaurant
            $table->string('ninea')->nullable();
            $table->string('rc')->nullable();
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->string('logo')->nullable();
            $table->string('signature')->nullable();
            $table->string('cachet')->nullable();
            $table->string('devise')->default('FCFA');
            $table->string('prefixe_facture')->default('FAC');
            $table->integer('compteur_facture')->default(1);
            // Réseaux sociaux
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('lien_whatsapp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};
