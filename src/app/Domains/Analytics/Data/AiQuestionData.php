<?php

namespace App\Domains\Analytics\Data;

use Spatie\LaravelData\Data;

final class AiQuestionData extends Data
{
    public function __construct(
        public string $prompt,
        public string $category = 'general',
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        // Plain text only — markup in the prompt is stripped before it reaches the model.
        $properties['prompt'] = trim(strip_tags((string) ($properties['prompt'] ?? '')));
        $properties['category'] = (string) ($properties['category'] ?? 'general') ?: 'general';

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:2', 'max:2000'],
            'category' => ['in:general,inventory,orders,sales,customers,products'],
        ];
    }
}
