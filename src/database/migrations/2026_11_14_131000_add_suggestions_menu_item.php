<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'content.suggestions';

    public function up(): void
    {
        MenuSeeder::add('content', ['title' => 'Engagement', 'icon' => 'megaphone', 'sort_order' => 60], 'content');
        MenuSeeder::add('content.suggestions', [
            'title' => 'Suggestions',
            'icon' => 'lightbulb',
            'sort_order' => 2,
            'parent' => 'content',
            'permission' => 'suggestions-view',
            'route_name' => 'admin.suggestions.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
