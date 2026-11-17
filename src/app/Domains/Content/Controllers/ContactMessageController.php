<?php

namespace App\Domains\Content\Controllers;

use App\Domains\Content\Actions\DeleteContactMessageAction;
use App\Domains\Content\Actions\MarkReadContactMessageAction;
use App\Domains\Content\Actions\MarkRepliedContactMessageAction;
use App\Domains\Content\Actions\UpdateContactMessageAction;
use App\Domains\Content\Data\ContactMessageData;
use App\Domains\Content\Enums\ContactMessagePermission;
use App\Domains\Content\Models\ContactMessage;
use App\Domains\Content\Queries\ContactMessageQueries;
use App\Domains\Content\Queries\ContactMessageSummaryQuery;
use App\Domains\Content\Resources\ContactMessageResource;
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

class ContactMessageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.ContactMessagePermission::ContactMessagesView->value, only: ['index']),
            new Middleware('can:'.ContactMessagePermission::ContactMessagesEdit->value, only: ['edit', 'update', 'markRead', 'markReplied']),
            new Middleware('can:'.ContactMessagePermission::ContactMessagesDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, ContactMessageQueries $queries, ContactMessageSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'contact_messages', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (ContactMessage $contactMessage) => ContactMessageResource::fromModel($contactMessage));

        return Inertia::render('contact-message/index', [
            'records' => ContactMessageResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(ContactMessageData::class)]
    public function edit(ContactMessage $contactMessage, ContactMessageQueries $queries): Response
    {
        return Inertia::render('contact-message/edit', [
            'record' => ContactMessageResource::fromModel($contactMessage),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(ContactMessage $contactMessage, ContactMessageData $data, UpdateContactMessageAction $update): RedirectResponse
    {
        $update->execute($contactMessage, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $contactMessage->subject])]);

        return to_route('admin.contact-messages.index');
    }

    /** Mark as read. */
    public function markRead(ContactMessage $contactMessage, MarkReadContactMessageAction $action): RedirectResponse
    {
        $action->execute($contactMessage);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Marked as read.')]);

        return back();
    }

    /** Mark as replied. */
    public function markReplied(ContactMessage $contactMessage, MarkRepliedContactMessageAction $action): RedirectResponse
    {
        $action->execute($contactMessage);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Marked as replied.')]);

        return back();
    }

    public function destroy(ContactMessage $contactMessage, DeleteContactMessageAction $delete): RedirectResponse
    {
        $delete->execute($contactMessage);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $contactMessage->subject])]);

        return back();
    }
}
