<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'token'           => $this->token,
            'coupon_code'     => $this->coupon_code,
            'discount_amount' => (float) $this->discount_amount,
            'subtotal'        => $this->subtotal,
            'total'           => $this->total,
            'item_count'      => $this->items->sum('qty'),
            'last_active_at'  => $this->last_active_at->toISOString(),
            'expires_at'      => $this->expires_at->toISOString(),
            'items'           => $this->items->map(fn ($item) => [
                'id'         => $item->id,
                'product_id' => $item->product_id,
                'name'       => $item->product->name,
                'sku'        => $item->product->sku,
                'slug'       => $item->product->slug,
                'image_url'  => $item->product->primary_image_url,
                'qty'        => $item->qty,
                'unit_price' => (float) $item->unit_price,
                'subtotal'   => $item->subtotal,
                'options'    => $item->options,
            ]),
        ];
    }
}
