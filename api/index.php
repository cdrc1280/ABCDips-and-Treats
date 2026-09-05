<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 0. Fast-path: Serve static assets directly if routed to serverless function
$publicPath = realpath(__DIR__ . '/../public');
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($publicPath && !empty($requestUri) && $requestUri !== '/' && $requestUri !== '/index.php') {
    $staticFile = realpath($publicPath . $requestUri);
    // Security check: ensure path is strictly within public/ and is a regular file
    if ($staticFile && str_starts_with($staticFile, $publicPath) && is_file($staticFile)) {
        $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
        $mimes = [
            'css'   => 'text/css; charset=UTF-8',
            'js'    => 'application/javascript; charset=UTF-8',
            'mjs'   => 'application/javascript; charset=UTF-8',
            'json'  => 'application/json',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'svg'   => 'image/svg+xml',
            'webp'  => 'image/webp',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'eot'   => 'application/vnd.ms-fontobject',
            'pdf'   => 'application/pdf',
            'txt'   => 'text/plain',
        ];

        header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($staticFile));
        header('Cache-Control: public, max-age=31536000, immutable');
        header('X-Content-Type-Options: nosniff');
        readfile($staticFile);
        exit;
    }
}

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

// 4. Robust Database Setup for Serverless Environment
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? ($_SERVER['DB_CONNECTION'] ?? null));
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? null));

// Determine if an external cloud database (Supabase, Neon, PlanetScale, RDS, etc.) is configured
$hasExternalDb = !empty($dbHost) && $dbHost !== '127.0.0.1' && $dbHost !== 'localhost';

if (!$hasExternalDb && ($dbConn === 'sqlite' || empty($dbConn))) {
    $sqlitePath = '/tmp/database.sqlite';
    $sourceDb = realpath(__DIR__ . '/../database/database.sqlite');

    // Copy bundled pre-seeded database if /tmp/database.sqlite doesn't exist or is empty
    if (!file_exists($sqlitePath) || filesize($sqlitePath) === 0) {
        if ($sourceDb && file_exists($sourceDb) && filesize($sourceDb) > 0) {
            @copy($sourceDb, $sqlitePath);
        } else {
            @touch($sqlitePath);
        }
        @chmod($sqlitePath, 0666);
    }

    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    putenv("DB_DATABASE={$sqlitePath}");
    $_ENV['DB_DATABASE'] = $sqlitePath;
    $_SERVER['DB_DATABASE'] = $sqlitePath;
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
    if ($response->getStatusCode() === 500) {
        $isApi = str_starts_with($request->path(), 'api') || $request->wantsJson();
        if (isset($response->exception) && $response->exception) {
            $e = $response->exception;
            error_log('Vercel 500 Exception on ' . $request->path() . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            if ($request->has('debug') || getenv('APP_DEBUG') === 'true') {
                if ($isApi) {
                    header('Content-Type: application/json; charset=utf-8');
                    http_response_code(500);
                    echo json_encode([
                        'error'     => true,
                        'message'   => $e->getMessage(),
                        'exception' => get_class($e),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                        'trace'     => explode("\n", $e->getTraceAsString())
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    exit;
                }

                header('Content-Type: text/html; charset=utf-8');
                http_response_code(500);
                echo "<h1>500 Serverless Exception Diagnostic</h1>";
                echo "<p><strong>Path:</strong> " . htmlspecialchars($request->path()) . "</p>";
                echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>Class:</strong> " . get_class($e) . "</p>";
                echo "<p><strong>Location:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
                echo "<pre style='background:#18181b;color:#f4f4f5;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                exit;
            }
        }
    }

    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    error_log('Vercel Fatal Serverless Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

    http_response_code(500);
    $isApiRequest = isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '/api/');

    if ($isApiRequest) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error'     => true,
            'message'   => $e->getMessage(),
            'file'      => $e->getFile() . ':' . $e->getLine(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    header('Content-Type: text/html; charset=utf-8');
    echo "<h1>500 Internal Server Error (Vercel Serverless)</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre style='background:#18181b;color:#f4f4f5;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}
