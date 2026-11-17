<?php

namespace App\Domains\Inventory\Controllers;

use App\Domains\Inventory\Actions\CreateSupplierAction;
use App\Domains\Inventory\Actions\DeleteSupplierAction;
use App\Domains\Inventory\Actions\UpdateSupplierAction;
use App\Domains\Inventory\Data\SupplierData;
use App\Domains\Inventory\Enums\SupplierPermission;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Inventory\Queries\SupplierQueries;
use App\Domains\Inventory\Resources\SupplierResource;
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

class SupplierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.SupplierPermission::SuppliersView->value, only: ['index']),
            new Middleware('can:'.SupplierPermission::SuppliersCreate->value, only: ['create', 'store']),
            new Middleware('can:'.SupplierPermission::SuppliersEdit->value, only: ['edit', 'update']),
            new Middleware('can:'.SupplierPermission::SuppliersDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, SupplierQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'suppliers', ['sort_field' => 'name', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Supplier $supplier) => SupplierResource::fromModel($supplier));

        return Inertia::render('supplier/index', [
            'records' => SupplierResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(SupplierData::class)]
    public function create(): Response
    {
        return Inertia::render('supplier/create', [
        ]);
    }

    public function store(SupplierData $data, CreateSupplierAction $create): RedirectResponse
    {
        $supplier = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $supplier->name])]);

        return to_route('admin.suppliers.index');
    }

    #[FormRules(SupplierData::class)]
    public function edit(Supplier $supplier, SupplierQueries $queries): Response
    {
        return Inertia::render('supplier/edit', [
            'record' => SupplierResource::fromModel($supplier->loadCount(['ingredients', 'purchaseOrders'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(Supplier $supplier, SupplierData $data, UpdateSupplierAction $update): RedirectResponse
    {
        $update->execute($supplier, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $supplier->name])]);

        return to_route('admin.suppliers.index');
    }

    public function destroy(Supplier $supplier, DeleteSupplierAction $delete): RedirectResponse
    {
        $delete->execute($supplier);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $supplier->name])]);

        return back();
    }
}
