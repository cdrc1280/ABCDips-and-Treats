<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'content.contact_messages';

    public function up(): void
    {
        MenuSeeder::add('content', ['title' => 'Engagement', 'icon' => 'megaphone', 'sort_order' => 60], 'content');
        MenuSeeder::add('content.contact_messages', [
            'title' => 'Contact messages',
            'icon' => 'mail',
            'sort_order' => 3,
            'parent' => 'content',
            'permission' => 'contact-messages-view',
            'route_name' => 'admin.contact-messages.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
