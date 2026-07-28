<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'order_number'      => $this->order_number,
            'tracking_token'    => $this->tracking_token,
            'customer_name'     => $this->customer_name,
            'customer_email'    => $this->customer_email,
            'customer_phone'    => $this->customer_phone,
            'fulfillment_type'  => $this->fulfillment_type,
            'delivery_address'  => $this->delivery_address,
            'city'              => $this->city,
            'postal_code'       => $this->postal_code,
            'scheduled_time'    => $this->scheduled_time?->toISOString(),
            'notes'             => $this->notes,
            'subtotal'          => (float) $this->subtotal,
            'discount_amount'   => (float) $this->discount_amount,
            'coupon_code'       => $this->coupon_code,
            'delivery_fee'      => (float) $this->delivery_fee,
            'total'             => (float) $this->total,
            'payment_method'    => $this->payment_method,
            'payment_status'    => $this->payment_status,
            'payment_reference' => $this->payment_reference,
            'status'            => $this->status,
            'status_label'      => ucwords(str_replace('_', ' ', $this->status)),
            'items'             => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id'           => $item->id,
                'product_id'   => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku'  => $item->product_sku,
                'product_slug' => $item->product?->slug,
                'qty'          => $item->qty,
                'unit_price'   => (float) $item->unit_price,
                'subtotal'     => (float) $item->subtotal,
                'image_url'    => $item->product?->primary_image_url,
            ])),
            'timeline'          => $this->whenLoaded('statusHistories', fn () => $this->statusHistories->map(fn ($h) => [
                'from_status' => $h->from_status,
                'to_status'   => $h->to_status,
                'label'       => ucwords(str_replace('_', ' ', $h->to_status)),
                'comment'     => $h->comment,
                'created_at'  => $h->created_at->toISOString(),
            ])),
            'created_at'        => $this->created_at->toISOString(),
        ];
    }
}
