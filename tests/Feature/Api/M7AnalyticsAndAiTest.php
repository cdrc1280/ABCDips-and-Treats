<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class M7AnalyticsAndAiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $this->seed(\Database\Seeders\InventorySeeder::class);
    }

    public function test_can_fetch_admin_analytics(): void
    {
        $response = $this->getJson('/api/admin/analytics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'executive_summary' => ['total_revenue', 'total_orders', 'completed_orders', 'low_stock_alerts'],
                'revenue_chart'     => ['labels', 'series'],
                'top_products'      => []
            ]);
    }

    public function test_can_query_ai_bakery_advisor(): void
    {
        $response = $this->postJson('/api/admin/ai/query', [
            'prompt' => 'Which raw ingredients are low in stock?',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['prompt', 'response', 'source']
            ]);

        $this->assertNotEmpty($response->json('data.response'));
    }
}
