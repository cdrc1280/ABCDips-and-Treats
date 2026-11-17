<?php

namespace App\Domains\Content\Resources;

use App\Domains\Content\Models\ContactMessage;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class ContactMessageResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public string $subject,
        public string $message,
        public string $status,
        public ?string $statusLabel,
        public ?string $adminNotes,
        public ?string $repliedAt,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(ContactMessage $contactMessage, array $context = []): self
    {
        return new self(
            id: $contactMessage->id,
            name: $contactMessage->name,
            email: $contactMessage->email,
            phone: $contactMessage->phone,
            subject: $contactMessage->subject,
            message: $contactMessage->message,
            status: (string) $contactMessage->status,
            statusLabel: self::option(['unread' => 'Unread', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived'], $contactMessage->status),
            adminNotes: $contactMessage->admin_notes,
            repliedAt: $contactMessage->replied_at?->toIso8601String(),
            createdAt: $contactMessage->created_at?->toIso8601String(),
            updatedAt: $contactMessage->updated_at?->toIso8601String(),
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
