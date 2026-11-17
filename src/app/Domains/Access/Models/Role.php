<?php

namespace App\Domains\Access\Models;

use App\Domains\Access\Data\RoleFilterData;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * App role model (config/permission.php → models.role). Extends Spatie's role with the
 * search scope the back-office table uses; behaviour is otherwise Spatie's.
 */
class Role extends SpatieRole
{
    use HasUuids, LogsModelActivity;

    /** Built-in roles the app relies on: they can't be renamed or deleted from the UI. */
    public const SYSTEM = ['super_admin', 'admin', 'manager', 'staff', 'customer'];

    public function isSystem(): bool
    {
        return in_array($this->name, self::SYSTEM, true);
    }

    /**
     * string → global search; array → RoleFilterData fields.
     *
     * @param  Builder<Role>  $query
     * @return Builder<Role>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$search}%"));
        }

        $filters = RoleFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword));
    }
}
