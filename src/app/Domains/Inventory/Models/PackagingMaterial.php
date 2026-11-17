<?php

namespace App\Domains\Inventory\Models;

use App\Domains\Inventory\Data\PackagingMaterialFilterData;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackagingMaterial extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['sku'];

    protected $fillable = ['name', 'sku', 'type', 'unit', 'cost_per_unit', 'stock_qty', 'min_stock_qty', 'supplier_id', 'notes', 'is_active'];

    protected $casts = ['cost_per_unit' => 'decimal:2', 'stock_qty' => 'decimal:2', 'min_stock_qty' => 'decimal:2', 'is_active' => 'boolean'];

    /** @return HasMany<PackagingMovement, $this> */
    public function movements(): HasMany
    {
        return $this->hasMany(PackagingMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->min_stock_qty;
    }

    /**
     * string → global search; array → PackagingMaterialFilterData fields.
     *
     * @param  Builder<PackagingMaterial>  $query
     * @return Builder<PackagingMaterial>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('sku', "%{$search}%")
                ->orWhereLike('type', "%{$search}%"));
        }

        $filters = PackagingMaterialFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->type !== null && $filters->type !== '', fn (Builder $q) => $q->where('type', $filters->type))
            ->when($filters->isActive !== null, fn (Builder $q) => $q->where('is_active', $filters->isActive));
    }
}
