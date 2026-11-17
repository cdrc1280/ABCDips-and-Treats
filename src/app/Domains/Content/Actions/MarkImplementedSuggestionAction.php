<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\Suggestion;
use Illuminate\Support\Facades\DB;

/** Mark implemented. */
final class MarkImplementedSuggestionAction
{
    public function execute(Suggestion $suggestion): Suggestion
    {
        DB::transaction(function () use ($suggestion) {
            $suggestion->update(['status' => 'implemented']);
        });

        return $suggestion->refresh();
    }
}
