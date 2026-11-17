<?php

namespace App\Shared\Support;

use Illuminate\Database\Eloquent\Model;

/** The key of a route-bound model (or the raw route value), e.g. for unique()->ignore() in Data rules. */
final class RouteModel
{
    public static function key(string $parameter): int|string|null
    {
        $value = request()->route($parameter);

        return match (true) {
            $value instanceof Model => $value->getKey(),
            is_string($value) => $value,
            default => null,
        };
    }
}
