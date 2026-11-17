<?php

namespace App\Domains\Storefront\Listeners;

use App\Domains\Storefront\Support\CurrentCart;
use App\Domains\User\Models\User;
use Illuminate\Auth\Events\Login;

final class MergeGuestCart
{
    public function __construct(private readonly CurrentCart $current) {}

    public function handle(Login $event): void
    {
        $request = request();
        if ($event->user instanceof User && $request->hasSession()) {
            $this->current->mergeGuestCart($request, $event->user);
        }
    }
}
