<?php

namespace App\Domains\Sales\Data;

use App\Rules\PhilippinePhone;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class OrderData extends Data
{
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $paymentStatus,
        public ?string $deliveryAddress = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $scheduledTime = null,
        public ?string $notes = null,
        public ?string $paymentReference = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['customer_name'] = trim((string) ($properties['customer_name'] ?? '')) ?: null;
        $properties['customer_email'] = strtolower(trim((string) ($properties['customer_email'] ?? ''))) ?: null;
        $properties['customer_phone'] = trim((string) ($properties['customer_phone'] ?? '')) ?: null;
        $properties['payment_status'] = trim((string) ($properties['payment_status'] ?? '')) ?: null;
        $properties['delivery_address'] = trim((string) ($properties['delivery_address'] ?? '')) ?: null;
        $properties['city'] = trim((string) ($properties['city'] ?? '')) ?: null;
        $properties['postal_code'] = trim((string) ($properties['postal_code'] ?? '')) ?: null;
        $properties['scheduled_time'] = trim((string) ($properties['scheduled_time'] ?? '')) ?: null;
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;
        $properties['payment_reference'] = trim((string) ($properties['payment_reference'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('order');

        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', new PhilippinePhone],
            'payment_status' => ['required', Rule::in(['pending', 'awaiting_payment', 'paid', 'failed', 'refunded'])],
            'delivery_address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'digits:4'],
            'scheduled_time' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
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
            'customer_name' => __('customer name'),
            'customer_email' => __('email'),
            'customer_phone' => __('phone'),
            'delivery_address' => __('delivery address'),
            'city' => __('city'),
            'postal_code' => __('postal code'),
            'scheduled_time' => __('scheduled for'),
            'notes' => __('customer notes'),
            'payment_status' => __('payment status'),
            'payment_reference' => __('payment reference'),
        ];
    }
}
