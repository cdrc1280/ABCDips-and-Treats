<?php

namespace App\Domains\User\Actions\Fortify;

use App\Domains\Access\Models\Role;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Register a storefront customer. Fortify fires Registered (verification email) after this.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ], [], [
            'name' => __('full name'),
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $user = User::create([
                'name' => trim($input['name']),
                'email' => strtolower(trim($input['email'])),
                'password' => Hash::make($input['password']),
            ]);

            if (Role::where('name', 'customer')->exists()) {
                $user->assignRole('customer');
            }

            return $user;
        });
    }
}
