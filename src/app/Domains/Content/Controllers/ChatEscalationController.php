<?php

namespace App\Domains\Content\Controllers;

use App\Domains\Content\Actions\DeleteChatEscalationAction;
use App\Domains\Content\Actions\ReopenChatEscalationAction;
use App\Domains\Content\Actions\ReplyChatEscalationAction;
use App\Domains\Content\Actions\ResolveChatEscalationAction;
use App\Domains\Content\Actions\UpdateChatEscalationAction;
use App\Domains\Content\Data\ChatEscalationData;
use App\Domains\Content\Enums\ChatEscalationPermission;
use App\Domains\Content\Models\ChatEscalation;
use App\Domains\Content\Queries\ChatEscalationQueries;
use App\Domains\Content\Queries\ChatEscalationSummaryQuery;
use App\Domains\Content\Resources\ChatEscalationResource;
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

class ChatEscalationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.ChatEscalationPermission::ChatEscalationsView->value, only: ['index']),
            new Middleware('can:'.ChatEscalationPermission::ChatEscalationsEdit->value, only: ['edit', 'update', 'reply', 'resolve', 'reopen']),
            new Middleware('can:'.ChatEscalationPermission::ChatEscalationsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, ChatEscalationQueries $queries, ChatEscalationSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'chat_escalations', ['sort_field' => 'updated_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (ChatEscalation $chatEscalation) => ChatEscalationResource::fromModel($chatEscalation));

        return Inertia::render('chat-escalation/index', [
            'records' => ChatEscalationResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(ChatEscalationData::class)]
    public function edit(ChatEscalation $chatEscalation, ChatEscalationQueries $queries): Response
    {
        return Inertia::render('chat-escalation/edit', [
            'record' => ChatEscalationResource::fromModel($chatEscalation),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(ChatEscalation $chatEscalation, ChatEscalationData $data, UpdateChatEscalationAction $update): RedirectResponse
    {
        $update->execute($chatEscalation, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $chatEscalation->guest_name])]);

        return to_route('admin.chat-escalations.index');
    }

    /** Reply. */
    public function reply(Request $request, ChatEscalation $chatEscalation, ReplyChatEscalationAction $action): RedirectResponse
    {
        $value = $request->validate(['message' => ['required', 'string', 'max:2000']])['message'];
        $action->execute($chatEscalation, $value);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Reply sent.')]);

        return back();
    }

    /** Mark resolved. */
    public function resolve(ChatEscalation $chatEscalation, ResolveChatEscalationAction $action): RedirectResponse
    {
        $action->execute($chatEscalation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Conversation resolved.')]);

        return back();
    }

    /** Reopen. */
    public function reopen(ChatEscalation $chatEscalation, ReopenChatEscalationAction $action): RedirectResponse
    {
        $action->execute($chatEscalation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Conversation reopened.')]);

        return back();
    }

    public function destroy(ChatEscalation $chatEscalation, DeleteChatEscalationAction $delete): RedirectResponse
    {
        $delete->execute($chatEscalation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $chatEscalation->guest_name])]);

        return back();
    }
}
