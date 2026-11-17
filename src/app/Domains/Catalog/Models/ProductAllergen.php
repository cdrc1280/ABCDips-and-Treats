<?php

namespace App\Domains\Catalog\Models;

use App\Concerns\InteractsWithModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAllergen extends Model
{
    use HasUuids, InteractsWithModelFactory;

    protected $fillable = ['product_id', 'allergen_name', 'type'];

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
