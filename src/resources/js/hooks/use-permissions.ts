import { usePage } from '@inertiajs/react';

/**
 * Permission checks against the shared `auth.permissions`.
 * Super admins pass every check (mirrors Gate::before in AppServiceProvider).
 */
export function usePermissions() {
    const { auth } = usePage().props;
    const granted = new Set(auth.permissions);

    const can = (permission?: string | null): boolean => !permission || auth.is_super_admin || granted.has(permission);
    const canAny = (...permissions: string[]): boolean => permissions.some((permission) => can(permission));

    return { can, canAny, isSuperAdmin: auth.is_super_admin };
}
