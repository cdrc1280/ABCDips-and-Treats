<?php

namespace Tests\Feature\Filament;

use App\Models\ChatEscalation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatEscalationResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create([
            'email' => 'admin@abcdips.test',
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_access_chat_escalations_list_page(): void
    {
        ChatEscalation::create([
            'guest_name' => 'Juan Dela Cruz',
            'guest_email' => 'juan@example.com',
            'conversation' => [
                ['role' => 'user', 'content' => 'Do you deliver in Bacoor?'],
                ['role' => 'assistant', 'content' => 'Yes, we offer same-day delivery!'],
            ],
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/chat-escalations');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_chat_escalation_edit_page(): void
    {
        $escalation = ChatEscalation::create([
            'guest_name' => 'Maria Clara',
            'guest_email' => 'maria@example.com',
            'conversation' => [
                ['role' => 'user', 'content' => 'How much is custom cake?'],
            ],
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/chat-escalations/{$escalation->id}/edit");

        $response->assertStatus(200);
    }
}
