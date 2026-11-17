<?php

namespace App\Domains\Storefront\Data;

use App\Rules\PhilippinePhone;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/** Checkout form. No price, fee or total is accepted from the browser. */
#[MapName(SnakeCaseMapper::class)]
final class CheckoutData extends Data
{
    public const PAYMENT_METHODS = ['gcash', 'maya', 'qrph', 'bank_transfer'];

    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $fulfillmentType,
        public string $deliveryMode,
        public string $paymentMethod,
        public ?string $region = null,
        public ?string $province = null,
        public ?string $city = null,
        public ?string $barangay = null,
        public ?string $streetAddress = null,
        public ?float $lat = null,
        public ?float $lng = null,
        public ?string $scheduledTime = null,
        public ?string $notes = null,
        public bool $saveAddress = false,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['customer_name', 'customer_email', 'customer_phone', 'region', 'province', 'city', 'barangay', 'street_address', 'notes', 'scheduled_time'] as $key) {
            $value = trim(strip_tags((string) ($properties[$key] ?? '')));
            $properties[$key] = $value === '' ? null : $value;
        }
        $properties['delivery_mode'] = ($properties['fulfillment_type'] ?? null) === 'delivery' ? ($properties['delivery_mode'] ?? 'priority') : 'priority';
        $properties['lat'] = is_numeric($properties['lat'] ?? null) ? (float) $properties['lat'] : null;
        $properties['lng'] = is_numeric($properties['lng'] ?? null) ? (float) $properties['lng'] : null;
        $properties['save_address'] = filter_var($properties['save_address'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email:rfc', 'max:255'],
            'customer_phone' => ['required', new PhilippinePhone],
            'fulfillment_type' => ['required', 'in:delivery,pickup'],
            'delivery_mode' => ['required', 'in:priority,pooling'],
            'payment_method' => ['required', 'in:'.implode(',', self::PAYMENT_METHODS)],
            'region' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'city' => ['required_if:fulfillment_type,delivery', 'nullable', 'string', 'max:255'],
            'barangay' => ['required_if:fulfillment_type,delivery', 'nullable', 'string', 'max:255'],
            'street_address' => ['required_if:fulfillment_type,delivery', 'nullable', 'string', 'max:500'],
            'lat' => ['nullable', 'numeric', 'between:4,22'],
            'lng' => ['nullable', 'numeric', 'between:114,128'],
            'scheduled_time' => ['nullable', 'date', 'after_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'save_address' => ['boolean'],
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return ['customer_name' => 'name', 'customer_email' => 'email', 'customer_phone' => 'mobile number', 'street_address' => 'street address'];
    }

    public function deliveryAddress(): ?string
    {
        if ($this->fulfillmentType !== 'delivery') {
            return null;
        }

        return implode(', ', array_filter([$this->streetAddress, $this->barangay, $this->city, $this->province]));
    }
}
