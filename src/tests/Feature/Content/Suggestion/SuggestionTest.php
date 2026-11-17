<?php

namespace Tests\Feature\Content\Suggestion;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Content\Enums\SuggestionPermission;
use App\Domains\Content\Models\Suggestion;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class SuggestionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(SuggestionPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: SuggestionPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Suggestion
    {
        static $n = 0;
        $n++;

        return Suggestion::query()->create(array_merge(['name' => "Fan {$n}", 'email' => "f{$n}@example.test", 'category' => 'product_idea', 'subject' => "Idea {$n}", 'message' => 'Mango float please', 'status' => 'new'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/suggestions')->assertRedirect('/login');
        $this->put('/admin/suggestions/1', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/suggestions')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(SuggestionPermission::SuggestionsView));

        $this->get('/admin/suggestions')->assertOk();
        $this->get("/admin/suggestions/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/suggestions/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/suggestions/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/suggestions')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('suggestion/index')
                ->has('records.data', 2)
                ->where('table.name', 'suggestions')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/suggestions/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('suggestion/edit')->where('record.id', $record->id));

        $this->put("/admin/suggestions/{$record->id}", ['name' => 'Fan', 'email' => 'f@example.test', 'category' => 'other', 'subject' => 'Idea', 'message' => 'Mango float', 'status' => 'declined', 'admin_notes' => 'Seasonal only'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/suggestions')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('suggestions', array_merge(['id' => $record->id], ['status' => 'declined', 'category' => 'other']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/suggestions/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_mark_reviewing(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/suggestions')->patch("/admin/suggestions/{$record->id}/mark-reviewing", [])->assertSessionHasNoErrors()->assertRedirect('/admin/suggestions');

        $this->assertSame('reviewing', $record->refresh()->status);
    }

    public function test_mark_implemented(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/suggestions')->patch("/admin/suggestions/{$record->id}/mark-implemented", [])->assertSessionHasNoErrors()->assertRedirect('/admin/suggestions');

        $this->assertSame('implemented', $record->refresh()->status);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['subject' => 'Gluten-free brownies']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/suggestions?search[keyword]=gluten')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
