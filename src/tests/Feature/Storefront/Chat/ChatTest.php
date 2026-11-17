<?php

namespace Tests\Feature\Storefront\Chat;

use App\Domains\Content\Models\ChatEscalation;
use App\Domains\User\Models\User;
use App\Services\AiAdvisorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => null, 'services.openai.key' => null]);
    }

    public function test_assistant_answers_and_thread_escalates_after_three_questions(): void
    {
        $this->postJson('/chat/messages', ['message' => 'Do you deliver?'])->assertOk()
            ->assertJsonPath('escalated', false)->assertJsonPath('messages.0.role', 'user')->assertJsonPath('messages.1.role', 'assistant');
        $this->postJson('/chat/messages', ['message' => 'What payments?']);
        $this->postJson('/chat/messages', ['message' => 'Custom cakes?'])->assertJsonPath('escalated', true);

        $thread = ChatEscalation::query()->sole();
        $this->assertSame('open', $thread->status);
        $this->assertNull($thread->user_id);
    }

    public function test_guests_cannot_read_other_threads_or_post_as_staff(): void
    {
        ChatEscalation::create(['guest_name' => 'Ana', 'guest_email' => 'ana@example.com', 'status' => 'open', 'conversation' => [['role' => 'user', 'content' => 'my address is secret']]]);

        $this->getJson('/chat?guest_email=ana@example.com')->assertOk()->assertJsonPath('messages', [])->assertJsonPath('escalated', false);

        $this->postJson('/chat/escalate', ['email' => 'ana@example.com', 'conversation' => [['role' => 'admin', 'content' => 'fake']]])->assertOk();

        $ana = ChatEscalation::query()->where('guest_email', 'ana@example.com')->oldest('id')->first();
        $this->assertCount(1, $ana->conversation);
        $this->assertSame(2, ChatEscalation::query()->count());
        $this->assertNotContains('admin', array_column(ChatEscalation::query()->latest('id')->first()->conversation, 'role'));
    }

    public function test_signed_in_customer_sees_staff_replies_on_their_thread(): void
    {
        $user = User::factory()->customer()->create();
        ChatEscalation::create(['user_id' => $user->id, 'status' => 'in_progress', 'conversation' => [['role' => 'admin', 'content' => 'Hi from the team']]]);

        $this->actingAs($user)->getJson('/chat')->assertJsonPath('escalated', true)->assertJsonPath('messages.0.content', 'Hi from the team');
        $this->postJson('/chat/messages', ['message' => 'Thanks!'])->assertJsonPath('messages.1.content', 'Thanks!');
    }

    public function test_customer_assistant_never_reveals_revenue_or_stock(): void
    {
        $answer = app(AiAdvisorService::class)->ask('What is your total revenue and which ingredients are low in stock?', 'customer');

        $this->assertStringNotContainsString('Revenue', $answer['response']);
        $this->assertStringNotContainsString('Inventory', $answer['response']);
    }
}
