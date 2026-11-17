<?php

namespace App\Domains\Access\Data;

use App\Domains\Access\Support\PermissionRegistry;
use App\Shared\Support\RouteModel;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class RoleData extends Data
{
    public function __construct(
        public string $name,
        public array $permissions = [],
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = Str::slug(trim((string) ($properties['name'] ?? '')) ?: (string) ($properties['name'] ?? ''), '_');
        $properties['permissions'] = array_values(array_unique(array_filter(array_map('strval', (array) ($properties['permissions'] ?? [])))));

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('role');

        return [
            'name' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($id)],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in(PermissionRegistry::names())],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [

        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'name' => __('role name'),
            'permissions' => __('permissions'),
        ];
    }
}
