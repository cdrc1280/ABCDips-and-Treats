<?php

namespace App\Domains\Access\Actions;

use App\Domains\Access\Models\Role;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteRoleAction
{
    public function execute(Role $role): void
    {
        if ($role->isSystem() || $role->users()->exists()) {
            throw new DomainRuleViolation(__('Built-in roles and roles still assigned to someone cannot be deleted.'));
        }

        DB::transaction(fn () => $role->delete());
    }
}
