<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * HasFactory for domain models: resolves Database\Factories\{Model}Factory by class
 * basename, since models live in App\Domains\{Domain}\Models rather than App\Models.
 */
trait InteractsWithModelFactory
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    /** @return Factory<static>|null */
    protected static function newFactory(): ?Factory
    {
        $factory = 'Database\\Factories\\'.class_basename(static::class).'Factory';

        return class_exists($factory) ? $factory::new() : null;
    }
}
