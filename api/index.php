<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Setup writable /tmp paths for serverless execution (Vercel Lambda is read-only)
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Ensure SQLite database exists in writable /tmp if using SQLite
if (getenv('DB_CONNECTION') === 'sqlite' || empty(getenv('DB_CONNECTION'))) {
    $sqlitePath = '/tmp/database.sqlite';
    if (!file_exists($sqlitePath)) {
        @touch($sqlitePath);
    }
    if (empty(getenv('DB_DATABASE'))) {
        putenv("DB_DATABASE={$sqlitePath}");
        $_ENV['DB_DATABASE'] = $sqlitePath;
        $_SERVER['DB_DATABASE'] = $sqlitePath;
    }
}

// Adjust script name and paths so Laravel routing matches root paths correctly
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
try {
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Override view compiled path and storage path for serverless environment
    $app->useStoragePath('/tmp/storage');

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // Output diagnostic error to stderr and HTTP response if debugging
    error_log('Vercel Serverless Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

    if (getenv('APP_DEBUG') === 'true' || getenv('APP_DEBUG') === '1' || (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true')) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo "<h1>500 Internal Server Error (Vercel Serverless)</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
        echo "<pre style='background:#f4f4f5;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        exit;
    }

    http_response_code(500);
    echo "<h1>500 Internal Server Error</h1><p>The server encountered an error. Check Vercel Function logs or set APP_DEBUG=true in Vercel Environment Variables to view detailed diagnostics.</p>";
    exit;
}
