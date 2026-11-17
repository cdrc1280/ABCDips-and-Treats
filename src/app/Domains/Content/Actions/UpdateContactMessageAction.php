<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Data\ContactMessageData;
use App\Domains\Content\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

final class UpdateContactMessageAction
{
    public function execute(ContactMessage $contactMessage, ContactMessageData $data): ContactMessage
    {
        return DB::transaction(function () use ($contactMessage, $data) {
            $contactMessage->update([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'subject' => $data->subject,
                'message' => $data->message,
                'status' => $data->status,
                'admin_notes' => $data->adminNotes,
            ]);

            return $contactMessage;
        });
    }
}
