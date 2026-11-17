<?php

namespace App\Domains\Catalog\Controllers;

use App\Domains\Catalog\Actions\CreateCategoryAction;
use App\Domains\Catalog\Actions\DeleteCategoryAction;
use App\Domains\Catalog\Actions\UpdateCategoryAction;
use App\Domains\Catalog\Data\CategoryData;
use App\Domains\Catalog\Enums\CategoryPermission;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Catalog\Queries\CategoryQueries;
use App\Domains\Catalog\Resources\CategoryResource;
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

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.CategoryPermission::CategoriesView->value, only: ['index']),
            new Middleware('can:'.CategoryPermission::CategoriesCreate->value, only: ['create', 'store']),
            new Middleware('can:'.CategoryPermission::CategoriesEdit->value, only: ['edit', 'update', 'toggle']),
            new Middleware('can:'.CategoryPermission::CategoriesDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, CategoryQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'categories', ['sort_field' => 'sort_order', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (ProductCategory $category) => CategoryResource::fromModel($category));

        return Inertia::render('category/index', [
            'records' => CategoryResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(CategoryData::class)]
    public function create(): Response
    {
        return Inertia::render('category/create');
    }

    public function store(CategoryData $data, CreateCategoryAction $create): RedirectResponse
    {
        $category = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $category->name])]);

        return to_route('admin.categories.index');
    }

    #[FormRules(CategoryData::class)]
    public function edit(ProductCategory $category, CategoryQueries $queries): Response
    {
        return Inertia::render('category/edit', [
            'record' => CategoryResource::fromModel($category->loadCount('products')),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(ProductCategory $category, CategoryData $data, UpdateCategoryAction $update): RedirectResponse
    {
        $update->execute($category, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $category->name])]);

        return to_route('admin.categories.index');
    }

    /** Inline status switch on the index table. */
    public function toggle(Request $request, ProductCategory $category): RedirectResponse
    {
        $category->update(['is_active' => $request->boolean('is_active')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $category->is_active
            ? __(':name is now visible on the storefront.', ['name' => $category->name])
            : __(':name is now hidden from the storefront.', ['name' => $category->name])]);

        return back();
    }

    public function destroy(ProductCategory $category, DeleteCategoryAction $delete): RedirectResponse
    {
        $delete->execute($category);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $category->name])]);

        return back();
    }
}
