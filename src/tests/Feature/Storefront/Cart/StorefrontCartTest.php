<?php

namespace Tests\Feature\Storefront\Cart;

use App\Domains\Sales\Models\Cart;
use App\Domains\Sales\Models\CartItem;
use App\Domains\Storefront\Support\CurrentCart;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

class StorefrontCartTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_browsing_does_not_create_carts(): void
    {
        $this->get('/')->assertOk();
        $this->get('/cart')->assertOk();

        $this->assertSame(0, Cart::query()->count());
    }

    public function test_guest_adds_updates_removes_and_restores(): void
    {
        $product = $this->product(['price' => 250]);

        $this->post('/cart/items', ['product_id' => $product->id, 'qty' => 2, 'options' => ['unit_price' => 1]])
            ->assertRedirect()
            ->assertSessionHas(CurrentCart::SESSION_KEY);

        $item = CartItem::query()->firstOrFail();
        $this->assertEquals(250, (float) $item->unit_price);

        $this->get('/shop')->assertInertia(fn (Assert $page) => $page->where('cart.item_count', 2)->where('cart.total', 500));

        $this->patch("/cart/items/{$item->id}", ['qty' => 3])->assertRedirect();
        $this->assertSame(3, $item->fresh()->qty);

        $this->delete("/cart/items/{$item->id}")->assertRedirect();
        $this->assertSoftDeleted($item);

        $this->post("/cart/items/{$item->id}/restore")->assertRedirect();
        $this->assertNotSoftDeleted($item);
    }

    public function test_visitors_cannot_touch_someone_elses_cart_lines(): void
    {
        $product = $this->product();
        $other = Cart::query()->create(['token' => 'other', 'last_active_at' => now(), 'expires_at' => now()->addDay()]);
        $line = CartItem::query()->create(['cart_id' => $other->id, 'product_id' => $product->id, 'qty' => 1, 'unit_price' => 300]);

        $this->patch("/cart/items/{$line->id}", ['qty' => 9])->assertNotFound();
        $this->delete("/cart/items/{$line->id}")->assertNotFound();
        $this->assertSame(1, $line->fresh()->qty);
    }

    public function test_guest_cart_merges_into_account_on_login(): void
    {
        $product = $this->product();
        $user = User::factory()->customer()->create(['password' => 'Password123!']);

        $this->post('/cart/items', ['product_id' => $product->id, 'qty' => 2]);
        $this->post('/login', ['email' => $user->email, 'password' => 'Password123!'])->assertRedirect();

        $cart = Cart::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertSame(2, (int) $cart->items()->sum('qty'));
    }

    public function test_invalid_coupon_returns_a_field_error(): void
    {
        $this->post('/cart/items', ['product_id' => $this->product()->id, 'qty' => 1]);

        $this->post('/cart/coupon', ['code' => 'NOPE'])->assertSessionHasErrors('code');
    }

    public function test_wishlist_requires_login_and_toggles(): void
    {
        $product = $this->product();
        $this->post("/wishlist/{$product->id}")->assertRedirect('/login');

        $user = User::factory()->customer()->create();
        $this->actingAs($user)->post("/wishlist/{$product->id}")->assertRedirect();
        $this->get('/account/wishlist')->assertInertia(fn (Assert $page) => $page
            ->component('account/wishlist')->has('products', 1)->where('wishlist_ids', [$product->id]));

        $this->post("/wishlist/{$product->id}");
        $this->get('/shop')->assertInertia(fn (Assert $page) => $page->where('wishlist_ids', []));
    }
}
