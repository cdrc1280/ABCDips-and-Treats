<?php

namespace App\Domains\Catalog\Actions;

use App\Domains\Catalog\Models\ProductCategory;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteCategoryAction
{
    public function execute(ProductCategory $category): void
    {
        $products = $category->products()->count();

        if ($products > 0) {
            throw new DomainRuleViolation(trans_choice(
                ':name still has :count product. Move or delete it first.|:name still has :count products. Move or delete them first.',
                $products,
                ['name' => $category->name, 'count' => $products],
            ));
        }

        DB::transaction(fn () => $category->delete());
    }
}
