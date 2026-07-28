<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductNutrition extends Model
{
    use HasFactory;

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
        'calories'  => 'integer',
        'fat_g'     => 'decimal:2',
        'carbs_g'   => 'decimal:2',
        'protein_g' => 'decimal:2',
        'sodium_mg' => 'decimal:2',
        'sugar_g'   => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
