<?php

namespace App\Domains\Content\Resources;

use App\Domains\Content\Models\ChatEscalation;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class ChatEscalationResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $guestName,
        public ?string $guestEmail,
        public string $status,
        public ?string $statusLabel,
        public ?string $adminNotes,
        public string $customerName,
        public array $messages,
        public int $messagesCount,
        public ?string $lastMessage,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(ChatEscalation $chatEscalation, array $context = []): self
    {
        return new self(
            id: $chatEscalation->id,
            guestName: $chatEscalation->guest_name,
            guestEmail: $chatEscalation->guest_email,
            status: (string) $chatEscalation->status,
            statusLabel: self::option(['open' => 'Open', 'in_progress' => 'In progress', 'resolved' => 'Resolved'], $chatEscalation->status),
            adminNotes: $chatEscalation->admin_notes,
            customerName: $chatEscalation->user->name ?? $chatEscalation->guest_name ?? 'Guest',
            messages: array_values(array_map(fn ($chatEscalationsg) => ['role' => (string) ($chatEscalationsg['role'] ?? 'user'), 'content' => (string) ($chatEscalationsg['content'] ?? ''), 'time' => $chatEscalationsg['time'] ?? null], (array) ($chatEscalation->conversation ?? []))),
            messagesCount: count((array) ($chatEscalation->conversation ?? [])),
            lastMessage: collect((array) ($chatEscalation->conversation ?? []))->last()['content'] ?? null,
            createdAt: $chatEscalation->created_at?->toIso8601String(),
            updatedAt: $chatEscalation->updated_at?->toIso8601String(),
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
