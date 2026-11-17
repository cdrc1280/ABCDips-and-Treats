<?php

namespace App\Domains\Production\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Catalog\Models\Product;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Production\Data\ProductionBatchFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionBatch extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['batch_number'];

    public const STATUS_PLANNED = 'planned';

    public const STATUS_IN_PREP = 'in_prep';

    public const STATUS_BAKING = 'baking';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'batch_number',
        'recipe_id',
        'product_id',
        'planned_qty',
        'actual_yield_qty',
        'status',
        'baker_user_id',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'planned_qty' => 'integer',
        'actual_yield_qty' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /** @return BelongsTo<Recipe, $this> */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<User, $this> */
    public function baker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'baker_user_id');
    }

    /**
     * string → global search; array → ProductionBatchFilterData fields.
     *
     * @param  Builder<ProductionBatch>  $query
     * @return Builder<ProductionBatch>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('batch_number', "%{$search}%")
                ->orWhereLike('notes', "%{$search}%")
                ->orWhereHas('product', fn (Builder $r) => $r->whereLike('name', "%{$search}%"))
                ->orWhereHas('recipe', fn (Builder $r) => $r->whereLike('name', "%{$search}%")));
        }

        $filters = ProductionBatchFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status))
            ->when($filters->productId !== null, fn (Builder $q) => $q->where('product_id', $filters->productId));
    }
}
