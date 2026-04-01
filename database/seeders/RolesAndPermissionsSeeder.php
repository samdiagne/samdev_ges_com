<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ──────────────────────────────────────────
        $permissions = [
            // Produits
            'products.view', 'products.create', 'products.edit', 'products.delete',

            // Catégories
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',

            // Ventes
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete',

            // Achats
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete',

            // Dépenses
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',

            // Fournisseurs
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',

            // Clients
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',

            // Factures
            'invoices.view', 'invoices.create', 'invoices.send',

            // Stock
            'stock.view', 'stock.adjust',

            // Rapports
            'reports.view', 'reports.export',

            // Paramètres
            'settings.view', 'settings.edit',

            // Utilisateurs (admin only)
            'users.view', 'users.create', 'users.edit', 'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Rôles ────────────────────────────────────────────────

        // Admin → tout
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Gestionnaire → tout sauf users et settings
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
            'stock.view', 'stock.adjust',
            'reports.view',
        ]);

        // Client → accès minimal (espace client site)
        $client = Role::firstOrCreate(['name' => 'client']);
        $client->givePermissionTo([
            'invoices.view',
        ]);

        // ── Créer l'utilisateur Admin par défaut ─────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@aladab-tech.sn'],
            [
                'name'     => 'Admin Al-Adab',
                'password' => bcrypt('Admin@2025!'),
                'phone'    => '+221000000000',
                'is_active'=> true,
            ]
        );
        $adminUser->assignRole('admin');

        // ── Créer le Gestionnaire par défaut ─────────────────────
        $gestionnaireUser = User::firstOrCreate(
            ['email' => 'caisse@aladab-tech.sn'],
            [
                'name'     => 'Gestionnaire Caisse',
                'password' => bcrypt('Caisse@2025!'),
                'phone'    => '+221000000000',
                'is_active'=> true,
            ]
        );
        $gestionnaireUser->assignRole('gestionnaire');

        $this->command->info('✅ Rôles, permissions et utilisateurs créés avec succès !');
    }
}