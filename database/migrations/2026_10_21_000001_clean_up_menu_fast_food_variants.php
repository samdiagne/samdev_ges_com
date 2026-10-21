<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Produit;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Mettre à jour Menu Burger (ID 69 ou nom correspondant)
        $menuBurger = Produit::find(69) ?? Produit::where('categorie_id', 25)->where('nom', 'like', '%Burger%')->first();
        if ($menuBurger) {
            $menuBurger->update([
                'nom'                => 'Menu Burger',
                'slug'               => 'menu-burger',
                'prix_vente'         => 2500.00,
                'est_actif'          => true,
                'sur_commande'       => true,
                'description_courte' => 'Menu Burger au choix avec portion de frites et boisson fraîche 250ml',
            ]);
        }

        // 2. Mettre à jour Menu Tacos (ID 75 ou création si absent)
        $menuTacos = Produit::find(75);
        if ($menuTacos) {
            $menuTacos->update([
                'nom'                => 'Menu Tacos',
                'slug'               => 'menu-tacos',
                'prix_vente'         => 3000.00,
                'est_actif'          => true,
                'sur_commande'       => true,
                'description_courte' => 'Menu Tacos au choix avec portion de frites et boisson fraîche 250ml',
            ]);
        } else {
            Produit::create([
                'id'                 => 75,
                'categorie_id'       => 25,
                'nom'                => 'Menu Tacos',
                'slug'               => 'menu-tacos',
                'prix_vente'         => 3000.00,
                'est_actif'          => true,
                'sur_commande'       => true,
                'description_courte' => 'Menu Tacos au choix avec portion de frites et boisson fraîche 250ml',
            ]);
        }

        // 3. Supprimer (soft-delete) les anciennes variantes spécifiques de menus fast-food
        Produit::whereIn('id', [76, 77, 78, 79, 92])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Produit::withTrashed()->whereIn('id', [76, 77, 78, 79, 92])->restore();

        $menuTacos = Produit::find(75);
        if ($menuTacos) {
            $menuTacos->update([
                'nom'  => 'Menu Tacos viande hachée',
                'slug' => 'menu-tacos-viande-hachee',
            ]);
        }
    }
};
