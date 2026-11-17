<?php

namespace App\Domains\Storefront\Data;

use App\Rules\PhilippinePhone;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class CustomCakeInquiryData extends Data
{
    /**
     * @param  list<UploadedFile>  $referencePhotos
     */
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $eventDate,
        public int $servingsCount,
        public int $tiersCount,
        public string $themeDescription,
        public ?string $flavorPreference = null,
        public ?string $frostingType = null,
        public ?string $cakeInscription = null,
        public ?float $budgetRangeMin = null,
        public ?float $budgetRangeMax = null,
        public array $referencePhotos = [],
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['customer_name', 'customer_email', 'customer_phone', 'theme_description', 'flavor_preference', 'frosting_type', 'cake_inscription'] as $key) {
            $properties[$key] = trim(strip_tags((string) ($properties[$key] ?? ''))) ?: null;
        }
        foreach (['budget_range_min', 'budget_range_max'] as $key) {
            $properties[$key] = is_numeric($properties[$key] ?? null) ? (float) $properties[$key] : null;
        }
        $properties['reference_photos'] = array_values(array_filter((array) ($properties['reference_photos'] ?? [])));

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', new PhilippinePhone],
            'event_date' => ['required', 'date', 'after_or_equal:'.now()->addDays(2)->toDateString()],
            'servings_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'tiers_count' => ['required', 'integer', 'min:1', 'max:10'],
            'theme_description' => ['required', 'string', 'min:5', 'max:3000'],
            'flavor_preference' => ['nullable', 'string', 'max:255'],
            'frosting_type' => ['nullable', 'string', 'max:255'],
            'cake_inscription' => ['nullable', 'string', 'max:120'],
            'budget_range_min' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'budget_range_max' => ['nullable', 'numeric', 'min:0', 'max:1000000', 'gte:budget_range_min'],
            'reference_photos' => ['array', 'max:5'],
            'reference_photos.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return ['event_date.after_or_equal' => __('Custom cakes need at least 2 days’ notice.')];
    }
}
