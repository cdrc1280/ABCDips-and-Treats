<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'avatar_url'        => $this->getFirstMediaUrl('avatar'),
            'phone'             => $this->phone,
            'address'           => $this->address,
            'city'              => $this->city,
            'region'            => $this->region,
            'province'          => $this->province,
            'barangay'          => $this->barangay,
            'street_address'    => $this->street_address,
            'roles'             => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'created_at'        => $this->created_at->toISOString(),
        ];
    }
}
