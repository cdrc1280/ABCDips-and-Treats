<?php

namespace App\Shared\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Activity log defaults for every model: log changed attributes only, skip empty logs,
 * never write hidden/encrypted attributes (passwords, tokens, secrets).
 */
trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logFillable()
            ->logExcept(array_merge($this->getHidden(), $this->encryptedAttributes(), ['updated_at', 'created_at', 'remember_token', 'password']))
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName(str(class_basename(static::class))->snake()->toString());
    }

    /**
     * @return list<string>
     */
    private function encryptedAttributes(): array
    {
        return array_keys(array_filter(
            $this->getCasts(),
            fn (string $cast) => str_starts_with($cast, 'encrypted'),
        ));
    }
}
