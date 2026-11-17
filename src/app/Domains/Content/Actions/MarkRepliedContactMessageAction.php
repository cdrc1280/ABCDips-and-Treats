<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

/** Mark as replied. */
final class MarkRepliedContactMessageAction
{
    public function execute(ContactMessage $contactMessage): ContactMessage
    {
        DB::transaction(function () use ($contactMessage) {
            $contactMessage->update(['status' => 'replied', 'replied_by' => auth()->id(), 'replied_at' => now()]);
        });

        return $contactMessage->refresh();
    }
}
