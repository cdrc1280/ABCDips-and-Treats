<?php

namespace App\Domains\Content\Resources;

use App\Domains\Content\Models\Suggestion;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class SuggestionResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $category,
        public ?string $categoryLabel,
        public string $subject,
        public string $message,
        public string $status,
        public ?string $statusLabel,
        public ?string $adminNotes,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Suggestion $suggestion, array $context = []): self
    {
        return new self(
            id: $suggestion->id,
            name: $suggestion->name,
            email: $suggestion->email,
            category: (string) $suggestion->category,
            categoryLabel: self::option(['product_idea' => 'Product idea', 'service_feedback' => 'Service feedback', 'feature_request' => 'Feature request', 'other' => 'Other'], $suggestion->category),
            subject: $suggestion->subject,
            message: $suggestion->message,
            status: (string) $suggestion->status,
            statusLabel: self::option(['new' => 'New', 'reviewing' => 'Reviewing', 'implemented' => 'Implemented', 'declined' => 'Declined'], $suggestion->status),
            adminNotes: $suggestion->admin_notes,
            createdAt: $suggestion->created_at?->toIso8601String(),
            updatedAt: $suggestion->updated_at?->toIso8601String(),
        );
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
