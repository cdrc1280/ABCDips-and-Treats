<?php

namespace App\Domains\Menu\Models;

use App\Domains\Access\Models\Permission;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Guarded(['id'])]
class Menu extends Model
{
    use HasUuids, SoftDeletes;

    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer'];

    /** @return HasMany<Menu, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    /** @return BelongsTo<Menu, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /** @return BelongsTo<Permission, $this> */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
