<?php

namespace Tests\Feature\Api;

use App\Models\CustomOrder;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class M4ReviewsAndCustomOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ProductSeeder::class);
    }

    public function test_can_update_customer_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'phone' => '09170000000',
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)
            ->putJson('/api/customer/profile', [
                'name' => 'Updated Name',
                'phone' => '09179998888',
                'address' => '456 Katipunan Ave',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_can_change_customer_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPassword@123'),
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/customer/password', [
                'current_password' => 'OldPassword@123',
                'new_password' => 'NewPassword@456',
                'new_password_confirmation' => 'NewPassword@456',
            ]);

        $response->assertStatus(200);
        $this->assertTrue(\Hash::check('NewPassword@456', $user->fresh()->password));
    }

    public function test_can_submit_review(): void
    {
        $product = Product::first();

        $response = $this->postJson("/api/products/{$product->id}/reviews", [
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Delicious Banana Bread!',
            'comment' => 'Super moist and rich in banana flavor. Perfect with coffee!',
            'reviewer_name' => 'Maria Clara',
            'reviewer_email' => 'maria@abcdips.test',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('reviews', ['reviewer_name' => 'Maria Clara']);
    }

    public function test_can_list_approved_reviews(): void
    {
        $product = Product::first();

        Review::create([
            'product_id' => $product->id,
            'reviewer_name' => 'John Doe',
            'reviewer_email' => 'john@abcdips.test',
            'rating' => 5,
            'comment' => 'Best cookies in Manila!',
            'is_approved' => true,
        ]);

        $response = $this->getJson("/api/products/{$product->id}/reviews");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'reviewer_name', 'rating', 'comment', 'is_verified_buyer']
                ]
            ]);
    }

    public function test_can_vote_helpful_on_review(): void
    {
        $product = Product::first();

        $review = Review::create([
            'product_id' => $product->id,
            'reviewer_name' => 'John Doe',
            'reviewer_email' => 'john@abcdips.test',
            'rating' => 5,
            'comment' => 'Best cookies in Cavite!',
            'is_approved' => true,
        ]);

        $response = $this->postJson("/api/reviews/{$review->id}/vote");

        $response->assertStatus(200);
        $this->assertEquals(1, $review->fresh()->helpful_votes);
    }

    public function test_can_submit_custom_bakery_inquiry(): void
    {
        $response = $this->postJson('/api/custom-orders', [
            'customer_name' => 'Ana Kalaw',
            'customer_email' => 'ana@abcdips.test',
            'customer_phone' => '09181234567',
            'event_date' => now()->addDays(14)->toDateString(),
            'servings_count' => 40,
            'tiers_count' => 2,
            'flavor_preference' => 'Signature Ube Halaya',
            'frosting_type' => 'Silky Cream Cheese',
            'theme_description' => 'Pastel floral theme with gold leaf accents for a 30th birthday celebration.',
            'budget_range_min' => 2500,
            'budget_range_max' => 5000,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'reference_number', 'customer_name', 'status']
            ]);

        $this->assertDatabaseHas('custom_orders', ['customer_name' => 'Ana Kalaw']);
    }
}
