<?php

namespace App\Domains\Inventory\Data;

use App\Rules\PhilippinePhone;
use App\Shared\Support\RouteModel;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class SupplierData extends Data
{
    public function __construct(
        public string $name,
        public string $contactPerson,
        public string $email,
        public string $phone,
        public string $address,
        public ?string $paymentTerms = 'Net 30',
        public ?string $notes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['contact_person'] = trim((string) ($properties['contact_person'] ?? '')) ?: null;
        $properties['email'] = strtolower(trim((string) ($properties['email'] ?? ''))) ?: null;
        $properties['phone'] = trim((string) ($properties['phone'] ?? '')) ?: null;
        $properties['address'] = trim((string) ($properties['address'] ?? '')) ?: null;
        $properties['payment_terms'] = trim((string) ($properties['payment_terms'] ?? '')) ?: 'Net 30';
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('supplier');

        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', new PhilippinePhone],
            'address' => ['required', 'string', 'max:1000'],
            'payment_terms' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
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
            'name' => __('company name'),
            'contact_person' => __('contact person'),
            'email' => __('email'),
            'phone' => __('phone'),
            'payment_terms' => __('payment terms'),
            'address' => __('address'),
            'notes' => __('notes'),
        ];
    }
}
