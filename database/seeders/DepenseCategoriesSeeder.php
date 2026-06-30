<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepenseCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Loyer',
            'Électricité',
            'Eau',
            'Internet / WiFi',
            'Gaz',
            'Salaires',
            'Transport / Livraison',
            'Emballages',
            'Entretien / Nettoyage',
            'Matériel cuisine',
            'Publicité / Marketing',
            'Autres',
        ];

        foreach ($categories as $nom) {
            DB::table('depense_categories')->updateOrInsert(
                ['nom' => $nom],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
