<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Électricité',   'icon' => 'bolt',      'color' => '#F5A800'],
            ['name' => 'Internet/WiFi', 'icon' => 'wifi',      'color' => '#1E6FBF'],
            ['name' => 'Eau',           'icon' => 'droplet',   'color' => '#4DB8FF'],
            ['name' => 'Loyer',         'icon' => 'building',  'color' => '#2D2D2D'],
            ['name' => 'Transport',     'icon' => 'truck',     'color' => '#10B981'],
            ['name' => 'Salaires',      'icon' => 'users',     'color' => '#8B5CF6'],
            ['name' => 'Fournitures',   'icon' => 'package',   'color' => '#EF4444'],
            ['name' => 'Autres',        'icon' => 'more',      'color' => '#6B7280'],
        ];

        foreach ($categories as $cat) {
            DB::table('expense_categories')->insertOrIgnore([
                ...$cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}