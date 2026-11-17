<?php

namespace Database\Seeders;

use App\Domains\Access\Models\Permission;
use App\Domains\Access\Models\Role;
use App\Domains\Access\Support\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds every permission from PermissionRegistry and the default role grants.
 * Additive only (givePermissionTo, never sync), so re-running on deploy never strips
 * permissions an admin added to a role by hand.
 */
class AccessSeeder extends Seeder
{
    /**
     * Default grants per role, as permission-name prefixes (`products-` = all product perms)
     * or exact names. super_admin needs none: Gate::before lets it through everything.
     */
    private const ROLE_DEFAULTS = [
        'admin' => ['*'],
        'manager' => [
            'dashboard-view', 'reports-', 'categories-', 'products-', 'suppliers-', 'ingredients-',
            'packaging-materials-', 'recipes-', 'product-costings-', 'orders-', 'custom-orders-',
            'delivery-pools-', 'coupons-', 'customers-', 'reviews-', 'production-batches-',
            'purchase-orders-', 'quotations-', 'employees-', 'attendances-', 'payrolls-view',
            'blog-posts-', 'suggestions-', 'contact-messages-', 'chat-escalations-', 'pos-', 'ai-advisor-',
        ],
        'staff' => [
            'dashboard-view', 'products-view', 'orders-view', 'orders-edit', 'custom-orders-view',
            'ingredients-view', 'packaging-materials-view', 'production-batches-view', 'customers-view',
            'pos-',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $names = PermissionRegistry::names();
        foreach ($names as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        foreach (['customer', 'staff', 'manager', 'admin', 'super_admin'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        foreach (self::ROLE_DEFAULTS as $roleName => $patterns) {
            $role = Role::findByName($roleName, 'web');
            $grant = array_values(array_filter($names, fn (string $name) => $this->matches($name, $patterns)));
            $missing = array_diff($grant, $role->permissions->pluck('name')->all());

            if ($missing !== []) {
                $role->givePermissionTo($missing);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  list<string>  $patterns
     */
    private function matches(string $name, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($pattern === '*' || $pattern === $name || (str_ends_with($pattern, '-') && str_starts_with($name, $pattern))) {
                return true;
            }
        }

        return false;
    }
}
