<?php

namespace App\Domains\Review\Controllers;

use App\Domains\Catalog\Models\Product;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Review\Actions\CreateReviewAction;
use App\Domains\Review\Actions\DeleteReviewAction;
use App\Domains\Review\Actions\FeatureReviewAction;
use App\Domains\Review\Actions\UpdateReviewAction;
use App\Domains\Review\Data\ReviewData;
use App\Domains\Review\Enums\ReviewPermission;
use App\Domains\Review\Models\Review;
use App\Domains\Review\Queries\ReviewQueries;
use App\Domains\Review\Queries\ReviewSummaryQuery;
use App\Domains\Review\Resources\ReviewResource;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class ReviewController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.ReviewPermission::ReviewsView->value, only: ['index']),
            new Middleware('can:'.ReviewPermission::ReviewsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.ReviewPermission::ReviewsEdit->value, only: ['edit', 'update', 'toggle', 'feature']),
            new Middleware('can:'.ReviewPermission::ReviewsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, ReviewQueries $queries, ReviewSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'reviews', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Review $review) => ReviewResource::fromModel($review));

        return Inertia::render('review/index', [
            'records' => ReviewResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
        ]);
    }

    #[FormRules(ReviewData::class)]
    public function create(): Response
    {
        return Inertia::render('review/create', [
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
        ]);
    }

    public function store(ReviewData $data, CreateReviewAction $create): RedirectResponse
    {
        $review = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $review->reviewer_name])]);

        return to_route('admin.reviews.index');
    }

    #[FormRules(ReviewData::class)]
    public function edit(Review $review, ReviewQueries $queries): Response
    {
        return Inertia::render('review/edit', [
            'record' => ReviewResource::fromModel($review),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
        ]);
    }

    public function update(Review $review, ReviewData $data, UpdateReviewAction $update): RedirectResponse
    {
        $update->execute($review, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $review->reviewer_name])]);

        return to_route('admin.reviews.index');
    }

    /** Inline switch on the index table. */
    public function toggle(Request $request, Review $review): RedirectResponse
    {
        $review->update(['is_approved' => $request->boolean('is_approved')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $review->is_approved
            ? __(':name is now active.', ['name' => $review->reviewer_name])
            : __(':name is now inactive.', ['name' => $review->reviewer_name])]);

        return back();
    }

    /** Toggle featured. */
    public function feature(Review $review, FeatureReviewAction $action): RedirectResponse
    {
        $action->execute($review);

        Inertia::flash('toast', ['type' => 'success', 'message' => $review->is_featured ? __('Review featured on the homepage.') : __('Review removed from the homepage.')]);

        return back();
    }

    public function destroy(Review $review, DeleteReviewAction $delete): RedirectResponse
    {
        $delete->execute($review);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $review->reviewer_name])]);

        return back();
    }
}
