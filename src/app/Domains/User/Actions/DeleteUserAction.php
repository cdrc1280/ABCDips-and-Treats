<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Models\User;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteUserAction
{
    public function execute(User $user): void
    {
        if ($user->is(auth()->user()) || ($user->hasRole('super_admin') && ! auth()->user()?->hasRole('super_admin'))) {
            throw new DomainRuleViolation(__('You cannot delete your own account or a super admin account.'));
        }

        DB::transaction(fn () => $user->delete());
    }
}
