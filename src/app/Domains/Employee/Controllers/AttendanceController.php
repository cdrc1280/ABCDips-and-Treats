<?php

namespace App\Domains\Employee\Controllers;

use App\Domains\Employee\Actions\CreateAttendanceAction;
use App\Domains\Employee\Actions\DeleteAttendanceAction;
use App\Domains\Employee\Actions\UpdateAttendanceAction;
use App\Domains\Employee\Data\AttendanceData;
use App\Domains\Employee\Enums\AttendancePermission;
use App\Domains\Employee\Models\Attendance;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Queries\AttendanceQueries;
use App\Domains\Employee\Queries\AttendanceSummaryQuery;
use App\Domains\Employee\Resources\AttendanceResource;
use App\Domains\Preference\Actions\ResolveTableStateAction;
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

class AttendanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.AttendancePermission::AttendancesView->value, only: ['index']),
            new Middleware('can:'.AttendancePermission::AttendancesCreate->value, only: ['create', 'store']),
            new Middleware('can:'.AttendancePermission::AttendancesEdit->value, only: ['edit', 'update']),
            new Middleware('can:'.AttendancePermission::AttendancesDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, AttendanceQueries $queries, AttendanceSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'attendances', ['sort_field' => 'date', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Attendance $attendance) => AttendanceResource::fromModel($attendance));

        return Inertia::render('attendance/index', [
            'records' => AttendanceResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
            'employee_options' => fn () => OptionsQuery::for(Employee::class, 'first_name', fn ($q) => $q->where('is_active', true), 'employee_number'),
        ]);
    }

    #[FormRules(AttendanceData::class)]
    public function create(): Response
    {
        return Inertia::render('attendance/create', [
            'employee_options' => fn () => OptionsQuery::for(Employee::class, 'first_name', fn ($q) => $q->where('is_active', true), 'employee_number'),
        ]);
    }

    public function store(AttendanceData $data, CreateAttendanceAction $create): RedirectResponse
    {
        $attendance = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $attendance->date])]);

        return to_route('admin.attendances.index');
    }

    #[FormRules(AttendanceData::class)]
    public function edit(Attendance $attendance, AttendanceQueries $queries): Response
    {
        return Inertia::render('attendance/edit', [
            'record' => AttendanceResource::fromModel($attendance),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'employee_options' => fn () => OptionsQuery::for(Employee::class, 'first_name', fn ($q) => $q->where('is_active', true), 'employee_number'),
        ]);
    }

    public function update(Attendance $attendance, AttendanceData $data, UpdateAttendanceAction $update): RedirectResponse
    {
        $update->execute($attendance, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $attendance->date])]);

        return to_route('admin.attendances.index');
    }

    public function destroy(Attendance $attendance, DeleteAttendanceAction $delete): RedirectResponse
    {
        $delete->execute($attendance);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $attendance->date])]);

        return back();
    }
}
