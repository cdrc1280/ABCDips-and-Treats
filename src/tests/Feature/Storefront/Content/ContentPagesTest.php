<?php

namespace Tests\Feature\Storefront\Content;

use App\Domains\Content\Models\BlogPost;
use App\Domains\Content\Models\ContactMessage;
use App\Domains\Content\Models\Faq;
use App\Domains\Content\Models\Suggestion;
use App\Domains\Sales\Models\CustomOrder;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContentPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_static_pages_render(): void
    {
        foreach (['/about' => 'storefront/about', '/contact' => 'storefront/contact', '/suggestions' => 'storefront/suggestions', '/privacy' => 'storefront/privacy', '/terms' => 'storefront/terms', '/custom-orders' => 'storefront/custom-orders', '/track' => 'storefront/track'] as $url => $component) {
            $this->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page->component($component));
        }
    }

    public function test_faq_lists_only_active_entries(): void
    {
        Faq::create(['question' => 'Do you deliver?', 'answer' => 'Yes', 'is_active' => true, 'sort_order' => 1]);
        Faq::create(['question' => 'Hidden?', 'answer' => 'No', 'is_active' => false]);

        $this->get('/faq')->assertInertia(fn (Assert $page) => $page->has('faqs', 1)->where('faqs.0.question', 'Do you deliver?'));
    }

    public function test_blog_hides_drafts_and_future_posts_and_sanitises_html(): void
    {
        BlogPost::create(['title' => 'Live', 'slug' => 'live', 'status' => 'published', 'published_at' => now()->subDay(), 'body' => '<p>Hi</p><script>alert(1)</script><img src="x" onerror="alert(2)">']);
        BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'status' => 'draft', 'body' => 'x']);
        BlogPost::create(['title' => 'Later', 'slug' => 'later', 'status' => 'published', 'published_at' => now()->addWeek(), 'body' => 'x']);

        $this->get('/blog')->assertInertia(fn (Assert $page) => $page->component('storefront/blog')->has('posts.data', 1));
        $this->get('/blog/draft')->assertNotFound();
        $this->get('/blog/later')->assertNotFound();

        $this->get('/blog/live')->assertInertia(fn (Assert $page) => $page
            ->component('storefront/blog-post')
            ->where('post.body', fn (string $body) => str_contains($body, '<p>Hi</p>') && ! str_contains($body, 'script') && ! str_contains($body, 'onerror')));
    }

    public function test_contact_and_suggestion_forms_store_messages(): void
    {
        $this->post('/contact', ['name' => 'Ana', 'email' => 'ana@example.com', 'subject' => 'Hello', 'message' => 'I would like to order 50 boxes.'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('unread', ContactMessage::query()->sole()->status);

        $this->post('/suggestions', ['name' => 'Ana', 'email' => 'ana@example.com', 'category' => 'product_idea', 'subject' => 'Ube crinkles', 'message' => 'Please bake ube crinkles!'])->assertSessionHasNoErrors();
        $this->assertSame('product_idea', Suggestion::query()->sole()->category);

        $this->post('/suggestions', ['name' => 'A', 'email' => 'bad', 'category' => 'hack', 'subject' => '', 'message' => 'x'])->assertSessionHasErrors(['email', 'category', 'subject', 'message']);
    }

    public function test_custom_cake_inquiry_requires_login_and_notice(): void
    {
        $payload = ['customer_name' => 'Ana', 'customer_email' => 'ana@example.com', 'customer_phone' => '09171234567', 'event_date' => now()->addDays(5)->toDateString(), 'servings_count' => 40, 'tiers_count' => 2, 'theme_description' => 'Pastel florals', 'cake_inscription' => 'Happy 7th!'];

        $this->post('/custom-orders', $payload)->assertRedirect('/login');

        $this->actingAs(User::factory()->customer()->create());
        $this->post('/custom-orders', [...$payload, 'event_date' => now()->toDateString()])->assertSessionHasErrors('event_date');
        $this->post('/custom-orders', $payload)->assertSessionHasNoErrors();

        $inquiry = CustomOrder::query()->sole();
        $this->assertStringContainsString('Happy 7th!', $inquiry->theme_description);
    }
}
