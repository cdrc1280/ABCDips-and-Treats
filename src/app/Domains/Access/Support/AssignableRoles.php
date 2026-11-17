<?php

namespace App\Domains\Access\Support;

use App\Domains\Access\Models\Role;

/**
 * Roles the signed-in user may hand out. Only a super admin can grant super_admin,
 * so an admin can never escalate their own (or anyone's) access past their level.
 */
final class AssignableRoles
{
    /**
     * @return array<int, string>
     */
    public static function names(): array
    {
        $canGrantSuper = (bool) auth()->user()?->hasRole('super_admin');

        return Role::query()
            ->orderBy('name')
            ->pluck('name')
            ->reject(fn (string $name) => $name === 'super_admin' && ! $canGrantSuper)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public static function options(): array
    {
        return array_map(fn (string $name) => ['label' => ucwords(str_replace('_', ' ', $name)), 'value' => $name], self::names());
    }
}
