<?php

namespace App\Domains\Preference\Controllers;

use App\Domains\Preference\Models\Preference;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Saves UI preferences that aren't part of a page request: table column layouts
 * (use-save-preference) and the sidebar open/closed state.
 */
class PreferenceController extends Controller
{
    public function updateTableColumns(Request $request, string $table): RedirectResponse
    {
        abort_unless(preg_match('/^[\w-]+$/', $table) === 1, 404);

        $columns = $request->validate([
            'hidden' => ['array'], 'hidden.*' => ['string'],
            'order' => ['array'], 'order.*' => ['string'],
            'widths' => ['array'], 'widths.*' => ['integer', 'min:40', 'max:1200'],
            'pinned' => ['array'], 'pinned.*' => ['string'],
        ]);

        $key = "tables.{$table}";
        $saved = (array) Preference::getFor($request->user(), $key, []);
        $saved['columns'] = $columns;
        Preference::putFor($request->user(), $key, $saved);

        return back();
    }
}
