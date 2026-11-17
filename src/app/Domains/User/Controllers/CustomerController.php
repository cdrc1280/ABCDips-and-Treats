<?php

namespace App\Domains\User\Controllers;

use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\User\Actions\UpdateCustomerAction;
use App\Domains\User\Data\CustomerData;
use App\Domains\User\Enums\CustomerPermission;
use App\Domains\User\Models\User;
use App\Domains\User\Queries\CustomerQueries;
use App\Domains\User\Resources\CustomerResource;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.CustomerPermission::CustomersView->value, only: ['index']),
            new Middleware('can:'.CustomerPermission::CustomersEdit->value, only: ['edit', 'update']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, CustomerQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'customers', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (User $customer) => CustomerResource::fromModel($customer));

        return Inertia::render('customer/index', [
            'records' => CustomerResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(CustomerData::class)]
    public function edit(User $customer, CustomerQueries $queries): Response
    {
        return Inertia::render('customer/edit', [
            'record' => CustomerResource::fromModel($customer->loadCount('orders')),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(User $customer, CustomerData $data, UpdateCustomerAction $update): RedirectResponse
    {
        $update->execute($customer, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $customer->name])]);

        return to_route('admin.customers.index');
    }
}
