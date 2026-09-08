<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'products.view',
            'products.create',
            'products.update',
            'products.archive',

            'inventory.view',
            'inventory.adjust',
            'inventory.receive',
            'inventory.deduct',

            'transfers.view',
            'transfers.create',
            'transfers.approve',
            'transfers.dispatch',
            'transfers.receive',
            'transfers.cancel',

            'purchase_orders.view',
            'purchase_orders.create',
            'purchase_orders.receive',

            'sales_orders.view',
            'sales_orders.create',
            'sales_orders.fulfill',

            'reports.view',

            'users.manage',

            'organization.view',
            'organization.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate(
                $permission,
                'web'
            );
        }

        $roles = [
            'Owner',
            'Admin',
            'Inventory Manager',
            'Warehouse Staff',
            'Sales Staff',
            'Procurement Staff',
            'Viewer',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role, 'web');
        }

          /*
        |--------------------------------------------------------------------------
        | Role Permissions
        |--------------------------------------------------------------------------
        */

        Role::findByName('Owner')->syncPermissions(
            Permission::all()
        );

        Role::findByName('Admin')->syncPermissions([
            'products.view',
            'products.create',
            'products.update',
            'products.archive',

            'inventory.view',
            'inventory.adjust',
            'inventory.receive',
            'inventory.deduct',

            'transfers.view',
            'transfers.create',
            'transfers.approve',
            'transfers.dispatch',
            'transfers.receive',
            'transfers.cancel',

            'purchase_orders.view',
            'purchase_orders.create',
            'purchase_orders.receive',

            'sales_orders.view',
            'sales_orders.create',
            'sales_orders.fulfill',

            'reports.view',

            'users.manage',

            'organization.view',
            'organization.manage',
        ]);

        Role::findByName('Inventory Manager')->syncPermissions([
            'products.view',
            'products.create',
            'products.update',

            'inventory.view',
            'inventory.adjust',
            'inventory.receive',
            'inventory.deduct',

            'transfers.view',
            'transfers.create',
            'transfers.approve',
            'transfers.dispatch',
            'transfers.receive',
            'transfers.cancel',

            'reports.view',
        ]);

        Role::findByName('Warehouse Staff')->syncPermissions([
            'products.view',

            'inventory.view',
            'inventory.receive',
            'inventory.deduct',

            'transfers.view',
            'transfers.receive',
        ]);

        Role::findByName('Sales Staff')->syncPermissions([
            'products.view',
            'inventory.view',

            'sales_orders.view',
            'sales_orders.create',
            'sales_orders.fulfill',
        ]);

        Role::findByName('Procurement Staff')->syncPermissions([
            'products.view',

            'purchase_orders.view',
            'purchase_orders.create',
            'purchase_orders.receive',

            'inventory.view',
            'inventory.receive',
        ]);

        Role::findByName('Viewer')->syncPermissions([
            'products.view',
            'inventory.view',
            'transfers.view',
            'reports.view',
        ]);
    }
}
