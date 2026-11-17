<?php

namespace App\Domains\Sales\Data;

use App\Domains\Sales\Models\Coupon;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class CouponData extends Data
{
    public function __construct(
        public string $code,
        public float $value,
        public ?string $type = 'fixed',
        public ?float $minSpend = 0,
        public ?float $maxDiscount = null,
        public ?int $usageLimit = null,
        public ?string $startsAt = null,
        public ?string $expiresAt = null,
        public bool $isActive = true,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['code'] = strtoupper(trim((string) ($properties['code'] ?? ''))) ?: null;
        $properties['value'] = ($properties['value'] ?? '') === '' ? null : (float) $properties['value'];
        $properties['type'] = trim((string) ($properties['type'] ?? '')) ?: 'fixed';
        $properties['min_spend'] = ($properties['min_spend'] ?? '') === '' ? 0 : (float) $properties['min_spend'];
        $properties['max_discount'] = ($properties['max_discount'] ?? '') === '' ? null : (float) $properties['max_discount'];
        $properties['usage_limit'] = ($properties['usage_limit'] ?? '') === '' ? null : (int) $properties['usage_limit'];
        $properties['starts_at'] = trim((string) ($properties['starts_at'] ?? '')) ?: null;
        $properties['expires_at'] = trim((string) ($properties['expires_at'] ?? '')) ?: null;
        $properties['is_active'] = filter_var($properties['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('coupon');

        return [
            'code' => ['required', 'string', 'max:40', Rule::unique('coupons', 'code')->ignore($id)->whereNull('deleted_at')],
            'value' => ['required', 'numeric', 'min:0'],
            'type' => ['required', Rule::in([Coupon::TYPE_FIXED, Coupon::TYPE_PERCENT])],
            'min_spend' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [
            'value.max' => __('A percentage discount cannot exceed 100%.'),
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'code' => __('code'),
            'type' => __('discount type'),
            'value' => __('value'),
            'min_spend' => __('minimum spend'),
            'max_discount' => __('maximum discount'),
            'usage_limit' => __('usage limit'),
            'starts_at' => __('starts'),
            'expires_at' => __('expires'),
            'is_active' => __('active'),
        ];
    }
}
