<?php

namespace App\Domains\User\Resources;

use App\Domains\User\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class UserResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public array $roles,
        public bool $emailVerified,
        public string $rolesLabel,
        public bool $isSelf,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(User $user, array $context = []): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            phone: $user->phone,
            roles: $user->getRoleNames()->values()->all(),
            emailVerified: $user->hasVerifiedEmail(),
            rolesLabel: $user->getRoleNames()->map(fn ($r) => ucwords(str_replace('_', ' ', $r)))->implode(', '),
            isSelf: $user->is(auth()->user()),
            createdAt: $user->created_at?->toIso8601String(),
            updatedAt: $user->updated_at?->toIso8601String(),
        );
    }
}
