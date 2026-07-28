<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'reference_number'  => $this->reference_number,
            'customer_name'     => $this->customer_name,
            'customer_email'    => $this->customer_email,
            'customer_phone'    => $this->customer_phone,
            'event_date'        => $this->event_date?->toDateString(),
            'servings_count'    => $this->servings_count,
            'tiers_count'       => $this->tiers_count,
            'flavor_preference' => $this->flavor_preference,
            'frosting_type'     => $this->frosting_type,
            'theme_description' => $this->theme_description,
            'budget_range'      => [
                'min' => $this->budget_range_min ? (float) $this->budget_range_min : null,
                'max' => $this->budget_range_max ? (float) $this->budget_range_max : null,
            ],
            'quoted_price'      => $this->quoted_price ? (float) $this->quoted_price : null,
            'status'            => $this->status,
            'status_label'      => ucwords(str_replace('_', ' ', $this->status)),
            'photos'            => $this->getMedia('reference_photos')->map(fn ($m) => $m->getUrl()),
            'created_at'        => $this->created_at->toISOString(),
        ];
    }
}
