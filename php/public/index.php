<?php

declare(strict_types=1);

// Always use simple autoloader (no Composer needed)
require_once __DIR__ . '/autoload.php';

// Load .env manually
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            putenv(trim($line));
        }
    }
}

use App\Api\LanguagesController;
use App\Api\StatsController;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    // Set response headers
    header('Content-Type: image/svg+xml');
    header('Cache-Control: public, max-age=1800');

    // Parse request URI - handle both /api/stats and /index.php/api/stats patterns
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH);
    // Remove index.php from path if present
    $path = preg_replace('#/index\.php#', '', $path);
    // Remove /php/public prefix if accessing via full path
    $path = preg_replace('#^/php/public#', '', $path);

    // Simple routing
    if ($path === '/api/stats') {
        $controller = new StatsController();
        echo $controller->handle($_GET);
    } elseif ($path === '/api/top-langs') {
        $controller = new LanguagesController();
        echo $controller->handle($_GET);
    } else {
        // 404 for other routes
        http_response_code(404);
        header('Content-Type: text/plain');
        echo "Not Found. Available endpoints:\n";
        echo "  /api/stats?username=YOUR_GITHUB_USERNAME\n";
        echo "  /api/top-langs?username=YOUR_GITHUB_USERNAME\n";
        echo "\nOptional: &theme=dark";
    }
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Required: PHP 8.1+\n";
}
