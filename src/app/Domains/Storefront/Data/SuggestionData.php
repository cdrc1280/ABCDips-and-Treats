<?php

namespace App\Domains\Storefront\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class SuggestionData extends Data
{
    public const CATEGORIES = ['product_idea', 'service_feedback', 'feature_request', 'other'];

    public function __construct(
        public string $name,
        public string $email,
        public string $category,
        public string $subject,
        public string $message,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['name', 'email', 'subject', 'message'] as $key) {
            $properties[$key] = trim(strip_tags((string) ($properties[$key] ?? ''))) ?: null;
        }

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['required', 'in:'.implode(',', self::CATEGORIES)],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
