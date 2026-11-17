<?php

namespace Tests;

use App\Domains\Access\Models\Permission;
use App\Domains\User\Models\User;
use BackedEnum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Feature tests assert server output, not the compiled frontend bundle.
        $this->withoutVite();
    }

    /**
     * A verified user holding exactly these permissions (enum cases or names).
     */
    protected function userWith(BackedEnum|string ...$permissions): User
    {
        $user = User::factory()->active()->create();

        foreach ($permissions as $permission) {
            $name = $permission instanceof BackedEnum ? (string) $permission->value : $permission;
            Permission::findOrCreate($name, 'web');
            $user->givePermissionTo($name);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }
}
