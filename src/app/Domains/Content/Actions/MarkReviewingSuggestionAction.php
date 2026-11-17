<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\Suggestion;
use Illuminate\Support\Facades\DB;

/** Start reviewing. */
final class MarkReviewingSuggestionAction
{
    public function execute(Suggestion $suggestion): Suggestion
    {
        DB::transaction(function () use ($suggestion) {
            $suggestion->update(['status' => 'reviewing']);
        });

        return $suggestion->refresh();
    }
}
