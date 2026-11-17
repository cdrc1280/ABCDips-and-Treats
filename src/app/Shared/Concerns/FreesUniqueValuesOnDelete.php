<?php

namespace App\Shared\Concerns;

use Illuminate\Support\Facades\Schema;

/**
 * For soft-deleted models whose columns have a DB unique index (slug, sku, code…):
 * on soft delete the values get a "~deleted-{key tail}" suffix, so the original value can be
 * reused immediately — which is exactly what `Rule::unique(...)->whereNull('deleted_at')`
 * promises. The original value stays in the activity log.
 *
 * The suffix uses the last 12 hex digits of the row's UUID (the random part of a v7 UUID), and
 * the value is trimmed so the result always fits the column (e.g. employee_number is varchar(32)).
 *
 * Declare `protected array $uniqueOnDelete = ['slug'];` on the model; it must also use SoftDeletes.
 */
trait FreesUniqueValuesOnDelete
{
    public static function bootFreesUniqueValuesOnDelete(): void
    {
        static::deleting(function (self $model) {
            if ($model->isForceDeleting()) {
                return;
            }

            $suffix = '~deleted-'.substr(str_replace('-', '', (string) $model->getKey()), -12);

            foreach ($model->uniqueOnDelete as $column) {
                $value = (string) $model->getAttribute($column);
                if ($value !== '' && ! str_contains($value, '~deleted-')) {
                    $room = max(1, self::columnLength($model->getTable(), $column) - mb_strlen($suffix));
                    $model->forceFill([$column => mb_substr($value, 0, $room).$suffix])->saveQuietly();
                }
            }
        });
    }

    /** Declared length of a string column (255 when unknown, e.g. text columns). */
    private static function columnLength(string $table, string $column): int
    {
        /** @var array<string, array<string, int>> $cache */
        static $cache = [];

        if (! isset($cache[$table])) {
            $cache[$table] = [];
            foreach (Schema::getColumns($table) as $info) {
                if (preg_match('/\((\d+)\)/', (string) $info['type'], $m)) {
                    $cache[$table][$info['name']] = (int) $m[1];
                }
            }
        }

        return $cache[$table][$column] ?? 255;
    }
}
