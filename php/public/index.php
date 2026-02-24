<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Api\StatsController;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Set response headers
header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=1800');

// Parse request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Simple routing
if ($path === '/api/stats') {
    $controller = new StatsController();
    echo $controller->handle($_GET);
} else {
    // 404 for other routes
    http_response_code(404);
    header('Content-Type: text/plain');
    echo "Not Found. Try /api/stats?username=YOUR_GITHUB_USERNAME";
}
