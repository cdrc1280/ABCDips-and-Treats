<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

/** Mark as read. */
final class MarkReadContactMessageAction
{
    public function execute(ContactMessage $contactMessage): ContactMessage
    {
        DB::transaction(function () use ($contactMessage) {
            $contactMessage->update(['status' => 'read']);
        });

        return $contactMessage->refresh();
    }
}
