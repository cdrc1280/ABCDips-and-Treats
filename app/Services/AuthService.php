<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register a new user and issue a token.
     *
     * @return array{user: User}
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign customer role if it exists (may not exist in testing without seeder)
        if (\Spatie\Permission\Models\Role::where('name', 'customer')->exists()) {
            $user->assignRole('customer');
        }

        $user->load('roles');

        // Fire registered event to trigger verification email
        event(new Registered($user));

        $token = $user->createToken('auth-token')->plainTextToken;

        return compact('user', 'token');
    }

    /**
     * Authenticate a user by credentials.
     *
     * @return array{user: User, token: string}|null
     */
    public function login(array $credentials): ?array
    {
        $user = User::with('roles')->where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return compact('user', 'token');
    }
}
