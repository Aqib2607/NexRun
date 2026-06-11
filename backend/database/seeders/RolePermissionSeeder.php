<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create Roles ───────────────────────────────────
        $roles = [
            ['role_name' => 'Customer',         'slug' => 'customer',          'description' => 'Default customer role'],
            ['role_name' => 'Support Agent',     'slug' => 'support-agent',     'description' => 'Customer support agent'],
            ['role_name' => 'Warehouse Manager', 'slug' => 'warehouse-manager', 'description' => 'Manages inventory and warehouses'],
            ['role_name' => 'Marketing Manager', 'slug' => 'marketing-manager', 'description' => 'Manages coupons and campaigns'],
            ['role_name' => 'Administrator',     'slug' => 'administrator',     'description' => 'Full system access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }

        // ── Create Permissions ─────────────────────────────
        $permissions = [
            // Products
            ['permission_name' => 'products.view',    'module' => 'Products',   'description' => 'View products'],
            ['permission_name' => 'products.create',  'module' => 'Products',   'description' => 'Create products'],
            ['permission_name' => 'products.update',  'module' => 'Products',   'description' => 'Update products'],
            ['permission_name' => 'products.delete',  'module' => 'Products',   'description' => 'Delete products'],
            // Categories
            ['permission_name' => 'categories.view',   'module' => 'Categories', 'description' => 'View categories'],
            ['permission_name' => 'categories.manage', 'module' => 'Categories', 'description' => 'Manage categories'],
            // Inventory
            ['permission_name' => 'inventory.view',   'module' => 'Inventory',  'description' => 'View inventory'],
            ['permission_name' => 'inventory.manage', 'module' => 'Inventory',  'description' => 'Manage inventory'],
            // Orders
            ['permission_name' => 'orders.view',     'module' => 'Orders',     'description' => 'View all orders'],
            ['permission_name' => 'orders.manage',   'module' => 'Orders',     'description' => 'Manage orders'],
            ['permission_name' => 'orders.own',      'module' => 'Orders',     'description' => 'View own orders'],
            // Payments
            ['permission_name' => 'payments.view',   'module' => 'Payments',   'description' => 'View payments'],
            ['permission_name' => 'payments.refund', 'module' => 'Payments',   'description' => 'Process refunds'],
            // Coupons
            ['permission_name' => 'coupons.view',    'module' => 'Coupons',    'description' => 'View coupons'],
            ['permission_name' => 'coupons.manage',  'module' => 'Coupons',    'description' => 'Manage coupons'],
            // Reviews
            ['permission_name' => 'reviews.create',   'module' => 'Reviews',   'description' => 'Create reviews'],
            ['permission_name' => 'reviews.moderate', 'module' => 'Reviews',   'description' => 'Moderate reviews'],
            // Users
            ['permission_name' => 'users.view',      'module' => 'Users',      'description' => 'View users'],
            ['permission_name' => 'users.manage',    'module' => 'Users',      'description' => 'Manage users'],
            // Support
            ['permission_name' => 'support.view',    'module' => 'Support',    'description' => 'View tickets'],
            ['permission_name' => 'support.manage',  'module' => 'Support',    'description' => 'Manage tickets'],
            ['permission_name' => 'support.own',     'module' => 'Support',    'description' => 'Own tickets'],
            // Analytics
            ['permission_name' => 'analytics.view',  'module' => 'Analytics',  'description' => 'View analytics'],
            // Settings
            ['permission_name' => 'settings.manage', 'module' => 'Settings',   'description' => 'Manage settings'],
            // Audit
            ['permission_name' => 'audit.view',      'module' => 'Audit',      'description' => 'View audit logs'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['permission_name' => $perm['permission_name']], $perm);
        }

        // ── Assign Permissions to Roles ────────────────────

        $customer = Role::where('slug', 'customer')->first();
        $customer->permissions()->syncWithoutDetaching(
            Permission::whereIn('permission_name', [
                'products.view', 'categories.view', 'orders.own',
                'reviews.create', 'support.own',
            ])->pluck('id')
        );

        $supportAgent = Role::where('slug', 'support-agent')->first();
        $supportAgent->permissions()->syncWithoutDetaching(
            Permission::whereIn('permission_name', [
                'products.view', 'categories.view', 'orders.view',
                'users.view', 'support.view', 'support.manage',
            ])->pluck('id')
        );

        $warehouseManager = Role::where('slug', 'warehouse-manager')->first();
        $warehouseManager->permissions()->syncWithoutDetaching(
            Permission::whereIn('permission_name', [
                'products.view', 'categories.view', 'inventory.view',
                'inventory.manage', 'orders.view',
            ])->pluck('id')
        );

        $marketingManager = Role::where('slug', 'marketing-manager')->first();
        $marketingManager->permissions()->syncWithoutDetaching(
            Permission::whereIn('permission_name', [
                'products.view', 'categories.view', 'coupons.view',
                'coupons.manage', 'analytics.view',
            ])->pluck('id')
        );

        // Administrator gets all permissions implicitly via hasPermission() check
    }
}
