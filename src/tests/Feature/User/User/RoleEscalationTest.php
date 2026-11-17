<?php

namespace Tests\Feature\User\User;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Access\Models\Role;
use App\Domains\User\Enums\UserPermission;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Hand-written: staff management can never be used to escalate privileges. */
class RoleEscalationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = $this->userWith(AccessPermission::DashboardView, ...UserPermission::cases());
        Role::findOrCreate('admin', 'web');
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_admin_cannot_grant_super_admin(): void
    {
        Role::findOrCreate('super_admin', 'web');
        $this->actingAs($this->admin());

        $this->post('/admin/users', ['name' => 'Sneaky', 'email' => 'sneaky@example.test', 'roles' => ['super_admin'],
            'password' => 'Bread!2026x', 'password_confirmation' => 'Bread!2026x'])->assertSessionHasErrors('roles.0');

        $this->assertDatabaseMissing('users', ['email' => 'sneaky@example.test']);
    }

    public function test_admin_cannot_edit_or_delete_a_super_admin(): void
    {
        $super = User::factory()->superAdmin()->create();
        Role::findOrCreate('staff', 'web');
        $this->actingAs($this->admin());

        $this->from('/admin/users')->put("/admin/users/{$super->id}", ['name' => 'Hacked', 'email' => $super->email, 'roles' => ['staff']])
            ->assertInertiaFlash('toast.type', 'error');
        $this->from('/admin/users')->delete("/admin/users/{$super->id}")->assertInertiaFlash('toast.type', 'error');

        $this->assertTrue($super->refresh()->hasRole('super_admin'));
        $this->assertNotSame('Hacked', $super->name);
    }

    public function test_cannot_delete_yourself(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->from('/admin/users')->delete("/admin/users/{$admin->id}")->assertInertiaFlash('toast.type', 'error');
        $this->assertNotSoftDeleted($admin);
    }

    public function test_super_admin_can_grant_super_admin(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());

        $this->post('/admin/users', ['name' => 'Co-owner', 'email' => 'owner2@example.test', 'roles' => ['super_admin'],
            'password' => 'Bread!2026x', 'password_confirmation' => 'Bread!2026x'])->assertSessionHasNoErrors();

        $this->assertTrue(User::where('email', 'owner2@example.test')->firstOrFail()->hasRole('super_admin'));
    }
}
