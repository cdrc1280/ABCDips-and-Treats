<?php

namespace App\Domains\Employee\Controllers;

use App\Domains\Employee\Actions\CreateEmployeeAction;
use App\Domains\Employee\Actions\DeleteEmployeeAction;
use App\Domains\Employee\Actions\UpdateEmployeeAction;
use App\Domains\Employee\Data\EmployeeData;
use App\Domains\Employee\Enums\EmployeePermission;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Queries\EmployeeQueries;
use App\Domains\Employee\Resources\EmployeeResource;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\User\Models\User;
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

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.EmployeePermission::EmployeesView->value, only: ['index']),
            new Middleware('can:'.EmployeePermission::EmployeesCreate->value, only: ['create', 'store']),
            new Middleware('can:'.EmployeePermission::EmployeesEdit->value, only: ['edit', 'update', 'toggle']),
            new Middleware('can:'.EmployeePermission::EmployeesDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, EmployeeQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'employees', ['sort_field' => 'first_name', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Employee $employee) => EmployeeResource::fromModel($employee));

        return Inertia::render('employee/index', [
            'records' => EmployeeResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(EmployeeData::class)]
    public function create(): Response
    {
        return Inertia::render('employee/create', [
            'user_options' => fn () => OptionsQuery::for(User::class, 'name', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer')), 'email'),
        ]);
    }

    public function store(EmployeeData $data, CreateEmployeeAction $create): RedirectResponse
    {
        $employee = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $employee->first_name])]);

        return to_route('admin.employees.index');
    }

    #[FormRules(EmployeeData::class)]
    public function edit(Employee $employee, EmployeeQueries $queries): Response
    {
        return Inertia::render('employee/edit', [
            'record' => EmployeeResource::fromModel($employee),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'user_options' => fn () => OptionsQuery::for(User::class, 'name', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer')), 'email'),
        ]);
    }

    public function update(Employee $employee, EmployeeData $data, UpdateEmployeeAction $update): RedirectResponse
    {
        $update->execute($employee, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $employee->first_name])]);

        return to_route('admin.employees.index');
    }

    /** Inline switch on the index table. */
    public function toggle(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update(['is_active' => $request->boolean('is_active')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $employee->is_active
            ? __(':name is now active.', ['name' => $employee->first_name])
            : __(':name is now inactive.', ['name' => $employee->first_name])]);

        return back();
    }

    public function destroy(Employee $employee, DeleteEmployeeAction $delete): RedirectResponse
    {
        $delete->execute($employee);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $employee->first_name])]);

        return back();
    }
}
