<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Ensure a valid 32-byte APP_KEY exists so Laravel's encrypter never throws 500
$currentAppKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? ($_SERVER['APP_KEY'] ?? null));
if (empty($currentAppKey) || strlen(trim($currentAppKey)) < 32) {
    $fallbackKey = 'base64:XG88lO2Pfq+eW/bX7jK8/yJ6Y9kQ2Lz5kM3qE8+G9xI=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// 2. Serverless safe defaults (Stateless cookie session & memory cache to prevent unmigrated DB crashes)
if (empty(getenv('SESSION_DRIVER')) || getenv('SESSION_DRIVER') === 'database') {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
}

if (empty(getenv('CACHE_STORE')) || getenv('CACHE_STORE') === 'database') {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
    $_SERVER['CACHE_STORE'] = 'array';
}

// 3. Setup writable /tmp paths for serverless execution (Vercel Lambda is read-only)
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
        @chmod($sqlitePath, 0666);
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

    // Set storage path to writable /tmp
    $app->useStoragePath('/tmp/storage');

    $request = Request::capture();
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request);

    // Diagnostics mode for debugging on Vercel
    if ($response->getStatusCode() === 500 && ($request->has('debug') || getenv('APP_DEBUG') === 'true')) {
        if (isset($response->exception) && $response->exception) {
            $e = $response->exception;
            header('Content-Type: text/html; charset=utf-8');
            http_response_code(500);
            echo "<h1>500 Serverless Exception Diagnostic</h1>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>Class:</strong> " . get_class($e) . "</p>";
            echo "<p><strong>Location:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
            echo "<pre style='background:#18181b;color:#f4f4f5;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            exit;
        }
    }

    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    error_log('Vercel Fatal Serverless Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo "<h1>500 Internal Server Error (Vercel Serverless)</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre style='background:#18181b;color:#f4f4f5;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}
