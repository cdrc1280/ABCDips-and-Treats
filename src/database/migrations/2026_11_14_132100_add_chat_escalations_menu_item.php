<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'content.chat_escalations';

    public function up(): void
    {
        MenuSeeder::add('content', ['title' => 'Engagement', 'icon' => 'megaphone', 'sort_order' => 60], 'content');
        MenuSeeder::add('content.chat_escalations', [
            'title' => 'Support escalations',
            'icon' => 'messages-square',
            'sort_order' => 4,
            'parent' => 'content',
            'permission' => 'chat-escalations-view',
            'route_name' => 'admin.chat-escalations.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
