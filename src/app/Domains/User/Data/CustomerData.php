<?php

namespace App\Domains\User\Data;

use App\Rules\PhilippinePhone;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class CustomerData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $streetAddress = null,
        public ?string $barangay = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $region = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['email'] = strtolower(trim((string) ($properties['email'] ?? ''))) ?: null;
        $properties['phone'] = trim((string) ($properties['phone'] ?? '')) ?: null;
        $properties['street_address'] = trim((string) ($properties['street_address'] ?? '')) ?: null;
        $properties['barangay'] = trim((string) ($properties['barangay'] ?? '')) ?: null;
        $properties['city'] = trim((string) ($properties['city'] ?? '')) ?: null;
        $properties['province'] = trim((string) ($properties['province'] ?? '')) ?: null;
        $properties['region'] = trim((string) ($properties['region'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('customer');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', new PhilippinePhone],
            'street_address' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
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
            'street_address' => __('street address'),
            'barangay' => __('barangay'),
            'city' => __('city'),
            'province' => __('province'),
            'region' => __('region'),
        ];
    }
}
