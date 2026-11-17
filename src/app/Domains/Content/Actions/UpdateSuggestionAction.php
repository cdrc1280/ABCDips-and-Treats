<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Data\SuggestionData;
use App\Domains\Content\Models\Suggestion;
use Illuminate\Support\Facades\DB;

final class UpdateSuggestionAction
{
    public function execute(Suggestion $suggestion, SuggestionData $data): Suggestion
    {
        return DB::transaction(function () use ($suggestion, $data) {
            $suggestion->update([
                'name' => $data->name,
                'email' => $data->email,
                'category' => $data->category,
                'subject' => $data->subject,
                'message' => $data->message,
                'status' => $data->status,
                'admin_notes' => $data->adminNotes,
            ]);

            return $suggestion;
        });
    }
}
