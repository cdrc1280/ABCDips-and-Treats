<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissions ──────────────────────────────────────────
        $permissions = [
            // Products
            'view products', 'create products', 'edit products', 'delete products',
            // Orders
            'view orders', 'manage orders', 'cancel orders', 'refund orders',
            // Inventory
            'view inventory', 'manage inventory',
            // Production
            'view production', 'manage production',
            // Suppliers
            'view suppliers', 'manage suppliers',
            // Payroll
            'view payroll', 'manage payroll',
            // Employees
            'view employees', 'manage employees',
            // Customers
            'view customers', 'manage customers',
            // Reviews
            'view reviews', 'moderate reviews',
            // Analytics
            'view analytics', 'view reports', 'export reports',
            // Settings
            'manage settings',
            // RBAC
            'manage roles', 'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ─── Roles ────────────────────────────────────────────────
        $customer = Role::firstOrCreate(['name' => 'customer',    'guard_name' => 'web']);
        $staff    = Role::firstOrCreate(['name' => 'staff',       'guard_name' => 'web']);
        $manager  = Role::firstOrCreate(['name' => 'manager',     'guard_name' => 'web']);
        $admin    = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        $super    = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // Customer — read only storefront
        $customer->syncPermissions([]);

        // Staff — can view most things, limited management
        $staff->syncPermissions([
            'view products', 'view orders', 'manage orders',
            'view inventory', 'view production', 'view customers',
        ]);

        // Manager — broader management without settings/RBAC
        $manager->syncPermissions([
            'view products', 'create products', 'edit products',
            'view orders', 'manage orders', 'cancel orders', 'refund orders',
            'view inventory', 'manage inventory',
            'view production', 'manage production',
            'view suppliers', 'manage suppliers',
            'view payroll', 'view employees', 'manage employees',
            'view customers', 'manage customers',
            'view reviews', 'moderate reviews',
            'view analytics', 'view reports', 'export reports',
        ]);

        // Admin — full access except RBAC management
        $admin->syncPermissions(
            Permission::all()->pluck('name')
                ->reject(fn ($p) => in_array($p, ['manage roles', 'manage permissions']))
                ->toArray()
        );

        // Super admin — all permissions (Spatie also checks gate for this role)
        $super->syncPermissions(Permission::all());
    }
}
