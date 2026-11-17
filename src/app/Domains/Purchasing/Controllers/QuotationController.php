<?php

namespace App\Domains\Purchasing\Controllers;

use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Purchasing\Actions\ConvertQuotationAction;
use App\Domains\Purchasing\Actions\CreateQuotationAction;
use App\Domains\Purchasing\Actions\DeleteQuotationAction;
use App\Domains\Purchasing\Actions\UpdateQuotationAction;
use App\Domains\Purchasing\Data\QuotationData;
use App\Domains\Purchasing\Enums\QuotationPermission;
use App\Domains\Purchasing\Models\Quotation;
use App\Domains\Purchasing\Queries\QuotationQueries;
use App\Domains\Purchasing\Queries\QuotationSummaryQuery;
use App\Domains\Purchasing\Resources\QuotationResource;
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

class QuotationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.QuotationPermission::QuotationsView->value, only: ['index']),
            new Middleware('can:'.QuotationPermission::QuotationsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.QuotationPermission::QuotationsEdit->value, only: ['edit', 'update', 'convert']),
            new Middleware('can:'.QuotationPermission::QuotationsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, QuotationQueries $queries, QuotationSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'quotations', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Quotation $quotation) => QuotationResource::fromModel($quotation));

        return Inertia::render('quotation/index', [
            'records' => QuotationResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
        ]);
    }

    #[FormRules(QuotationData::class)]
    public function create(): Response
    {
        return Inertia::render('quotation/create', [
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
        ]);
    }

    public function store(QuotationData $data, CreateQuotationAction $create): RedirectResponse
    {
        $quotation = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $quotation->quotation_number])]);

        return to_route('admin.quotations.index');
    }

    #[FormRules(QuotationData::class)]
    public function edit(Quotation $quotation, QuotationQueries $queries): Response
    {
        return Inertia::render('quotation/edit', [
            'record' => QuotationResource::fromModel($quotation->load(['supplier', 'items.ingredient'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
        ]);
    }

    public function update(Quotation $quotation, QuotationData $data, UpdateQuotationAction $update): RedirectResponse
    {
        $update->execute($quotation, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $quotation->quotation_number])]);

        return to_route('admin.quotations.index');
    }

    /** Convert to PO. */
    public function convert(Quotation $quotation, ConvertQuotationAction $action): RedirectResponse
    {
        $action->execute($quotation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':number converted to a purchase order.', ['number' => $quotation->quotation_number])]);

        return back();
    }

    public function destroy(Quotation $quotation, DeleteQuotationAction $delete): RedirectResponse
    {
        $delete->execute($quotation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $quotation->quotation_number])]);

        return back();
    }
}
