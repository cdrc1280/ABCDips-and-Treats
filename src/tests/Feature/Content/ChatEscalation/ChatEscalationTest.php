<?php

namespace Tests\Feature\Content\ChatEscalation;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Content\Enums\ChatEscalationPermission;
use App\Domains\Content\Models\ChatEscalation;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class ChatEscalationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(ChatEscalationPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: ChatEscalationPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): ChatEscalation
    {
        static $n = 0;
        $n++;

        return ChatEscalation::query()->create(array_merge(['guest_name' => "Guest {$n}", 'guest_email' => "g{$n}@example.test", 'conversation' => [['role' => 'user', 'content' => 'Can I talk to a person?']], 'status' => 'open'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/chat-escalations')->assertRedirect('/login');
        $this->put('/admin/chat-escalations/1', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/chat-escalations')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(ChatEscalationPermission::ChatEscalationsView));

        $this->get('/admin/chat-escalations')->assertOk();
        $this->get("/admin/chat-escalations/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/chat-escalations/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/chat-escalations/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/chat-escalations')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('chat-escalation/index')
                ->has('records.data', 2)
                ->where('table.name', 'chat_escalations')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/chat-escalations/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('chat-escalation/edit')->where('record.id', $record->id));

        $this->put("/admin/chat-escalations/{$record->id}", ['guest_name' => 'Guest', 'guest_email' => 'g@example.test', 'status' => 'in_progress', 'admin_notes' => 'Called back'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/chat-escalations')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('chat_escalations', array_merge(['id' => $record->id], ['status' => 'in_progress', 'admin_notes' => 'Called back']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/chat-escalations/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_reply(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/chat-escalations')->patch("/admin/chat-escalations/{$record->id}/reply", ['message' => 'Hi! Your cake is on the way.'])->assertSessionHasNoErrors()->assertRedirect('/admin/chat-escalations');

        $record->refresh();
        $this->assertSame('in_progress', $record->status);
        $conversation = $record->conversation;
        $this->assertSame('admin', end($conversation)['role']);
    }

    public function test_resolve(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/chat-escalations')->patch("/admin/chat-escalations/{$record->id}/resolve", [])->assertSessionHasNoErrors()->assertRedirect('/admin/chat-escalations');

        $this->assertSame('resolved', $record->refresh()->status);
    }

    public function test_reopen(): void
    {
        $record = $this->record(['status' => 'resolved']);

        $this->actingAs($this->admin())->from('/admin/chat-escalations')->patch("/admin/chat-escalations/{$record->id}/reopen", [])->assertSessionHasNoErrors()->assertRedirect('/admin/chat-escalations');

        $this->assertSame('open', $record->refresh()->status);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['guest_name' => 'Lorna Diaz']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/chat-escalations?search[keyword]=lorna')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
