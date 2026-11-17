<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'ai_advisor';

    public function up(): void
    {
        MenuSeeder::add('ai_advisor', [
            'title' => 'AI advisor',
            'icon' => 'sparkles',
            'sort_order' => 1,
            'permission' => 'ai-advisor-use',
            'route_name' => 'admin.ai-advisor.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
