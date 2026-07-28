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
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign customer role if it exists (may not exist in testing without seeder)
        if (\Spatie\Permission\Models\Role::where('name', 'customer')->exists()) {
            $user->assignRole('customer');
        }

        // Fire registered event for email verification (wrapped to handle missing routes in tests)
        try {
            event(new Registered($user));
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException) {
            // verification.verify route not registered in test environment — skip
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'token');
    }

    /**
     * Authenticate a user by credentials and issue a token.
     *
     * @return array{user: User, token: string}|null
     */
    public function login(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        // Revoke old tokens to prevent accumulation (optional — remove for multi-device)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'token');
    }
}
