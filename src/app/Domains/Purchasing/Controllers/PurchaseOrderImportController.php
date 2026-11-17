<?php

namespace App\Domains\Purchasing\Controllers;

use App\Domains\Purchasing\Enums\PurchaseOrderPermission;
use App\Http\Controllers\Controller;
use App\Services\PoPdfParserService;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

/**
 * Reads a supplier's PO / Conforme PDF and hands the extracted fields back to the create
 * page (flash `po_import`) to prefill the form. Nothing is saved until the user saves.
 */
class PurchaseOrderImportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:'.PurchaseOrderPermission::PurchaseOrdersCreate->value)];
    }

    public function __invoke(Request $request, PoPdfParserService $parser): RedirectResponse
    {
        $file = $request->validate(['pdf' => ['required', 'file', 'mimes:pdf', 'max:10240']])['pdf'];

        $result = $parser->parsePoPdf($file->getRealPath());
        if (! ($result['success'] ?? false)) {
            throw new DomainRuleViolation($result['error'] ?? __('Could not read that PDF.'));
        }

        Inertia::flash('po_import', [
            'po_number' => $result['po_number'] ?? null,
            'supplier_id' => $result['supplier_id'] ?? null,
            'supplier_name' => $result['supplier_name'] ?? null,
            'expected_delivery_date' => $result['expected_delivery_date'] ?? null,
            'payment_terms' => $result['payment_terms'] ?? null,
            'tax' => (float) ($result['tax'] ?? 0),
            'items' => array_map(fn (array $item) => [
                'ingredient_id' => $item['ingredient_id'] ?? null,
                'item_name' => $item['item_name'] ?? '',
                'qty_ordered' => (float) ($item['qty_ordered'] ?? 0),
                'unit_cost' => (float) ($item['unit_cost'] ?? 0),
            ], (array) ($result['items'] ?? [])),
        ]);
        Inertia::flash('toast', ['type' => 'success', 'message' => __('PDF read — check the details below, then save.')]);

        return back();
    }
}
