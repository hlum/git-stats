<?php
// Detailed diagnostic script

header('Content-Type: text/plain');

echo "=== Server Diagnostics ===\n\n";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Required: PHP 8.1+\n\n";

echo "=== File Checks ===\n";
$files = [
    '../src/Api/StatsController.php',
    '../src/Api/LanguagesController.php', 
    '../src/Types/CardColors.php',
    '../src/Utils/ColorParser.php',
    '../src/Utils/HttpClient.php',
    '../.env',
    './autoload.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path) ? 'OK' : 'MISSING';
    echo "$file: $exists\n";
}

echo "\n=== Loading .env ===\n";
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo ".env found, loading...\n";
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            putenv(trim($line));
            list($key) = explode('=', $line, 2);
            echo "  Set: " . trim($key) . "\n";
        }
    }
} else {
    echo ".env NOT FOUND\n";
}

echo "\nGITHUB_TOKEN set: " . (getenv('GITHUB_TOKEN') ? 'Yes (length: ' . strlen(getenv('GITHUB_TOKEN')) . ')' : 'No') . "\n";

echo "\n=== Testing Autoloader ===\n";
try {
    require_once __DIR__ . '/autoload.php';
    echo "Autoloader: OK\n";
    
    $classes = [
        'App\\Types\\CardColors',
        'App\\Types\\RenderOptions',
        'App\\Utils\\ColorParser',
        'App\\Utils\\HttpClient',
        'App\\Api\\StatsController',
    ];
    
    foreach ($classes as $class) {
        if (class_exists($class)) {
            echo "$class: OK\n";
        } else {
            echo "$class: FAILED\n";
        }
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Testing Controller ===\n";
try {
    $controller = new \App\Api\StatsController();
    echo "StatsController created: OK\n";
    
    // Try to call handle with a test username
    echo "Calling handle(['username' => 'octocat'])...\n";
    $result = $controller->handle(['username' => 'octocat']);
    echo "Result length: " . strlen($result) . " bytes\n";
    echo "Contains SVG: " . (strpos($result, '<svg') !== false ? 'Yes' : 'No') . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
