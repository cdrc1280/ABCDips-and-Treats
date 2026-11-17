<?php

namespace Tests\Feature\Flows;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Enums\CategoryPermission;
use App\Domains\Catalog\Enums\ProductPermission;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

/** What staff switch on or off in the back office is exactly what customers can see and buy. */
class CatalogToStorefrontFlowTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function shopSlugs(): array
    {
        $slugs = [];
        $this->get('/shop')->assertOk()->assertInertia(function (Assert $page) use (&$slugs) {
            $slugs = array_column($page->toArray()['props']['products']['data'] ?? [], 'slug');
        });

        return $slugs;
    }

    public function test_turning_a_product_off_and_on_controls_the_shop(): void
    {
        $product = $this->product(['slug' => 'ube-crinkles']);
        $staff = $this->userWith(AccessPermission::DashboardView, ...ProductPermission::cases());

        $this->assertContains('ube-crinkles', $this->shopSlugs());

        $this->actingAs($staff)->from('/admin/products')->patch("/admin/products/{$product->id}/toggle", ['is_active' => false])->assertRedirect();
        $this->app['auth']->forgetGuards();
        $this->assertNotContains('ube-crinkles', $this->shopSlugs());
        $this->get('/products/ube-crinkles')->assertNotFound();
        $this->post('/cart/items', ['product_id' => $product->id, 'qty' => 1])->assertSessionHasErrors('product_id');

        $this->actingAs($staff)->patch("/admin/products/{$product->id}/toggle", ['is_active' => true]);
        $this->app['auth']->forgetGuards();
        $this->post('/cart/items', ['product_id' => $product->id, 'qty' => 1])->assertSessionHasNoErrors();
        $this->app['auth']->forgetGuards();
        $this->assertContains('ube-crinkles', $this->shopSlugs());
        $this->get('/products/ube-crinkles')->assertOk();
    }

    public function test_a_product_switched_off_after_it_was_added_to_the_basket_cannot_be_bought(): void
    {
        $product = $this->product();
        $customer = User::factory()->customer()->create(['email_verified_at' => now()]);
        $this->actingAs($customer)->post('/cart/items', ['product_id' => $product->id, 'qty' => 1])->assertSessionHasNoErrors();

        $product->update(['is_active' => false]);

        $this->post('/checkout', ['customer_name' => 'Ana Cruz', 'customer_email' => 'ana@example.com', 'customer_phone' => '09171234567',
            'fulfillment_type' => 'pickup', 'delivery_mode' => 'pooling', 'payment_method' => 'bank_transfer'])
            ->assertRedirect()->assertInertiaFlash('toast.type', 'error');
        $this->assertSame(0, Order::query()->count());
    }

    public function test_turning_a_category_off_hides_the_category_and_its_products(): void
    {
        $product = $this->product(['slug' => 'fudge-brownies']);
        $staff = $this->userWith(AccessPermission::DashboardView, ...CategoryPermission::cases());

        $this->actingAs($staff)->patch("/admin/categories/{$product->category_id}/toggle", ['is_active' => false])->assertRedirect();
        $this->app['auth']->forgetGuards();

        $this->get("/category/{$product->category->slug}")->assertNotFound();
        $this->get('/products/fudge-brownies')->assertNotFound();
        $this->assertNotContains('fudge-brownies', $this->shopSlugs());
    }
}
