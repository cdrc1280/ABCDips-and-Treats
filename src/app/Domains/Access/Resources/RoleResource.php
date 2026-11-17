<?php

namespace App\Domains\Access\Resources;

use App\Domains\Access\Models\Role;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class RoleResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public array $permissions,
        public int $usersCount,
        public int $permissionsCount,
        public bool $isSystem,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Role $role, array $context = []): self
    {
        return new self(
            id: (string) $role->id,
            name: $role->name,
            permissions: $role->permissions->pluck('name')->values()->all(),
            usersCount: (int) ($role->users_count ?? $role->users()->count()),
            permissionsCount: (int) ($role->permissions_count ?? $role->permissions()->count()),
            isSystem: $role->isSystem(),
            createdAt: $role->created_at?->toIso8601String(),
            updatedAt: $role->updated_at?->toIso8601String(),
        );
    }
}
