<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionBatch extends Model
{
    use HasFactory;

    public const STATUS_PLANNED       = 'planned';
    public const STATUS_IN_PREP       = 'in_prep';
    public const STATUS_BAKING        = 'baking';
    public const STATUS_COMPLETED     = 'completed';
    public const STATUS_CANCELLED     = 'cancelled';

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
        'planned_qty'      => 'integer',
        'actual_yield_qty' => 'integer',
        'started_at'       => 'datetime',
        'completed_at'     => 'datetime',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function baker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'baker_user_id');
    }
}
