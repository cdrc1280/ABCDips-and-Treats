<?php

namespace Tests\Feature\Sales\Coupon;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\CouponPermission;
use App\Domains\Sales\Models\Coupon;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class CouponTest extends TestCase
{
    use RefreshDatabase;

    private function admin(CouponPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: CouponPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Coupon
    {
        static $n = 0;
        $n++;

        return Coupon::query()->create(array_merge(['code' => "CODE{$n}", 'type' => 'fixed', 'value' => 50, 'min_spend' => 0, 'is_active' => true], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/coupons')->assertRedirect('/login');
        $this->post('/admin/coupons', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/coupons')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(CouponPermission::CouponsView));

        $this->get('/admin/coupons')->assertOk();
        $this->get('/admin/coupons/create')->assertForbidden();
        $this->get("/admin/coupons/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/coupons/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/coupons/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/coupons')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('coupon/index')
                ->has('records.data', 2)
                ->where('table.name', 'coupons')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/coupons', ['code' => 'sweet10', 'type' => 'percent', 'value' => '10', 'min_spend' => '500', 'max_discount' => '200', 'is_active' => '1'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/coupons')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('coupons', ['code' => 'SWEET10', 'type' => 'percent']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/coupons', [])
            ->assertSessionHasErrors(['code', 'value']);
    }

    public function test_unique_code_ignores_soft_deleted_rows(): void
    {

        $existing = $this->record();
        $payload = array_merge(['code' => 'sweet10', 'type' => 'percent', 'value' => '10', 'min_spend' => '500', 'max_discount' => '200', 'is_active' => '1'], ['code' => $existing->code]);

        $this->actingAs($this->admin())->post('/admin/coupons', $payload)->assertSessionHasErrors(['code']);

        $existing->delete();
        $this->post('/admin/coupons', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/coupons/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('coupon/edit')->where('record.id', $record->id));

        $this->put("/admin/coupons/{$record->id}", ['code' => 'SWEET15', 'type' => 'percent', 'value' => '15', 'min_spend' => '0', 'is_active' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/coupons')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('coupons', array_merge(['id' => $record->id], ['code' => 'SWEET15', 'is_active' => false]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/coupons/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['code' => 'BIRTHDAY']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/coupons?search[keyword]=birth')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
