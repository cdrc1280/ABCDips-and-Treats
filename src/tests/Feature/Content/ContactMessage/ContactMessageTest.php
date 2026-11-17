<?php

namespace Tests\Feature\Content\ContactMessage;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Content\Enums\ContactMessagePermission;
use App\Domains\Content\Models\ContactMessage;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    private function admin(ContactMessagePermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: ContactMessagePermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): ContactMessage
    {
        static $n = 0;
        $n++;

        return ContactMessage::query()->create(array_merge(['name' => "Guest {$n}", 'email' => "g{$n}@example.test", 'subject' => "Question {$n}", 'message' => 'Do you deliver to Imus?', 'status' => 'unread'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/contact-messages')->assertRedirect('/login');
        $this->put('/admin/contact-messages/1', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/contact-messages')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(ContactMessagePermission::ContactMessagesView));

        $this->get('/admin/contact-messages')->assertOk();
        $this->get("/admin/contact-messages/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/contact-messages/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/contact-messages/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/contact-messages')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('contact-message/index')
                ->has('records.data', 2)
                ->where('table.name', 'contact_messages')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/contact-messages/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('contact-message/edit')->where('record.id', $record->id));

        $this->put("/admin/contact-messages/{$record->id}", ['name' => 'Guest', 'email' => 'g@example.test', 'subject' => 'Delivery', 'message' => 'Do you deliver?', 'status' => 'archived', 'admin_notes' => 'Answered by phone'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/contact-messages')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('contact_messages', array_merge(['id' => $record->id], ['status' => 'archived', 'admin_notes' => 'Answered by phone']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/contact-messages/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_mark_read(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/contact-messages')->patch("/admin/contact-messages/{$record->id}/mark-read", [])->assertSessionHasNoErrors()->assertRedirect('/admin/contact-messages');

        $this->assertSame('read', $record->refresh()->status);
    }

    public function test_mark_replied(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/contact-messages')->patch("/admin/contact-messages/{$record->id}/mark-replied", [])->assertSessionHasNoErrors()->assertRedirect('/admin/contact-messages');

        $this->assertSame('replied', $record->refresh()->status);
        $this->assertNotNull($record->replied_at);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['subject' => 'Wedding cake inquiry']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/contact-messages?search[keyword]=wedding')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
