<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

// Dumps every table's columns (after running all migrations on in-memory SQLite) to schema.json,
// so modgen.py knows e.g. which tables already have deleted_at. Run: scripts/t.sh php scripts/modgen/schema.php
// Force an isolated in-memory database and cache before Laravel boots (the app defaults to pgsql).
foreach (['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'DB_URL' => '', 'CACHE_STORE' => 'array'] as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $_SERVER[$key] = $value;
}
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
Artisan::call('migrate', ['--force' => true]);
$out = [];
foreach (Schema::getTables() as $t) {
    $out[$t['name']] = array_map(
        fn ($c) => $c['name'].':'.$c['type_name'].($c['nullable'] ? '?' : ''),
        Schema::getColumns($t['name']),
    );
}
file_put_contents(__DIR__.'/schema.json', json_encode($out, JSON_PRETTY_PRINT));
echo count($out)." tables\n";
