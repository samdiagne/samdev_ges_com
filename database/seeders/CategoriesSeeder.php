<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Plats',      'ordre' => 1],
            ['nom' => 'Entrées',    'ordre' => 2],
            ['nom' => 'Desserts',   'ordre' => 3],
            ['nom' => 'Jus',        'ordre' => 4],
            ['nom' => 'Boissons',   'ordre' => 5],
            ['nom' => 'Snacks',     'ordre' => 6],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insertOrIgnore([
                'nom'        => $cat['nom'],
                'slug'       => Str::slug($cat['nom']),
                'ordre'      => $cat['ordre'],
                'est_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
