<?php

namespace Tests\Feature\Inventory\Supplier;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Inventory\Enums\SupplierPermission;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class SupplierTest extends TestCase
{
    use RefreshDatabase;

    private function admin(SupplierPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: SupplierPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Supplier
    {
        static $n = 0;
        $n++;

        return Supplier::query()->create(array_merge(['name' => "Supplier {$n}", 'contact_person' => 'Ana', 'email' => "s{$n}@example.test", 'phone' => '09171234567', 'address' => 'Cavite', 'payment_terms' => 'Net 30'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/suppliers')->assertRedirect('/login');
        $this->post('/admin/suppliers', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/suppliers')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(SupplierPermission::SuppliersView));

        $this->get('/admin/suppliers')->assertOk();
        $this->get('/admin/suppliers/create')->assertForbidden();
        $this->get("/admin/suppliers/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/suppliers/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/suppliers/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/suppliers')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('supplier/index')
                ->has('records.data', 2)
                ->where('table.name', 'suppliers')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/suppliers', ['name' => 'Golden Mill', 'contact_person' => 'Ben Reyes', 'email' => 'SALES@goldenmill.test', 'phone' => '09170000000', 'payment_terms' => 'COD', 'address' => 'Imus, Cavite'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/suppliers')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('suppliers', ['name' => 'Golden Mill', 'email' => 'sales@goldenmill.test', 'payment_terms' => 'COD']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/suppliers', [])
            ->assertSessionHasErrors(['name', 'contact_person', 'email', 'phone', 'address']);
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/suppliers/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('supplier/edit')->where('record.id', $record->id));

        $this->put("/admin/suppliers/{$record->id}", ['name' => 'Golden Mill Corp', 'contact_person' => 'Ben', 'email' => 'b@mill.test', 'phone' => '09179998888', 'payment_terms' => 'Net 15', 'address' => 'Imus'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/suppliers')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('suppliers', array_merge(['id' => $record->id], ['name' => 'Golden Mill Corp', 'payment_terms' => 'Net 15']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/suppliers/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_delete_is_refused_while_in_use(): void
    {
        $record = $this->record();
        Ingredient::query()->create(['sku' => 'ING-1', 'name' => 'Flour', 'unit' => 'kg', 'supplier_id' => $record->id]);

        $this->actingAs($this->admin())->from('/admin/suppliers')->delete("/admin/suppliers/{$record->id}")->assertRedirect('/admin/suppliers')->assertInertiaFlash('toast.type', 'error');

        $this->assertNotSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Flourish Trading']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/suppliers?search[keyword]=flourish')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
