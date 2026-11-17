<?php

namespace App\Domains\Catalog\Actions;

use App\Domains\Catalog\Models\Product;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteProductAction
{
    public function execute(Product $product): void
    {
        if ($product->orderItems()->exists()) {
            throw new DomainRuleViolation(__(':name appears in past orders. Turn it off instead of deleting it.', ['name' => $product->name]));
        }

        DB::transaction(fn () => $product->delete());
    }
}
