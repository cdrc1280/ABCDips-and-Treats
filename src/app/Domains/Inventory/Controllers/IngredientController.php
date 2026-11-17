<?php

namespace App\Domains\Inventory\Controllers;

use App\Domains\Inventory\Actions\CreateIngredientAction;
use App\Domains\Inventory\Actions\DeleteIngredientAction;
use App\Domains\Inventory\Actions\RestockIngredientAction;
use App\Domains\Inventory\Actions\UpdateIngredientAction;
use App\Domains\Inventory\Data\IngredientData;
use App\Domains\Inventory\Enums\IngredientPermission;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Inventory\Queries\IngredientQueries;
use App\Domains\Inventory\Queries\IngredientSummaryQuery;
use App\Domains\Inventory\Resources\IngredientResource;
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

class IngredientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.IngredientPermission::IngredientsView->value, only: ['index']),
            new Middleware('can:'.IngredientPermission::IngredientsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.IngredientPermission::IngredientsEdit->value, only: ['edit', 'update', 'restock']),
            new Middleware('can:'.IngredientPermission::IngredientsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, IngredientQueries $queries, IngredientSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'ingredients', ['sort_field' => 'name', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Ingredient $ingredient) => IngredientResource::fromModel($ingredient));

        return Inertia::render('ingredient/index', [
            'records' => IngredientResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
        ]);
    }

    #[FormRules(IngredientData::class)]
    public function create(): Response
    {
        return Inertia::render('ingredient/create', [
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
        ]);
    }

    public function store(IngredientData $data, CreateIngredientAction $create): RedirectResponse
    {
        $ingredient = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $ingredient->name])]);

        return to_route('admin.ingredients.index');
    }

    #[FormRules(IngredientData::class)]
    public function edit(Ingredient $ingredient, IngredientQueries $queries): Response
    {
        return Inertia::render('ingredient/edit', [
            'record' => IngredientResource::fromModel($ingredient->load('supplier')->loadCount('recipeIngredients')),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
        ]);
    }

    public function update(Ingredient $ingredient, IngredientData $data, UpdateIngredientAction $update): RedirectResponse
    {
        $update->execute($ingredient, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $ingredient->name])]);

        return to_route('admin.ingredients.index');
    }

    /** Add stock. */
    public function restock(Request $request, Ingredient $ingredient, RestockIngredientAction $action): RedirectResponse
    {
        $value = $request->validate(['add_qty' => ['required', 'numeric', 'gt:0', 'max:10000000']])['add_qty'];
        $action->execute($ingredient, $value);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':qty :unit added to :name.', ['qty' => $value, 'unit' => $ingredient->unit, 'name' => $ingredient->name])]);

        return back();
    }

    public function destroy(Ingredient $ingredient, DeleteIngredientAction $delete): RedirectResponse
    {
        $delete->execute($ingredient);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $ingredient->name])]);

        return back();
    }
}
