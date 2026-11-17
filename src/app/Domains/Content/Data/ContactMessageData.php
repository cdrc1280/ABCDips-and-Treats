<?php

namespace App\Domains\Content\Data;

use App\Rules\PhilippinePhone;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class ContactMessageData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $subject,
        public string $message,
        public ?string $phone = null,
        public ?string $status = 'unread',
        public ?string $adminNotes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['email'] = strtolower(trim((string) ($properties['email'] ?? ''))) ?: null;
        $properties['subject'] = trim((string) ($properties['subject'] ?? '')) ?: null;
        $properties['message'] = trim((string) ($properties['message'] ?? '')) ?: null;
        $properties['phone'] = trim((string) ($properties['phone'] ?? '')) ?: null;
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'unread';
        $properties['admin_notes'] = trim((string) ($properties['admin_notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('contactMessage');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', new PhilippinePhone],
            'status' => ['required', Rule::in(['unread', 'read', 'replied', 'archived'])],
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
            'name' => __('name'),
            'email' => __('email'),
            'phone' => __('phone'),
            'subject' => __('subject'),
            'message' => __('message'),
            'status' => __('status'),
            'admin_notes' => __('internal notes'),
        ];
    }
}
