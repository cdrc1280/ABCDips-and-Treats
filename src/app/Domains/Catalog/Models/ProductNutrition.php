<?php

namespace App\Domains\Catalog\Models;

use App\Concerns\InteractsWithModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductNutrition extends Model
{
    use HasUuids, InteractsWithModelFactory;

    protected $table = 'product_nutritions';

    protected $fillable = [
        'product_id',
        'serving_size',
        'calories',
        'fat_g',
        'carbs_g',
        'protein_g',
        'sodium_mg',
        'sugar_g',
    ];

    protected $casts = [
        'calories' => 'integer',
        'fat_g' => 'decimal:2',
        'carbs_g' => 'decimal:2',
        'protein_g' => 'decimal:2',
        'sodium_mg' => 'decimal:2',
        'sugar_g' => 'decimal:2',
    ];

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
