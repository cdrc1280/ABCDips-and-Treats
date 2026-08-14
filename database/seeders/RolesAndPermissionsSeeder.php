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

        // Use firstOrCreate — NEVER deletes or modifies existing permissions.
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ─── Roles ────────────────────────────────────────────────
        // Use firstOrCreate — NEVER overwrites existing roles or their assigned users.
        $customer = Role::firstOrCreate(['name' => 'customer',    'guard_name' => 'web']);
        $staff    = Role::firstOrCreate(['name' => 'staff',       'guard_name' => 'web']);
        $manager  = Role::firstOrCreate(['name' => 'manager',     'guard_name' => 'web']);
        $admin    = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        $super    = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // ─── Permission assignments ────────────────────────────────
        // IMPORTANT: We use givePermissionTo() instead of syncPermissions().
        // syncPermissions() REMOVES existing permissions that aren't in the list.
        // givePermissionTo() only ADDS — it never removes what's already assigned.
        // This means re-deploying will never strip permissions from customized roles.

        $staffPerms = [
            'view products', 'view orders', 'manage orders',
            'view inventory', 'view production', 'view customers',
        ];
        $missingStaff = array_diff($staffPerms, $staff->permissions->pluck('name')->toArray());
        if (!empty($missingStaff)) {
            $staff->givePermissionTo($missingStaff);
        }

        $managerPerms = [
            'view products', 'create products', 'edit products',
            'view orders', 'manage orders', 'cancel orders', 'refund orders',
            'view inventory', 'manage inventory',
            'view production', 'manage production',
            'view suppliers', 'manage suppliers',
            'view payroll', 'view employees', 'manage employees',
            'view customers', 'manage customers',
            'view reviews', 'moderate reviews',
            'view analytics', 'view reports', 'export reports',
        ];
        $missingManager = array_diff($managerPerms, $manager->permissions->pluck('name')->toArray());
        if (!empty($missingManager)) {
            $manager->givePermissionTo($missingManager);
        }

        $adminPerms = Permission::all()
            ->pluck('name')
            ->reject(fn ($p) => in_array($p, ['manage roles', 'manage permissions']))
            ->toArray();
        $missingAdmin = array_diff($adminPerms, $admin->permissions->pluck('name')->toArray());
        if (!empty($missingAdmin)) {
            $admin->givePermissionTo($missingAdmin);
        }

        $superPerms = Permission::all()->pluck('name')->toArray();
        $missingSuper = array_diff($superPerms, $super->permissions->pluck('name')->toArray());
        if (!empty($missingSuper)) {
            $super->givePermissionTo($missingSuper);
        }

        // customer role intentionally has no permissions (storefront access only)
    }
}
