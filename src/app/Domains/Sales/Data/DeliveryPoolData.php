<?php

namespace App\Domains\Sales\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class DeliveryPoolData extends Data
{
    public function __construct(
        public ?string $poolCode = null,
        public ?string $zoneName = null,
        public ?string $region = null,
        public ?string $province = null,
        public ?string $city = null,
        public ?string $barangay = null,
        public ?float $totalDeliveryFee = 0,
        public ?string $status = 'open',
        public array $allocations = [],
        public ?string $notes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['pool_code'] = strtoupper(trim((string) ($properties['pool_code'] ?? ''))) ?: null;
        $properties['zone_name'] = trim((string) ($properties['zone_name'] ?? '')) ?: null;
        $properties['region'] = trim((string) ($properties['region'] ?? '')) ?: null;
        $properties['province'] = trim((string) ($properties['province'] ?? '')) ?: null;
        $properties['city'] = trim((string) ($properties['city'] ?? '')) ?: null;
        $properties['barangay'] = trim((string) ($properties['barangay'] ?? '')) ?: null;
        $properties['total_delivery_fee'] = ($properties['total_delivery_fee'] ?? '') === '' ? 0 : (float) $properties['total_delivery_fee'];
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'open';
        $properties['allocations'] = array_values(array_filter((array) ($properties['allocations'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('deliveryPool');

        return [
            'pool_code' => ['nullable', 'string', 'max:40', Rule::unique('delivery_pools', 'pool_code')->ignore($id)->whereNull('deleted_at')],
            'zone_name' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'total_delivery_fee' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['open', 'building'])],
            'allocations' => ['array', 'max:100'],
            'allocations.*.order_id' => ['bail', 'required', 'uuid', Rule::exists('orders', 'id')],
            'allocations.*.assigned_fee' => ['required', 'numeric', 'min:0'],
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
            'pool_code' => __('pool code'),
            'zone_name' => __('zone name'),
            'region' => __('region'),
            'province' => __('province'),
            'city' => __('city'),
            'barangay' => __('barangay'),
            'total_delivery_fee' => __('total delivery fee'),
            'status' => __('status'),
            'allocations' => __('orders in this pool'),
            'notes' => __('notes'),
        ];
    }
}
