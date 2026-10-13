<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Product permissions
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'products.manage_variants', 'products.manage_images',

            // Category permissions
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',

            // Stock permissions
            'stock.view', 'stock.adjust', 'stock.manage_thresholds',

            // Order permissions
            'orders.view', 'orders.manage', 'orders.cancel', 'orders.refund',

            // Payment permissions
            'payments.view', 'payments.verify', 'payments.refund',

            // Shipment permissions
            'shipments.view', 'shipments.create', 'shipments.update',

            // POS permissions
            'pos.access', 'pos.sell', 'pos.void', 'pos.refund',

            // Report permissions
            'reports.view', 'reports.export',

            // User/Role permissions
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.assign',

            // Expense permissions
            'expenses.view', 'expenses.create', 'expenses.approve',

            // Audit permissions
            'audit.view',

            // Settings permissions
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $cashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        // Assign permissions to roles
        // Customer - basic permissions
        $customer->syncPermissions([
            'products.view',
            'categories.view',
            'orders.view', // own orders only via policy
        ]);

        // Cashier - POS + limited access
        $cashier->syncPermissions([
            'products.view',
            'categories.view',
            'stock.view',
            'orders.view',
            'orders.manage', // POS orders
            'pos.access',
            'pos.sell',
            'shipments.view',
        ]);

        // Admin - full operational access
        $admin->syncPermissions([
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'products.manage_variants', 'products.manage_images',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'stock.view', 'stock.adjust', 'stock.manage_thresholds',
            'orders.view', 'orders.manage', 'orders.cancel', 'orders.refund',
            'payments.view', 'payments.verify', 'payments.refund',
            'shipments.view', 'shipments.create', 'shipments.update',
            'pos.access', 'pos.sell', 'pos.void', 'pos.refund',
            'reports.view', 'reports.export',
            'users.view', 'users.create', 'users.edit',
            'roles.view', 'roles.assign',
            'expenses.view', 'expenses.create', 'expenses.approve',
            'audit.view',
            'settings.view',
        ]);

        // Owner - full access including settings
        $owner->syncPermissions(Permission::all());
    }
}
