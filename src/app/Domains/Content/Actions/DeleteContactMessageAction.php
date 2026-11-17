<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

final class DeleteContactMessageAction
{
    public function execute(ContactMessage $contactMessage): void
    {
        DB::transaction(fn () => $contactMessage->delete());
    }
}
