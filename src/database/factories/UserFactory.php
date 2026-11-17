<?php

namespace Database\Factories;

use App\Domains\Access\Models\Role;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /** Verified, able to sign in (the default state, kept for readable tests). */
    public function active(): static
    {
        return $this->state(fn () => ['email_verified_at' => now()]);
    }

    /** Super admin: passes every permission check (Gate::before). */
    public function superAdmin(): static
    {
        return $this->role('super_admin');
    }

    public function customer(): static
    {
        return $this->role('customer');
    }

    public function role(string $name): static
    {
        return $this->afterCreating(function (User $user) use ($name) {
            Role::findOrCreate($name, 'web');
            $user->assignRole($name);
        });
    }
}
