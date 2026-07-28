<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'product_id'        => $this->product_id,
            'reviewer_name'     => $this->reviewer_name,
            'rating'            => $this->rating,
            'title'             => $this->title,
            'comment'           => $this->comment,
            'is_verified_buyer' => $this->is_verified_buyer,
            'is_featured'       => $this->is_featured,
            'helpful_votes'     => $this->helpful_votes,
            'photos'            => $this->getMedia('review_photos')->map(fn ($m) => $m->getUrl()),
            'created_at'        => $this->created_at->toISOString(),
        ];
    }
}
