<?php

// Prepare ephemeral /tmp storage directories required by Laravel in Vercel serverless environment
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || !empty(getenv('VERCEL'))) {
    $storageDirs = [
        '/tmp/storage/app',
        '/tmp/storage/app/public',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
    ];

    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
}

// Forward Vercel Serverless Function requests to Laravel entrypoint
require __DIR__ . '/../public/index.php';
