<?php

namespace App\Domains\Audit\Controllers;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Audit\Queries\ActivityQueries;
use App\Domains\Audit\Resources\ActivityResource;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;
use Spatie\LaravelData\PaginatedDataCollection;

/** Read-only activity log: every create / update / delete recorded by LogsModelActivity. */
class ActivityLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:'.AccessPermission::AuditLogsView->value)];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, ActivityQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'activity_log', ['sort_field' => 'id', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Activity $activity) => ActivityResource::fromModel($activity));

        return Inertia::render('activity-log/index', [
            'records' => ActivityResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'log_options' => fn () => $queries->logNameOptions(),
            'causer_options' => fn () => $queries->causerOptions(),
        ]);
    }
}
