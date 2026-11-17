<?php

namespace Tests\Feature\Audit\ActivityLog;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_users_without_permission_are_kept_out(): void
    {
        $this->get('/admin/activity-log')->assertRedirect('/login');
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/activity-log')->assertForbidden();
    }

    public function test_model_changes_are_recorded_with_before_and_after_values(): void
    {
        $user = $this->userWith(AccessPermission::DashboardView, AccessPermission::AuditLogsView);
        $this->actingAs($user);

        $category = ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $category->update(['name' => 'Bread loaves']);

        $this->get('/admin/activity-log?search[event]=updated')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('activity-log/index')
                ->has('records.data', 1)
                ->where('records.data.0.log_name', 'product_category')
                ->where('records.data.0.causer_name', $user->name)
                ->where('records.data.0.changes.0.field', 'name')
                ->where('records.data.0.changes.0.old', 'Breads')
                ->where('records.data.0.changes.0.new', 'Bread loaves'));
    }

    public function test_hidden_and_secret_fields_are_never_logged(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView, AccessPermission::AuditLogsView));
        User::factory()->create(['password' => 'Top!Secret123']);

        // properties is a JSON column: search its text form.
        foreach (['%password%', '%Top!Secret%', '%$2y$%'] as $needle) {
            $this->assertSame(0, Activity::query()->whereRaw('CAST(properties AS TEXT) LIKE ?', [$needle])->count(), "activity_log leaked {$needle}");
        }
    }
}
