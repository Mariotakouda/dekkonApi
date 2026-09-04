<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->permissionDefinitions();

        // 1. Créer toutes les permissions
        $permissionModels = collect($permissions)->map(function (array $perm) {
            return Permission::firstOrCreate(
                ['code' => $perm['code']],
                [
                    'name' => $perm['name'],
                    'description' => $perm['description'] ?? null,
                ]
            );
        });

        // 2. Créer les rôles avec leurs permissions associées
        $roles = $this->roleDefinitions();

        foreach ($roles as $roleCode => $roleData) {
            $role = Role::firstOrCreate(
                ['code' => $roleCode],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_system' => $roleData['is_system'] ?? false,
                ]
            );

            if ($roleData['permissions'] === '*') {
                // Administrateur : toutes les permissions
                $role->permissions()->sync($permissionModels->pluck('id'));
            } else {
                $codes = $roleData['permissions'];
                $ids = $permissionModels->whereIn('code', $codes)->pluck('id');
                $role->permissions()->sync($ids);
            }
        }

        $this->command->info('Rôles et permissions créés : ' . count($roles) . ' rôles, ' . $permissionModels->count() . ' permissions.');
    }

    /**
     * Toutes les permissions du système, par domaine (section 4 et 37 du cahier des charges).
     */
    private function permissionDefinitions(): array
    {
        return [
            // Catalogue
            ['code' => 'categories.view', 'name' => 'Voir les catégories'],
            ['code' => 'categories.manage', 'name' => 'Gérer les catégories'],
            ['code' => 'products.view', 'name' => 'Voir les produits'],
            ['code' => 'products.manage', 'name' => 'Gérer les produits'],
            ['code' => 'product_variants.manage', 'name' => 'Gérer les variantes'],
            ['code' => 'product_images.manage', 'name' => 'Gérer les images produits'],

            // Stock
            ['code' => 'inventory.view', 'name' => 'Voir le stock'],
            ['code' => 'inventory.update', 'name' => 'Modifier le stock'],
            ['code' => 'stock_movements.view', 'name' => 'Voir les mouvements de stock'],

            // Commandes
            ['code' => 'orders.view', 'name' => 'Voir les commandes'],
            ['code' => 'orders.confirm', 'name' => 'Confirmer une commande'],
            ['code' => 'orders.update', 'name' => 'Modifier une commande'],
            ['code' => 'orders.cancel', 'name' => 'Annuler une commande'],

            // Paiements
            ['code' => 'payments.view', 'name' => 'Voir les paiements'],
            ['code' => 'payments.manage', 'name' => 'Gérer les paiements'],

            // Promotions
            ['code' => 'promotions.view', 'name' => 'Voir les promotions'],
            ['code' => 'promotions.manage', 'name' => 'Gérer les promotions'],

            // Livraisons
            ['code' => 'deliveries.view', 'name' => 'Voir les livraisons'],
            ['code' => 'deliveries.manage', 'name' => 'Gérer les livraisons'],
            ['code' => 'drivers.manage', 'name' => 'Gérer les livreurs'],

            // Employés & accès
            ['code' => 'employees.view', 'name' => 'Voir les employés'],
            ['code' => 'employees.manage', 'name' => 'Gérer les employés'],
            ['code' => 'roles.manage', 'name' => 'Gérer les rôles'],
            ['code' => 'permissions.manage', 'name' => 'Gérer les permissions'],

            // Journal d'activité
            ['code' => 'activity_logs.view', 'name' => "Consulter le journal d'activité"],
        ];
    }

    /**
     * Rôles types du cahier des charges (section 6.2, exemple section 7).
     */
    private function roleDefinitions(): array
    {
        return [
            'ADMIN' => [
                'name' => 'Administrateur',
                'description' => "Accès complet à l'administration du système",
                'is_system' => true,
                'permissions' => '*',
            ],
            'STOCK_MANAGER' => [
                'name' => 'Gestionnaire de stock',
                'description' => 'Gère le catalogue et les niveaux de stock',
                'permissions' => [
                    'products.view',
                    'inventory.view',
                    'inventory.update',
                    'stock_movements.view',
                ],
            ],
            'ORDER_MANAGER' => [
                'name' => 'Gestionnaire de commandes',
                'description' => 'Gère le cycle de vie des commandes',
                'permissions' => [
                    'orders.view',
                    'orders.confirm',
                    'orders.update',
                    'orders.cancel',
                ],
            ],
            'DELIVERY_MANAGER' => [
                'name' => 'Responsable livraison',
                'description' => 'Gère les livreurs et les livraisons',
                'permissions' => [
                    'orders.view',
                    'deliveries.view',
                    'deliveries.manage',
                    'drivers.manage',
                ],
            ],
            'CUSTOMER_SERVICE' => [
                'name' => 'Service client',
                'description' => 'Support client, consultation des commandes',
                'permissions' => [
                    'orders.view',
                    'products.view',
                    'deliveries.view',
                ],
            ],
        ];
    }
}
