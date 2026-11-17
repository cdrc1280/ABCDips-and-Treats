<?php

namespace Tests\Feature\Production\ProductionBatch;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Production\Enums\ProductionBatchPermission;
use App\Domains\Production\Models\ProductionBatch;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class ProductionBatchTest extends TestCase
{
    use RefreshDatabase;

    private function admin(ProductionBatchPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: ProductionBatchPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): ProductionBatch
    {
        static $n = 0;
        $n++;
        $category = ProductCategory::query()->create(['name' => "Cat {$n}", 'slug' => "cat-{$n}"]);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => "P-{$n}", 'name' => "Loaf {$n}", 'slug' => "loaf-{$n}", 'price' => 100, 'inventory_mode' => Product::MODE_BAKED_AHEAD]);
        $recipe = Recipe::query()->create(['product_id' => $product->id, 'name' => "Recipe {$n}"]);

        return ProductionBatch::query()->create(array_merge(['batch_number' => "PB-{$n}", 'recipe_id' => $recipe->id, 'product_id' => $product->id, 'planned_qty' => 10, 'status' => 'planned'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/production-batches')->assertRedirect('/login');
        $this->post('/admin/production-batches', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/production-batches')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(ProductionBatchPermission::ProductionBatchesView));

        $this->get('/admin/production-batches')->assertOk();
        $this->get('/admin/production-batches/create')->assertForbidden();
        $this->get("/admin/production-batches/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/production-batches/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/production-batches/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/production-batches')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('production-batch/index')
                ->has('records.data', 2)
                ->where('table.name', 'production_batches')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $category = ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => 'P-X', 'name' => 'Banana loaf', 'slug' => 'banana-loaf', 'price' => 350, 'inventory_mode' => Product::MODE_BAKED_AHEAD]);
        $recipe = Recipe::query()->create(['product_id' => $product->id, 'name' => 'Banana loaf recipe']);

        $this->actingAs($this->admin())
            ->post('/admin/production-batches', ['recipe_id' => $recipe->id, 'product_id' => $product->id, 'planned_qty' => '24', 'status' => 'planned'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/production-batches')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('production_batches', ['product_id' => $product->id, 'planned_qty' => 24]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/production-batches', [])
            ->assertSessionHasErrors(['recipe_id', 'product_id']);
    }

    public function test_unique_batch_number_ignores_soft_deleted_rows(): void
    {
        $category = ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => 'P-X', 'name' => 'Banana loaf', 'slug' => 'banana-loaf', 'price' => 350, 'inventory_mode' => Product::MODE_BAKED_AHEAD]);
        $recipe = Recipe::query()->create(['product_id' => $product->id, 'name' => 'Banana loaf recipe']);

        $existing = $this->record();
        $payload = array_merge(['recipe_id' => $recipe->id, 'product_id' => $product->id, 'planned_qty' => '24', 'status' => 'planned'], ['batch_number' => $existing->batch_number]);

        $this->actingAs($this->admin())->post('/admin/production-batches', $payload)->assertSessionHasErrors(['batch_number']);

        $existing->delete();
        $this->post('/admin/production-batches', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $category = ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => 'P-X', 'name' => 'Banana loaf', 'slug' => 'banana-loaf', 'price' => 350, 'inventory_mode' => Product::MODE_BAKED_AHEAD]);
        $recipe = Recipe::query()->create(['product_id' => $product->id, 'name' => 'Banana loaf recipe']);

        $this->actingAs($this->admin())
            ->get("/admin/production-batches/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('production-batch/edit')->where('record.id', $record->id));

        $this->put("/admin/production-batches/{$record->id}", ['batch_number' => 'PB-EDIT', 'recipe_id' => $recipe->id, 'product_id' => $product->id, 'planned_qty' => '12', 'status' => 'baking'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/production-batches')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('production_batches', array_merge(['id' => $record->id], ['batch_number' => 'PB-EDIT', 'status' => 'baking']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/production-batches/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['batch_number' => 'PB-UBE-01']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/production-batches?search[keyword]=ube')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
