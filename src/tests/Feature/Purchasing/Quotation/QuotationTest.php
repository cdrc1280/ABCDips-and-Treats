<?php

namespace Tests\Feature\Purchasing\Quotation;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Purchasing\Enums\QuotationPermission;
use App\Domains\Purchasing\Models\Quotation;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class QuotationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(QuotationPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: QuotationPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Quotation
    {
        static $n = 0;
        $n++;

        return Quotation::query()->create(array_merge(['quotation_number' => "QT-{$n}", 'status' => 'draft', 'tax' => 0], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/quotations')->assertRedirect('/login');
        $this->post('/admin/quotations', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/quotations')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(QuotationPermission::QuotationsView));

        $this->get('/admin/quotations')->assertOk();
        $this->get('/admin/quotations/create')->assertForbidden();
        $this->get("/admin/quotations/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/quotations/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/quotations/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/quotations')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('quotation/index')
                ->has('records.data', 2)
                ->where('table.name', 'quotations')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);

        $this->actingAs($this->admin())
            ->post('/admin/quotations', ['status' => 'draft', 'tax' => '12', 'items' => [['ingredient_id' => (string) $flour->id, 'qty' => '10', 'unit' => 'kg', 'unit_price' => '55.5']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/quotations')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('quotations', ['status' => 'draft', 'subtotal' => 555, 'total' => 567]);
    }

    public function test_unique_quotation_number_ignores_soft_deleted_rows(): void
    {
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);

        $existing = $this->record();
        $payload = array_merge(['status' => 'draft', 'tax' => '12', 'items' => [['ingredient_id' => (string) $flour->id, 'qty' => '10', 'unit' => 'kg', 'unit_price' => '55.5']]], ['quotation_number' => $existing->quotation_number]);

        $this->actingAs($this->admin())->post('/admin/quotations', $payload)->assertSessionHasErrors(['quotation_number']);

        $existing->delete();
        $this->post('/admin/quotations', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);

        $this->actingAs($this->admin())
            ->get("/admin/quotations/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('quotation/edit')->where('record.id', $record->id));

        $this->put("/admin/quotations/{$record->id}", ['quotation_number' => 'QT-EDIT', 'status' => 'sent', 'tax' => '0', 'items' => [['ingredient_id' => (string) $flour->id, 'qty' => '2', 'unit' => 'kg', 'unit_price' => '50']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/quotations')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('quotations', array_merge(['id' => $record->id], ['status' => 'sent', 'total' => 100]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/quotations/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['notes' => 'Holiday butter order']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/quotations?search[keyword]=holiday')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
