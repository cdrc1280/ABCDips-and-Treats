<?php

namespace Tests\Feature\Catalog\Product;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Enums\ProductPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function admin(ProductPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: ProductPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Product
    {
        static $n = 0;
        $n++;
        $category = ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);

        return Product::query()->create(array_merge(['category_id' => $category->id, 'sku' => "SKU-{$n}", 'name' => "Product {$n}", 'slug' => "product-{$n}", 'price' => 250, 'stock_qty' => 20, 'min_stock_qty' => 5], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/products')->assertRedirect('/login');
        $this->post('/admin/products', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/products')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(ProductPermission::ProductsView));

        $this->get('/admin/products')->assertOk();
        $this->get('/admin/products/create')->assertForbidden();
        $this->get("/admin/products/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/products/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/products/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/products')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('product/index')
                ->has('records.data', 2)
                ->where('table.name', 'products')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 4)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $category = ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);

        $this->actingAs($this->admin())
            ->post('/admin/products', ['name' => 'Ube Cheese Loaf', 'slug' => '', 'category_id' => $category->id, 'sku' => 'ube-01', 'price' => '380', 'stock_qty' => '12', 'min_stock_qty' => '4', 'prep_time_minutes' => '45', 'variation_type' => 'size', 'variations' => [['label' => 'Mini', 'price_modifier' => '-100'], ['label' => '', 'price_modifier' => '']], 'tags' => 'bestseller, ube', 'is_active' => '1'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/products')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('products', ['slug' => 'ube-cheese-loaf', 'sku' => 'UBE-01', 'variation_type' => 'size']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', [])
            ->assertSessionHasErrors(['name', 'slug', 'category_id', 'sku', 'price']);
    }

    public function test_unique_slug_ignores_soft_deleted_rows(): void
    {
        $category = ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);

        $existing = $this->record();
        $payload = array_merge(['name' => 'Ube Cheese Loaf', 'slug' => '', 'category_id' => $category->id, 'sku' => 'ube-01', 'price' => '380', 'stock_qty' => '12', 'min_stock_qty' => '4', 'prep_time_minutes' => '45', 'variation_type' => 'size', 'variations' => [['label' => 'Mini', 'price_modifier' => '-100'], ['label' => '', 'price_modifier' => '']], 'tags' => 'bestseller, ube', 'is_active' => '1'], ['slug' => $existing->slug]);

        $this->actingAs($this->admin())->post('/admin/products', $payload)->assertSessionHasErrors(['slug']);

        $existing->delete();
        $this->post('/admin/products', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $category = ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);

        $this->actingAs($this->admin())
            ->get("/admin/products/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('product/edit')->where('record.id', $record->id));

        $this->put("/admin/products/{$record->id}", ['name' => 'Ube Loaf', 'slug' => 'ube-loaf', 'category_id' => $category->id, 'sku' => 'UBE-01', 'price' => '400', 'sale_price' => '350', 'stock_qty' => '8', 'min_stock_qty' => '4', 'prep_time_minutes' => '45', 'variation_type' => 'none', 'flavors' => [['name' => 'Classic', 'price_modifier' => '0']], 'tags' => 'ube', 'is_active' => '1'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/products')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('products', array_merge(['id' => $record->id], ['slug' => 'ube-loaf', 'sale_price' => 350]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/products/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_delete_is_refused_while_in_use(): void
    {
        $record = $this->record();
        $order = Order::query()->create(['order_number' => 'ORD-1', 'tracking_token' => 'tok', 'customer_name' => 'A', 'customer_email' => 'a@a.test', 'customer_phone' => '09170000000', 'fulfillment_type' => 'pickup', 'subtotal' => 250, 'total' => 250, 'payment_method' => 'gcash', 'status' => 'completed']);
        $order->items()->create(['product_id' => $record->id, 'product_name' => $record->name, 'product_sku' => $record->sku, 'unit_price' => 250, 'qty' => 1, 'subtotal' => 250]);

        $this->actingAs($this->admin())->from('/admin/products')->delete("/admin/products/{$record->id}")->assertRedirect('/admin/products')->assertInertiaFlash('toast.type', 'error');

        $this->assertNotSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Mocha Brownies']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/products?search[keyword]=mocha')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
