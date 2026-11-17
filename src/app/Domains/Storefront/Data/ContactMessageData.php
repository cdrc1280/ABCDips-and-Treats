<?php

namespace App\Domains\Storefront\Data;

use App\Rules\PhilippinePhone;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class ContactMessageData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $subject,
        public string $message,
        public ?string $phone = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['name', 'email', 'phone', 'subject', 'message'] as $key) {
            $properties[$key] = trim(strip_tags((string) ($properties[$key] ?? ''))) ?: null;
        }

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', new PhilippinePhone],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }
}
