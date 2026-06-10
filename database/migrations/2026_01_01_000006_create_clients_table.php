<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom');
            $table->string('email')->nullable()->unique();
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->enum('type', ['particulier', 'entreprise'])->default('particulier');
            $table->text('notes')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
