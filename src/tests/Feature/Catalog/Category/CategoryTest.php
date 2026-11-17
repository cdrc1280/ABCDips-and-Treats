<?php

namespace Tests\Feature\Catalog\Category;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Enums\CategoryPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(CategoryPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: CategoryPermission::cases()));
    }

    private function category(array $attributes = []): ProductCategory
    {
        static $n = 0;
        $n++;

        return ProductCategory::query()->create(array_merge(['name' => "Category {$n}", 'slug' => "category-{$n}", 'is_active' => true], $attributes));
    }

    // 1. Guests ──────────────────────────────────────────────

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/categories')->assertRedirect('/login');
        $this->post('/admin/categories', ['name' => 'X'])->assertRedirect('/login');
    }

    // 2. Permissions ─────────────────────────────────────────

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/categories')->assertForbidden();
    }

    public function test_view_only_user_cannot_create_edit_or_delete(): void
    {
        $category = $this->category();
        $this->actingAs($this->admin(CategoryPermission::CategoriesView));

        $this->get('/admin/categories')->assertOk();
        $this->get('/admin/categories/create')->assertForbidden();
        $this->post('/admin/categories', ['name' => 'New', 'slug' => 'new'])->assertForbidden();
        $this->get("/admin/categories/{$category->id}/edit")->assertForbidden();
        $this->put("/admin/categories/{$category->id}", ['name' => 'Changed', 'slug' => 'changed'])->assertForbidden();
        $this->delete("/admin/categories/{$category->id}")->assertForbidden();
    }

    // 3. Index ───────────────────────────────────────────────

    public function test_index_renders_records_filters_table_and_deferred_summary(): void
    {
        $this->category(['name' => 'Brownies', 'sort_order' => 2]);
        $this->category(['name' => 'Banana Bread', 'sort_order' => 1]);

        $this->actingAs($this->admin())
            ->get('/admin/categories')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('category/index')
                ->has('records.data', 2)
                ->where('records.data.0.name', 'Banana Bread')
                ->has('records.data.0', fn (Assert $row) => $row
                    ->where('products_count', 0)
                    ->where('has_products', false)
                    ->etc())
                ->where('filters.sort_field', 'sort_order')
                ->where('table.name', 'categories')
                ->missing('summary'));
    }

    // 4. Store / update / validation ─────────────────────────

    public function test_store_persists_with_generated_slug_and_flashes_a_toast(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/categories', [
                'name' => '  Custom Cakes ',
                'slug' => '',
                'description' => 'Made to order',
                'is_active' => '1',
                'image' => UploadedFile::fake()->image('cake.jpg'),
            ])
            ->assertRedirect('/admin/categories')
            ->assertInertiaFlash('toast.type', 'success');

        $category = ProductCategory::query()->where('slug', 'custom-cakes')->firstOrFail();
        $this->assertSame('Custom Cakes', $category->name);
        $this->assertGreaterThan(0, $category->sort_order);
        Storage::disk('public')->assertExists($category->image_path);
    }

    public function test_store_validates_required_and_unique_slug(): void
    {
        $this->category(['slug' => 'brownies']);
        $this->actingAs($this->admin());

        $this->post('/admin/categories', ['name' => ''])->assertSessionHasErrors(['name']);
        $this->post('/admin/categories', ['name' => 'Brownies', 'slug' => 'brownies'])->assertSessionHasErrors(['slug']);
    }

    public function test_unique_slug_ignores_soft_deleted_categories(): void
    {
        $this->category(['slug' => 'seasonal'])->delete();

        $this->actingAs($this->admin())
            ->post('/admin/categories', ['name' => 'Seasonal', 'slug' => 'seasonal'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/categories');

        $this->assertSame(1, ProductCategory::query()->where('slug', 'seasonal')->count());
    }

    public function test_edit_renders_record_and_deferred_switcher(): void
    {
        $category = $this->category(['name' => 'Cookies']);

        $this->actingAs($this->admin())
            ->get("/admin/categories/{$category->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('category/edit')
                ->where('record.name', 'Cookies')
                ->where('form_rules.required', ['name', 'slug'])
                ->missing('switcher')
                ->loadDeferredProps('switcher', fn (Assert $reload) => $reload->has('switcher', 1)));
    }

    public function test_update_persists_and_keeps_its_own_slug(): void
    {
        $category = $this->category(['name' => 'Cookies', 'slug' => 'cookies']);

        $this->actingAs($this->admin())
            ->put("/admin/categories/{$category->id}", ['name' => 'Cookies & Bars', 'slug' => 'cookies', 'is_active' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/categories')
            ->assertInertiaFlash('toast.type', 'success');

        $category->refresh();
        $this->assertSame('Cookies & Bars', $category->name);
        $this->assertFalse($category->is_active);
    }

    public function test_toggle_switches_visibility(): void
    {
        $category = $this->category(['is_active' => true]);

        $this->actingAs($this->admin())->patch("/admin/categories/{$category->id}/toggle", ['is_active' => false])->assertRedirect();

        $this->assertFalse($category->refresh()->is_active);
    }

    // 5. Business rules ──────────────────────────────────────

    public function test_cannot_delete_a_category_that_still_has_products(): void
    {
        $category = $this->category();
        Product::query()->create(['category_id' => $category->id, 'sku' => 'SKU-1', 'name' => 'Loaf', 'slug' => 'loaf', 'price' => 100]);

        $this->actingAs($this->admin())
            ->from('/admin/categories')
            ->delete("/admin/categories/{$category->id}")
            ->assertRedirect('/admin/categories')
            ->assertInertiaFlash('toast.type', 'error');

        $this->assertNotSoftDeleted($category);
    }

    public function test_delete_soft_deletes_and_frees_the_slug(): void
    {
        $category = $this->category(['slug' => 'old-stuff']);

        $this->actingAs($this->admin())->delete("/admin/categories/{$category->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($category);
        $this->assertStringContainsString('~deleted-', (string) ProductCategory::withTrashed()->find($category->id)->slug);
    }

    // 6. Search / filter / sort ──────────────────────────────

    public function test_index_searches_filters_and_sorts(): void
    {
        $this->category(['name' => 'Banana Bread', 'is_active' => true]);
        $this->category(['name' => 'Brownies', 'is_active' => false]);
        $this->category(['name' => 'Cheesecakes', 'is_active' => true]);
        $this->actingAs($this->admin());

        $this->get('/admin/categories?search[keyword]=brown')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1)->where('records.data.0.name', 'Brownies'));

        $this->get('/admin/categories?search[is_active]=0')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1)->where('records.data.0.name', 'Brownies'));

        $this->get('/admin/categories?sort_field=name&sort_dir=desc')
            ->assertInertia(fn (Assert $page) => $page->where('records.data.0.name', 'Cheesecakes'));
    }

    public function test_table_state_is_restored_on_a_bare_visit(): void
    {
        $this->category(['name' => 'Banana Bread']);
        $this->category(['name' => 'Brownies']);
        $this->actingAs($this->admin());

        $this->get('/admin/categories?search[keyword]=banana&sort_field=name&sort_dir=desc');

        $this->get('/admin/categories')
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.search.keyword', 'banana')
                ->where('filters.sort_dir', 'desc')
                ->has('records.data', 1));
    }
}
