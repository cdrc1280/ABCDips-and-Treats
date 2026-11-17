<?php

namespace App\Domains\Audit\Resources;

use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Activitylog\Models\Activity;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class ActivityResource extends Resource
{
    /**
     * @param  list<array{field: string, old: mixed, new: mixed}>  $changes
     */
    public function __construct(
        public int $id,
        public ?string $logName,
        public ?string $event,
        public string $description,
        public ?string $subjectType,
        public ?string $subjectId,
        public ?string $causerName,
        public array $changes,
        public ?string $createdAt,
    ) {}

    public static function fromModel(Activity $activity): self
    {
        $properties = $activity->properties?->toArray() ?? [];
        $new = (array) ($properties['attributes'] ?? []);
        $old = (array) ($properties['old'] ?? []);
        $fields = array_values(array_unique([...array_keys($new), ...array_keys($old)]));

        return new self(
            id: $activity->id,
            logName: $activity->log_name,
            event: $activity->event,
            description: $activity->description,
            subjectType: $activity->subject_type ? class_basename(Relation::getMorphedModel($activity->subject_type) ?? $activity->subject_type) : null,
            subjectId: $activity->subject_id ? (string) $activity->subject_id : null,
            causerName: $activity->causer?->getAttribute('name'),
            changes: array_map(fn (string $field) => ['field' => $field, 'old' => $old[$field] ?? null, 'new' => $new[$field] ?? null], $fields),
            createdAt: $activity->created_at?->toIso8601String(),
        );
    }
}
