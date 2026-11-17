<?php

namespace App\Domains\Content\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class ChatEscalationData extends Data
{
    public function __construct(
        public ?string $guestName = null,
        public ?string $guestEmail = null,
        public ?string $status = 'open',
        public ?string $adminNotes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['guest_name'] = trim((string) ($properties['guest_name'] ?? '')) ?: null;
        $properties['guest_email'] = strtolower(trim((string) ($properties['guest_email'] ?? ''))) ?: null;
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'open';
        $properties['admin_notes'] = trim((string) ($properties['admin_notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('chatEscalation');

        return [
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved'])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
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
            'guest_name' => __('customer name'),
            'guest_email' => __('customer email'),
            'status' => __('status'),
            'admin_notes' => __('internal notes'),
        ];
    }
}
