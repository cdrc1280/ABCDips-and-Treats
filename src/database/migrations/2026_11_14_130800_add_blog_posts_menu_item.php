<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'content.blog_posts';

    public function up(): void
    {
        MenuSeeder::add('content', ['title' => 'Engagement', 'icon' => 'megaphone', 'sort_order' => 60], 'content');
        MenuSeeder::add('content.blog_posts', [
            'title' => 'Blog posts',
            'icon' => 'newspaper',
            'sort_order' => 1,
            'parent' => 'content',
            'permission' => 'blog-posts-view',
            'route_name' => 'admin.blog-posts.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
