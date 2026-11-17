<?php

namespace Tests\Feature\Review\Review;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Review\Enums\ReviewPermission;
use App\Domains\Review\Models\Review;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private function admin(ReviewPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: ReviewPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Review
    {
        static $n = 0;
        $n++;

        return Review::query()->create(array_merge(['reviewer_name' => "Reviewer {$n}", 'reviewer_email' => "r{$n}@example.test", 'rating' => 5, 'comment' => 'Lovely', 'is_approved' => true], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/reviews')->assertRedirect('/login');
        $this->post('/admin/reviews', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/reviews')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(ReviewPermission::ReviewsView));

        $this->get('/admin/reviews')->assertOk();
        $this->get('/admin/reviews/create')->assertForbidden();
        $this->get("/admin/reviews/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/reviews/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/reviews/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/reviews')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('review/index')
                ->has('records.data', 2)
                ->where('table.name', 'reviews')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 4)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/reviews', ['reviewer_name' => 'Joy', 'reviewer_email' => 'JOY@example.test', 'rating' => '4', 'title' => 'Moist!', 'comment' => 'Best banana bread in Cavite', 'is_approved' => '1'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/reviews')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('reviews', ['reviewer_name' => 'Joy', 'reviewer_email' => 'joy@example.test', 'rating' => 4]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/reviews', [])
            ->assertSessionHasErrors(['reviewer_name', 'reviewer_email', 'comment']);
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/reviews/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('review/edit')->where('record.id', $record->id));

        $this->put("/admin/reviews/{$record->id}", ['reviewer_name' => 'Joy', 'reviewer_email' => 'joy@example.test', 'rating' => '5', 'comment' => 'Edited', 'is_approved' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/reviews')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('reviews', array_merge(['id' => $record->id], ['rating' => 5, 'is_approved' => false]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/reviews/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_feature(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/reviews')->patch("/admin/reviews/{$record->id}/feature", [])->assertSessionHasNoErrors()->assertRedirect('/admin/reviews');

        $this->assertTrue($record->refresh()->is_featured);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['comment' => 'The ube cheesecake was amazing']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/reviews?search[keyword]=ube')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
