<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Data\UserData;
use App\Domains\User\Models\User;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class UpdateUserAction
{
    public function execute(User $user, UserData $data): User
    {
        if ($user->hasRole('super_admin') && ! auth()->user()?->hasRole('super_admin')) {
            throw new DomainRuleViolation(__('Only a super admin can change a super admin account.'));
        }
        if ($user->is(auth()->user()) && ! in_array('super_admin', $data->roles, true) && $user->hasRole('super_admin')) {
            throw new DomainRuleViolation(__('You cannot remove your own super admin role.'));
        }

        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'password' => $data->password ?? $user->password,
            ]);
            $user->syncRoles($data->roles);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $user;
        });
    }
}
