<?php

namespace Tests\Feature\Content\BlogPost;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Content\Enums\BlogPostPermission;
use App\Domains\Content\Models\BlogPost;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class BlogPostTest extends TestCase
{
    use RefreshDatabase;

    private function admin(BlogPostPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: BlogPostPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): BlogPost
    {
        static $n = 0;
        $n++;

        return BlogPost::query()->create(array_merge(['title' => "Post {$n}", 'slug' => "post-{$n}", 'body' => 'Hello', 'status' => 'draft', 'category' => 'General'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/blog-posts')->assertRedirect('/login');
        $this->post('/admin/blog-posts', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/blog-posts')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(BlogPostPermission::BlogPostsView));

        $this->get('/admin/blog-posts')->assertOk();
        $this->get('/admin/blog-posts/create')->assertForbidden();
        $this->get("/admin/blog-posts/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/blog-posts/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/blog-posts/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/blog-posts')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('blog-post/index')
                ->has('records.data', 2)
                ->where('table.name', 'blog_posts')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/blog-posts', ['title' => 'Why we use real butter', 'slug' => '', 'category' => 'Baking', 'body' => 'Long story', 'status' => 'draft'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/blog-posts')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('blog_posts', ['slug' => 'why-we-use-real-butter', 'category' => 'Baking']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/blog-posts', [])
            ->assertSessionHasErrors(['title', 'slug', 'body']);
    }

    public function test_unique_slug_ignores_soft_deleted_rows(): void
    {

        $existing = $this->record();
        $payload = array_merge(['title' => 'Why we use real butter', 'slug' => '', 'category' => 'Baking', 'body' => 'Long story', 'status' => 'draft'], ['slug' => $existing->slug]);

        $this->actingAs($this->admin())->post('/admin/blog-posts', $payload)->assertSessionHasErrors(['slug']);

        $existing->delete();
        $this->post('/admin/blog-posts', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/blog-posts/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('blog-post/edit')->where('record.id', $record->id));

        $this->put("/admin/blog-posts/{$record->id}", ['title' => 'Real butter, always', 'slug' => 'real-butter', 'category' => 'Baking', 'body' => 'Edited', 'status' => 'archived'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/blog-posts')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('blog_posts', array_merge(['id' => $record->id], ['slug' => 'real-butter', 'status' => 'archived']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/blog-posts/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_publish(): void
    {
        $record = $this->record(['status' => 'draft']);

        $this->actingAs($this->admin())->from('/admin/blog-posts')->patch("/admin/blog-posts/{$record->id}/publish", [])->assertSessionHasNoErrors()->assertRedirect('/admin/blog-posts');

        $this->assertSame('published', $record->refresh()->status);
        $this->assertNotNull($record->published_at);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['title' => 'Ube season is here']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/blog-posts?search[keyword]=ube')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
