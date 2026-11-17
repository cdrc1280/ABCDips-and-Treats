<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Data\UserData;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class CreateUserAction
{
    public function execute(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'password' => $data->password,
                'email_verified_at' => now(),
            ]);
            $user->syncRoles($data->roles);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $user;
        });
    }
}
