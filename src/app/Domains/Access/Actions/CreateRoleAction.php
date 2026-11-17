<?php

namespace App\Domains\Access\Actions;

use App\Domains\Access\Data\RoleData;
use App\Domains\Access\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class CreateRoleAction
{
    public function execute(RoleData $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::query()->create([
                'name' => $data->name,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($data->permissions);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role;
        });
    }
}
