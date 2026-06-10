<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UtilisateursSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ──────────────────────────────────────────
        $permissions = [
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete',
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'invoices.view', 'invoices.create', 'invoices.send',
            'quotes.view', 'quotes.create', 'quotes.edit', 'quotes.delete',
            'stock.view', 'stock.adjust',
            'reports.view', 'reports.export',
            'settings.view', 'settings.edit',
            'users.view', 'users.create', 'users.edit', 'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Rôles ────────────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $gestionnaire = Role::firstOrCreate(['name' => 'gestionnaire']);
        $gestionnaire->givePermissionTo([
            'products.view', 'products.create', 'products.edit',
            'categories.view',
            'sales.view', 'sales.create', 'sales.edit',
            'purchases.view', 'purchases.create', 'purchases.edit',
            'expenses.view', 'expenses.create', 'expenses.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'invoices.view', 'invoices.create', 'invoices.send',
            'quotes.view', 'quotes.create', 'quotes.edit',
            'stock.view', 'stock.adjust',
            'reports.view',
        ]);

        $client = Role::firstOrCreate(['name' => 'client']);
        $client->givePermissionTo(['invoices.view']);

        // ── Utilisateurs par défaut ──────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@maffood.sn'],
            [
                'name'      => 'Admin',
                'password'  => bcrypt('Admin@2025!'),
                'is_active' => true,
            ]
        );
        $adminUser->assignRole('admin');

        $gestionnaireUser = User::firstOrCreate(
            ['email' => 'caisse@maffood.sn'],
            [
                'name'      => 'Caissier',
                'password'  => bcrypt('Caisse@2025!'),
                'is_active' => true,
            ]
        );
        $gestionnaireUser->assignRole('gestionnaire');

        $this->command->info('Roles, permissions et utilisateurs crees.');
        $this->command->info('   admin@maffood.sn   -> Admin@2025!');
        $this->command->info('   caisse@maffood.sn  -> Caisse@2025!');
    }
}
