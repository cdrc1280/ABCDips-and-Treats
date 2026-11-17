<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Catalog\Models\Product;
use App\Domains\Content\Models\BlogPost;
use App\Domains\Content\Models\ContactMessage;
use App\Domains\Content\Models\Faq;
use App\Domains\Content\Models\Suggestion;
use App\Domains\Review\Models\Review;
use App\Domains\Sales\Models\Order;
use App\Domains\Setting\Models\Setting;
use App\Domains\Storefront\Data\ContactMessageData;
use App\Domains\Storefront\Data\CustomCakeInquiryData;
use App\Domains\Storefront\Data\SuggestionData;
use App\Domains\Storefront\Queries\StorefrontContent;
use App\Domains\Storefront\Support\CustomCakePricing;
use App\Domains\Storefront\Support\SafeHtml;
use App\Domains\User\Models\User;
use App\Http\Controllers\Controller;
use App\Services\CustomOrderService;
use App\Shared\Services\PublicUploads;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

final class ContentController extends Controller
{
    public function about(StorefrontContent $content): Response
    {
        return Inertia::render('storefront/about', [
            'content' => $content->about(),
            'stats' => Inertia::defer(fn () => Cache::remember('storefront.about_stats', 600, fn () => [
                'happy_customers' => max(User::role('customer')->count(), Order::query()->distinct('customer_email')->count('customer_email')),
                'signature_recipes' => Product::query()->forCustomer()->count(),
                'average_rating' => ($avg = Review::query()->where('is_approved', true)->avg('rating')) ? round((float) $avg, 1) : null,
            ])),
        ]);
    }

    public function faq(): Response
    {
        return Inertia::render('storefront/faq', [
            'faqs' => Faq::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')
                ->get(['id', 'question', 'answer', 'category'])
                ->map(fn (Faq $f) => ['id' => $f->id, 'question' => $f->question, 'answer' => $f->answer, 'category' => $f->category ?: 'General'])
                ->all(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('storefront/contact', [
            'store' => [
                'address' => Setting::get('store_address', ''),
                'phone' => Setting::get('store_phone', ''),
                'email' => Setting::get('store_email', ''),
                'hours' => Setting::get('store_hours', 'Mon–Sat, 8:00 AM – 6:00 PM'),
            ],
        ]);
    }

    public function sendContact(ContactMessageData $data): RedirectResponse
    {
        ContactMessage::create([...$data->toArray(), 'status' => 'unread']);
        Inertia::flash('toast', ['type' => 'success', 'message' => __('Thanks! We’ll reply within 24 hours.')]);

        return back();
    }

    public function suggestions(): Response
    {
        return Inertia::render('storefront/suggestions');
    }

    public function sendSuggestion(Request $request, SuggestionData $data): RedirectResponse
    {
        Suggestion::create([...$data->toArray(), 'user_id' => $request->user()?->id]);
        Inertia::flash('toast', ['type' => 'success', 'message' => __('Thank you — your idea reached our team!')]);

        return back();
    }

    public function blog(Request $request): Response
    {
        $category = $request->string('category')->trim()->limit(60, '')->toString() ?: null;

        return Inertia::render('storefront/blog', [
            'category' => $category,
            'categories' => fn () => $this->published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all(),
            'posts' => Inertia::scroll(fn () => $this->published()
                ->when($category, fn (Builder $q) => $q->where('category', $category))
                ->with('author:id,name')
                ->orderByDesc('published_at')
                ->paginate(9)
                ->withQueryString()
                ->through(fn (BlogPost $post) => $this->postCard($post))),
        ]);
    }

    public function post(string $slug): Response
    {
        $post = $this->published()->with('author:id,name')->where('slug', $slug)->firstOrFail();

        return Inertia::render('storefront/blog-post', [
            'post' => [
                ...$this->postCard($post),
                'body' => SafeHtml::clean($post->body),
                'tags' => $post->tags ?? [],
                'seo_title' => $post->seo_title ?: $post->title,
                'seo_description' => $post->seo_description ?: $post->excerpt,
            ],
            'more' => fn () => $this->published()->whereKeyNot($post->id)->orderByDesc('published_at')->limit(3)->get()->map(fn (BlogPost $p) => $this->postCard($p))->all(),
        ]);
    }

    public function customOrders(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('storefront/custom-orders', [
            'prefill' => ['customer_name' => $user->name ?? '', 'customer_email' => $user->email ?? '', 'customer_phone' => $user->phone ?? ''],
            'pricing' => CustomCakePricing::table(),
            // Custom cakes ride on a cake product in the basket (priced by CustomCakePricing, never stock-deducted).
            'cakeProductId' => fn () => Product::query()->forCustomer()
                ->whereHas('category', fn (Builder $q) => $q->whereLike('slug', '%cake%'))
                ->orderBy('id')->value('id'),
        ]);
    }

    public function sendCustomOrder(Request $request, CustomCakeInquiryData $data, CustomOrderService $service): RedirectResponse
    {
        $inquiry = $service->createInquiry([
            'customer_name' => $data->customerName,
            'customer_email' => $data->customerEmail,
            'customer_phone' => $data->customerPhone,
            'event_date' => $data->eventDate,
            'servings_count' => $data->servingsCount,
            'tiers_count' => $data->tiersCount,
            'flavor_preference' => $data->flavorPreference,
            'frosting_type' => $data->frostingType,
            'theme_description' => $data->themeDescription.($data->cakeInscription ? "\n\nInscription: “{$data->cakeInscription}”" : ''),
            'budget_range_min' => $data->budgetRangeMin,
            'budget_range_max' => $data->budgetRangeMax,
            'reference_photos' => $data->referencePhotos,
        ], $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request :ref received! Our pastry chef will send a quote within 24 hours.', ['ref' => $inquiry->reference_number])]);

        return back();
    }

    public function page(string $page): Response
    {
        return Inertia::render("storefront/{$page}", [
            'store' => ['name' => Setting::get('store_name', 'ABCDips & Treats'), 'email' => Setting::get('store_email', ''), 'address' => Setting::get('store_address', '')],
            'updated' => '2026-01-01',
        ]);
    }

    /**
     * @return Builder<BlogPost>
     */
    private function published(): Builder
    {
        return BlogPost::query()->where('status', 'published')->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /**
     * @return array<string, mixed>
     */
    private function postCard(BlogPost $post): array
    {
        $cover = $post->cover_image;

        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'category' => $post->category,
            'cover_url' => PublicUploads::url($cover),
            'author' => $post->author?->name,
            'published_at' => $post->published_at?->toIso8601String(),
            'reading_minutes' => max(1, (int) ceil(str_word_count(strip_tags((string) $post->body)) / 200)),
        ];
    }
}
