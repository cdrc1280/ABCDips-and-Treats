<?php

namespace App\Domains\Employee\Controllers;

use App\Domains\Employee\Actions\ApprovePayrollAction;
use App\Domains\Employee\Actions\CreatePayrollAction;
use App\Domains\Employee\Actions\DeletePayrollAction;
use App\Domains\Employee\Actions\MarkPaidPayrollAction;
use App\Domains\Employee\Actions\UpdatePayrollAction;
use App\Domains\Employee\Data\PayrollData;
use App\Domains\Employee\Enums\PayrollPermission;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Models\Payroll;
use App\Domains\Employee\Queries\PayrollQueries;
use App\Domains\Employee\Queries\PayrollSummaryQuery;
use App\Domains\Employee\Resources\PayrollResource;
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

class PayrollController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.PayrollPermission::PayrollsView->value, only: ['index']),
            new Middleware('can:'.PayrollPermission::PayrollsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.PayrollPermission::PayrollsEdit->value, only: ['edit', 'update', 'approve', 'markPaid']),
            new Middleware('can:'.PayrollPermission::PayrollsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, PayrollQueries $queries, PayrollSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'payrolls', ['sort_field' => 'period_start', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Payroll $payroll) => PayrollResource::fromModel($payroll));

        return Inertia::render('payroll/index', [
            'records' => PayrollResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(PayrollData::class)]
    public function create(): Response
    {
        return Inertia::render('payroll/create', [
            'employee_options' => fn () => OptionsQuery::for(Employee::class, 'first_name', fn ($q) => $q->where('is_active', true), 'employee_number'),
        ]);
    }

    public function store(PayrollData $data, CreatePayrollAction $create): RedirectResponse
    {
        $payroll = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $payroll->payroll_number])]);

        return to_route('admin.payrolls.index');
    }

    #[FormRules(PayrollData::class)]
    public function edit(Payroll $payroll, PayrollQueries $queries): Response
    {
        return Inertia::render('payroll/edit', [
            'record' => PayrollResource::fromModel($payroll->load('items.employee')),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'employee_options' => fn () => OptionsQuery::for(Employee::class, 'first_name', fn ($q) => $q->where('is_active', true), 'employee_number'),
        ]);
    }

    public function update(Payroll $payroll, PayrollData $data, UpdatePayrollAction $update): RedirectResponse
    {
        $update->execute($payroll, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $payroll->payroll_number])]);

        return to_route('admin.payrolls.index');
    }

    /** Approve. */
    public function approve(Payroll $payroll, ApprovePayrollAction $action): RedirectResponse
    {
        $action->execute($payroll);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':number approved.', ['number' => $payroll->payroll_number])]);

        return back();
    }

    /** Mark paid. */
    public function markPaid(Payroll $payroll, MarkPaidPayrollAction $action): RedirectResponse
    {
        $action->execute($payroll);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':number marked as paid.', ['number' => $payroll->payroll_number])]);

        return back();
    }

    public function destroy(Payroll $payroll, DeletePayrollAction $delete): RedirectResponse
    {
        $delete->execute($payroll);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $payroll->payroll_number])]);

        return back();
    }
}
