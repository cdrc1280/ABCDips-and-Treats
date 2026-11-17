<?php

namespace Tests\Feature\Flows;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Review\Enums\ReviewPermission;
use App\Domains\Review\Models\Review;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

/** Customer review → hidden until staff approve it in the back office → shown on the product page. */
class ReviewModerationFlowTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_a_review_is_hidden_until_staff_approve_it(): void
    {
        $product = $this->product();
        $customer = User::factory()->customer()->create(['email_verified_at' => now()]);

        $this->actingAs($customer)->post('/reviews', ['product_id' => $product->id, 'rating' => 5, 'comment' => 'Moist, rich and not too sweet.'])
            ->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $review = Review::query()->sole();
        $this->assertFalse((bool) $review->is_approved);
        $this->get("/products/{$product->slug}")->assertInertia(fn (Assert $page) => $page
            ->loadDeferredProps('reviews', fn (Assert $reload) => $reload->has('reviews', 0)));

        $staff = $this->userWith(AccessPermission::DashboardView, ...ReviewPermission::cases());
        $this->actingAs($staff)->from('/admin/reviews')->patch("/admin/reviews/{$review->id}/toggle", ['is_approved' => true])->assertRedirect('/admin/reviews');

        $this->get("/products/{$product->slug}")->assertInertia(fn (Assert $page) => $page
            ->where('product.reviews_count', 1)
            ->loadDeferredProps('reviews', fn (Assert $reload) => $reload->has('reviews', 1)->where('reviews.0.comment', 'Moist, rich and not too sweet.')));
    }
}
