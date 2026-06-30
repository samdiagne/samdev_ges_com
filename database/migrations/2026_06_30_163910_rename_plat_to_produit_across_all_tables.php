<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tables principales
        Schema::rename('plats', 'produits');
        Schema::rename('plat_images', 'produit_images');

        // 2. Colonnes dans les tables associées
        Schema::table('produit_images', function (Blueprint $table) {
            $table->renameColumn('plat_id', 'produit_id');
        });

        Schema::table('avis', function (Blueprint $table) {
            $table->renameColumn('plat_id', 'produit_id');
        });

        Schema::table('vente_lignes', function (Blueprint $table) {
            $table->renameColumn('plat_id', 'produit_id');
            $table->renameColumn('nom_plat', 'nom_produit');
        });

        Schema::table('commande_lignes', function (Blueprint $table) {
            $table->renameColumn('plat_id', 'produit_id');
            $table->renameColumn('nom_plat', 'nom_produit');
        });

        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->renameColumn('plat_id', 'produit_id');
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->renameColumn('plat_id', 'produit_id');
        });
    }

    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->renameColumn('produit_id', 'plat_id');
        });

        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->renameColumn('produit_id', 'plat_id');
        });

        Schema::table('commande_lignes', function (Blueprint $table) {
            $table->renameColumn('nom_produit', 'nom_plat');
            $table->renameColumn('produit_id', 'plat_id');
        });

        Schema::table('vente_lignes', function (Blueprint $table) {
            $table->renameColumn('nom_produit', 'nom_plat');
            $table->renameColumn('produit_id', 'plat_id');
        });

        Schema::table('avis', function (Blueprint $table) {
            $table->renameColumn('produit_id', 'plat_id');
        });

        Schema::table('produit_images', function (Blueprint $table) {
            $table->renameColumn('produit_id', 'plat_id');
        });

        Schema::rename('produit_images', 'plat_images');
        Schema::rename('produits', 'plats');
    }
};
