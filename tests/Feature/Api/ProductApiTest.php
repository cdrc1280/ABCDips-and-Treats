<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ProductSeeder::class);
    }

    public function test_can_list_categories(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description', 'image_url', 'products_count']
                ]
            ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function test_can_list_products_paginated(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'sku', 'name', 'slug', 'price', 'effective_price',
                        'prep_time_minutes', 'stock_qty', 'is_in_stock',
                        'primary_image_url', 'category', 'tags', 'allergens', 'nutrition'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_can_filter_products_by_category(): void
    {
        $category = ProductCategory::where('slug', 'banana-bread-loaves')->first();

        $response = $this->getJson("/api/products?category={$category->slug}");

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals($category->name, $response->json('data.0.category.name'));
    }

    public function test_can_search_products(): void
    {
        $response = $this->getJson('/api/products?search=Banana');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
        $this->assertStringContainsString('Banana', $response->json('data.0.name'));
    }

    public function test_can_fetch_product_by_slug(): void
    {
        $product = Product::first();

        $response = $this->getJson("/api/products/{$product->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $product->name);
    }

    public function test_returns_404_for_invalid_product_slug(): void
    {
        $response = $this->getJson('/api/products/non-existent-pastry-slug');

        $response->assertStatus(404);
    }

    public function test_can_fetch_featured_products(): void
    {
        $response = $this->getJson('/api/products/featured');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'is_featured']
                ]
            ]);
    }

    public function test_can_fetch_best_sellers(): void
    {
        $response = $this->getJson('/api/products/best-sellers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'is_best_seller']
                ]
            ]);
    }
}
