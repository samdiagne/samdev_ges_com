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
        Schema::table('produits', function (Blueprint $table) {
            if (! Schema::hasColumn('produits', 'est_menu')) {
                $table->boolean('est_menu')->default(false)->after('est_variante');
            }
            if (! Schema::hasColumn('produits', 'mode_prix_menu')) {
                $table->string('mode_prix_menu', 20)->default('fixe')->after('est_menu'); // 'fixe' ou 'supplement'
            }
            if (! Schema::hasColumn('produits', 'supplement_menu')) {
                $table->decimal('supplement_menu', 10, 2)->default(0)->after('mode_prix_menu');
            }
        });

        if (! Schema::hasTable('menu_composants')) {
            Schema::create('menu_composants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
                $table->string('titre'); // Ex: "Choix du sandwich", "Accompagnement", "Boisson au choix"
                $table->string('type_selection', 20)->default('categorie'); // 'categorie' ou 'produit_fixe'
                $table->foreignId('categorie_id')->nullable()->constrained('categories')->cascadeOnDelete();
                $table->foreignId('produit_fixe_id')->nullable()->constrained('produits')->nullOnDelete();
                $table->unsignedTinyInteger('ordre')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_composants');

        Schema::table('produits', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('produits', 'supplement_menu')) {
                $columnsToDrop[] = 'supplement_menu';
            }
            if (Schema::hasColumn('produits', 'mode_prix_menu')) {
                $columnsToDrop[] = 'mode_prix_menu';
            }
            if (Schema::hasColumn('produits', 'est_menu')) {
                $columnsToDrop[] = 'est_menu';
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
