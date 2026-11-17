<?php

namespace App\Domains\User\Data;

use App\Domains\Access\Support\AssignableRoles;
use App\Rules\PhilippinePhone;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class UserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public array $roles = [],
        public ?string $phone = null,
        public ?string $password = null,
        public ?string $passwordConfirmation = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['email'] = strtolower(trim((string) ($properties['email'] ?? ''))) ?: null;
        $properties['roles'] = array_values(array_unique(array_filter(array_map('strval', (array) ($properties['roles'] ?? [])))));
        $properties['phone'] = trim((string) ($properties['phone'] ?? '')) ?: null;
        $properties['password'] = (string) ($properties['password'] ?? '') ?: null;
        $properties['password_confirmation'] = (string) ($properties['password_confirmation'] ?? '') ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at')],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::in(AssignableRoles::names())],
            'phone' => ['nullable', 'string', new PhilippinePhone],
            'password' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn () => request()->route('user') === null), 'confirmed', Password::defaults()],
            'password_confirmation' => ['nullable', 'string', 'max:255'],
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
            'name' => __('full name'),
            'email' => __('email'),
            'phone' => __('phone'),
            'roles' => __('roles'),
            'password' => __('password'),
            'password_confirmation' => __('confirm password'),
        ];
    }
}
