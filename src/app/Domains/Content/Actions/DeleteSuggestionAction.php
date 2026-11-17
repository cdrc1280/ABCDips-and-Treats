<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\Suggestion;
use Illuminate\Support\Facades\DB;

final class DeleteSuggestionAction
{
    public function execute(Suggestion $suggestion): void
    {
        DB::transaction(fn () => $suggestion->delete());
    }
}
