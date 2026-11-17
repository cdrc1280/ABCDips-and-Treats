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
final class CustomOrderData extends Data
{
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $eventDate,
        public string $themeDescription,
        public ?string $referenceNumber = null,
        public ?int $servingsCount = 20,
        public ?int $tiersCount = 1,
        public ?string $flavorPreference = null,
        public ?string $frostingType = null,
        public ?float $budgetRangeMin = null,
        public ?float $budgetRangeMax = null,
        public ?float $quotedPrice = null,
        public ?string $status = 'inquiry',
        public ?string $staffNotes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['customer_name'] = trim((string) ($properties['customer_name'] ?? '')) ?: null;
        $properties['customer_email'] = strtolower(trim((string) ($properties['customer_email'] ?? ''))) ?: null;
        $properties['customer_phone'] = trim((string) ($properties['customer_phone'] ?? '')) ?: null;
        $properties['event_date'] = trim((string) ($properties['event_date'] ?? '')) ?: null;
        $properties['theme_description'] = trim((string) ($properties['theme_description'] ?? '')) ?: null;
        $properties['reference_number'] = strtoupper(trim((string) ($properties['reference_number'] ?? ''))) ?: null;
        $properties['servings_count'] = ($properties['servings_count'] ?? '') === '' ? 20 : (int) $properties['servings_count'];
        $properties['tiers_count'] = ($properties['tiers_count'] ?? '') === '' ? 1 : (int) $properties['tiers_count'];
        $properties['flavor_preference'] = trim((string) ($properties['flavor_preference'] ?? '')) ?: null;
        $properties['frosting_type'] = trim((string) ($properties['frosting_type'] ?? '')) ?: null;
        $properties['budget_range_min'] = ($properties['budget_range_min'] ?? '') === '' ? null : (float) $properties['budget_range_min'];
        $properties['budget_range_max'] = ($properties['budget_range_max'] ?? '') === '' ? null : (float) $properties['budget_range_max'];
        $properties['quoted_price'] = ($properties['quoted_price'] ?? '') === '' ? null : (float) $properties['quoted_price'];
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'inquiry';
        $properties['staff_notes'] = trim((string) ($properties['staff_notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('customOrder');

        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', new PhilippinePhone],
            'event_date' => ['required', 'date'],
            'theme_description' => ['required', 'string', 'max:3000'],
            'reference_number' => ['nullable', 'string', 'max:40', Rule::unique('custom_orders', 'reference_number')->ignore($id)->whereNull('deleted_at')],
            'servings_count' => ['required', 'integer', 'min:1'],
            'tiers_count' => ['required', 'integer', 'min:1', 'max:10'],
            'flavor_preference' => ['nullable', 'string', 'max:255'],
            'frosting_type' => ['nullable', 'string', 'max:255'],
            'budget_range_min' => ['nullable', 'numeric', 'min:0'],
            'budget_range_max' => ['nullable', 'numeric', 'min:0'],
            'quoted_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['inquiry', 'quoted', 'deposit_paid', 'in_production', 'ready', 'completed', 'cancelled'])],
            'staff_notes' => ['nullable', 'string', 'max:5000'],
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
            'reference_number' => __('reference'),
            'customer_name' => __('customer name'),
            'customer_email' => __('email'),
            'customer_phone' => __('phone'),
            'event_date' => __('event date'),
            'servings_count' => __('servings'),
            'tiers_count' => __('tiers'),
            'flavor_preference' => __('flavor'),
            'frosting_type' => __('frosting'),
            'theme_description' => __('theme & design'),
            'budget_range_min' => __('budget from'),
            'budget_range_max' => __('budget to'),
            'quoted_price' => __('quoted price'),
            'status' => __('status'),
            'staff_notes' => __('staff notes'),
        ];
    }
}
