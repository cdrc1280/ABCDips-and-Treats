<?php

namespace App\Domains\Content\Controllers;

use App\Domains\Content\Actions\DeleteSuggestionAction;
use App\Domains\Content\Actions\MarkImplementedSuggestionAction;
use App\Domains\Content\Actions\MarkReviewingSuggestionAction;
use App\Domains\Content\Actions\UpdateSuggestionAction;
use App\Domains\Content\Data\SuggestionData;
use App\Domains\Content\Enums\SuggestionPermission;
use App\Domains\Content\Models\Suggestion;
use App\Domains\Content\Queries\SuggestionQueries;
use App\Domains\Content\Queries\SuggestionSummaryQuery;
use App\Domains\Content\Resources\SuggestionResource;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class SuggestionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.SuggestionPermission::SuggestionsView->value, only: ['index']),
            new Middleware('can:'.SuggestionPermission::SuggestionsEdit->value, only: ['edit', 'update', 'markReviewing', 'markImplemented']),
            new Middleware('can:'.SuggestionPermission::SuggestionsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, SuggestionQueries $queries, SuggestionSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'suggestions', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Suggestion $suggestion) => SuggestionResource::fromModel($suggestion));

        return Inertia::render('suggestion/index', [
            'records' => SuggestionResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(SuggestionData::class)]
    public function edit(Suggestion $suggestion, SuggestionQueries $queries): Response
    {
        return Inertia::render('suggestion/edit', [
            'record' => SuggestionResource::fromModel($suggestion),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(Suggestion $suggestion, SuggestionData $data, UpdateSuggestionAction $update): RedirectResponse
    {
        $update->execute($suggestion, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $suggestion->subject])]);

        return to_route('admin.suggestions.index');
    }

    /** Start reviewing. */
    public function markReviewing(Suggestion $suggestion, MarkReviewingSuggestionAction $action): RedirectResponse
    {
        $action->execute($suggestion);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Moved to reviewing.')]);

        return back();
    }

    /** Mark implemented. */
    public function markImplemented(Suggestion $suggestion, MarkImplementedSuggestionAction $action): RedirectResponse
    {
        $action->execute($suggestion);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Marked as implemented. Nice!')]);

        return back();
    }

    public function destroy(Suggestion $suggestion, DeleteSuggestionAction $delete): RedirectResponse
    {
        $delete->execute($suggestion);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $suggestion->subject])]);

        return back();
    }
}
