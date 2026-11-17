<?php

namespace App\Domains\Pos\Queries;

use App\Domains\Catalog\Models\Product;

final class PosProductQueries
{
    /**
     * Everything sellable at the register, lightweight (one query + media).
     *
     * @return array<int, array<string, mixed>>
     */
    public function sellable(): array
    {
        return Product::query()
            ->with(['category:id,name', 'media'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'barcode' => $p->barcode,
                'category' => $p->category?->name,
                'price' => (float) $p->effective_price,
                'regular_price' => (float) $p->price,
                'stock' => (int) $p->stock_qty,
                'image_url' => $p->primary_image_url,
            ])
            ->values()->all();
    }
}
