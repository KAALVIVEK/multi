<?php
include_once __DIR__ . '/config.php';

if (!APP_DEBUG) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

echo "Health Check (debug mode)\n";
echo str_repeat('=', 40) . "\n\n";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "Timezone: " . date_default_timezone_get() . "\n";
echo "Now: " . date('c') . "\n\n";

// Check DB
global $pdo;
if ($pdo) {
    try {
        $stmt = $pdo->query('SELECT 1');
        $ok = $stmt ? 'OK' : 'FAIL';
        echo "DB Connection: $ok\n";
    } catch (Throwable $e) {
        echo "DB Connection: FAIL - " . $e->getMessage() . "\n";
    }
} else {
    echo "DB Connection: not configured\n";
}

// Basic session check
echo "Session status: " . session_status() . " (" . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE') . ")\n";
echo "APP_DEBUG: " . (APP_DEBUG ? '1' : '0') . "\n";

// Key PHP ini flags
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";

// Filesystem permissions sanity for current dir
$writable = is_writable(__DIR__) ? 'writable' : 'not writable';
echo "Workspace dir is $writable\n";

echo "\nDone.\n";
?>

