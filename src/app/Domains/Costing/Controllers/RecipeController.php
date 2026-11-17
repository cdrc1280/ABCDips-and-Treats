<?php

namespace App\Domains\Costing\Controllers;

use App\Domains\Catalog\Models\Product;
use App\Domains\Costing\Actions\ApplyPriceRecipeAction;
use App\Domains\Costing\Actions\CreateRecipeAction;
use App\Domains\Costing\Actions\DeleteRecipeAction;
use App\Domains\Costing\Actions\UpdateRecipeAction;
use App\Domains\Costing\Data\RecipeData;
use App\Domains\Costing\Enums\RecipePermission;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Costing\Queries\RecipeQueries;
use App\Domains\Costing\Resources\RecipeResource;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\PackagingMaterial;
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

class RecipeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.RecipePermission::RecipesView->value, only: ['index']),
            new Middleware('can:'.RecipePermission::RecipesCreate->value, only: ['create', 'store']),
            new Middleware('can:'.RecipePermission::RecipesEdit->value, only: ['edit', 'update', 'applyPrice']),
            new Middleware('can:'.RecipePermission::RecipesDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, RecipeQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'recipes', ['sort_field' => 'name', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Recipe $recipe) => RecipeResource::fromModel($recipe));

        return Inertia::render('recipe/index', [
            'records' => RecipeResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
        ]);
    }

    #[FormRules(RecipeData::class)]
    public function create(): Response
    {
        return Inertia::render('recipe/create', [
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
            'packaging_options' => fn () => OptionsQuery::for(PackagingMaterial::class, 'name', fn ($q) => $q->where('is_active', true), 'unit'),
        ]);
    }

    public function store(RecipeData $data, CreateRecipeAction $create): RedirectResponse
    {
        $recipe = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $recipe->name])]);

        return to_route('admin.recipes.index');
    }

    #[FormRules(RecipeData::class)]
    public function edit(Recipe $recipe, RecipeQueries $queries): Response
    {
        return Inertia::render('recipe/edit', [
            'record' => RecipeResource::fromModel($recipe->load(['product', 'recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
            'packaging_options' => fn () => OptionsQuery::for(PackagingMaterial::class, 'name', fn ($q) => $q->where('is_active', true), 'unit'),
        ]);
    }

    public function update(Recipe $recipe, RecipeData $data, UpdateRecipeAction $update): RedirectResponse
    {
        $update->execute($recipe, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $recipe->name])]);

        return to_route('admin.recipes.index');
    }

    /** Use suggested price. */
    public function applyPrice(Recipe $recipe, ApplyPriceRecipeAction $action): RedirectResponse
    {
        $action->execute($recipe);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':product now sells for ₱:price.', ['product' => $recipe->product?->name, 'price' => number_format((float) $recipe->unit_selling_price, 2)])]);

        return back();
    }

    public function destroy(Recipe $recipe, DeleteRecipeAction $delete): RedirectResponse
    {
        $delete->execute($recipe);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $recipe->name])]);

        return back();
    }
}
