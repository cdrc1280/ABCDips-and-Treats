<?php

namespace App\Domains\Inventory\Controllers;

use App\Domains\Inventory\Actions\CreatePackagingMaterialAction;
use App\Domains\Inventory\Actions\DeletePackagingMaterialAction;
use App\Domains\Inventory\Actions\RestockPackagingMaterialAction;
use App\Domains\Inventory\Actions\UpdatePackagingMaterialAction;
use App\Domains\Inventory\Data\PackagingMaterialData;
use App\Domains\Inventory\Enums\PackagingMaterialPermission;
use App\Domains\Inventory\Models\PackagingMaterial;
use App\Domains\Inventory\Queries\PackagingMaterialQueries;
use App\Domains\Inventory\Queries\PackagingMaterialSummaryQuery;
use App\Domains\Inventory\Resources\PackagingMaterialResource;
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

class PackagingMaterialController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.PackagingMaterialPermission::PackagingMaterialsView->value, only: ['index']),
            new Middleware('can:'.PackagingMaterialPermission::PackagingMaterialsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.PackagingMaterialPermission::PackagingMaterialsEdit->value, only: ['edit', 'update', 'toggle', 'restock']),
            new Middleware('can:'.PackagingMaterialPermission::PackagingMaterialsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, PackagingMaterialQueries $queries, PackagingMaterialSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'packaging_materials', ['sort_field' => 'name', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (PackagingMaterial $packagingMaterial) => PackagingMaterialResource::fromModel($packagingMaterial));

        return Inertia::render('packaging-material/index', [
            'records' => PackagingMaterialResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(PackagingMaterialData::class)]
    public function create(): Response
    {
        return Inertia::render('packaging-material/create', [
        ]);
    }

    public function store(PackagingMaterialData $data, CreatePackagingMaterialAction $create): RedirectResponse
    {
        $packagingMaterial = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $packagingMaterial->name])]);

        return to_route('admin.packaging-materials.index');
    }

    #[FormRules(PackagingMaterialData::class)]
    public function edit(PackagingMaterial $packagingMaterial, PackagingMaterialQueries $queries): Response
    {
        return Inertia::render('packaging-material/edit', [
            'record' => PackagingMaterialResource::fromModel($packagingMaterial),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(PackagingMaterial $packagingMaterial, PackagingMaterialData $data, UpdatePackagingMaterialAction $update): RedirectResponse
    {
        $update->execute($packagingMaterial, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $packagingMaterial->name])]);

        return to_route('admin.packaging-materials.index');
    }

    /** Inline switch on the index table. */
    public function toggle(Request $request, PackagingMaterial $packagingMaterial): RedirectResponse
    {
        $packagingMaterial->update(['is_active' => $request->boolean('is_active')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $packagingMaterial->is_active
            ? __(':name is now active.', ['name' => $packagingMaterial->name])
            : __(':name is now inactive.', ['name' => $packagingMaterial->name])]);

        return back();
    }

    /** Add stock. */
    public function restock(Request $request, PackagingMaterial $packagingMaterial, RestockPackagingMaterialAction $action): RedirectResponse
    {
        $value = $request->validate(['add_qty' => ['required', 'numeric', 'gt:0', 'max:1000000']])['add_qty'];
        $action->execute($packagingMaterial, $value);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':qty :unit added to :name.', ['qty' => $value, 'unit' => $packagingMaterial->unit, 'name' => $packagingMaterial->name])]);

        return back();
    }

    public function destroy(PackagingMaterial $packagingMaterial, DeletePackagingMaterialAction $delete): RedirectResponse
    {
        $delete->execute($packagingMaterial);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $packagingMaterial->name])]);

        return back();
    }
}
