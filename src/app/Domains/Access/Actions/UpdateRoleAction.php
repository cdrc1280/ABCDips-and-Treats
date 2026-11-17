<?php

namespace App\Domains\Access\Actions;

use App\Domains\Access\Data\RoleData;
use App\Domains\Access\Models\Role;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class UpdateRoleAction
{
    public function execute(Role $role, RoleData $data): Role
    {
        if ($role->isSystem() && $data->name !== $role->name) {
            throw new DomainRuleViolation(__('Built-in roles cannot be renamed.'));
        }
        if ($role->name === 'super_admin') {
            throw new DomainRuleViolation(__('The super admin role always has every permission and cannot be edited.'));
        }

        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data->name,
            ]);
            $role->syncPermissions($data->permissions);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role;
        });
    }
}
