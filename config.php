<?php
// ====================================================================
// AUTH PANEL CONFIGURATION
// --- REPLACE THESE VALUES WITH YOUR HOSTINGER CREDENTIALS ---
// ====================================================================

$host = getenv('DB_HOST') ?: 'localhost'; // On Hostinger, this is usually 'localhost'
$db_name = getenv('DB_NAME') ?: '[YOUR_HOSTINGER_DB_NAME]'; // e.g., u123456789_authdb
$db_user = getenv('DB_USER') ?: '[YOUR_HOSTINGER_DB_USER]'; // e.g., u123456789_user
$db_pass = getenv('DB_PASS') ?: '[YOUR_HOSTINGER_DB_PASSWORD]'; // The password you set for the user

// --- CORE SETTINGS ---
define('SITE_URL', 'https://yourdomain.com/'); // Optional; redirects are domain-agnostic below
define('SECRET_KEY', 'Your_Strong_Random_Secret_Key_For_Sessions_And_Hashing'); 

// --- DATABASE CONNECTION (PDO) ---
// If placeholders are not replaced or env vars are missing, skip DB connection gracefully
$pdo = null;
$hasValidDbConfig = (
    !empty($db_name) && strpos($db_name, '[') === false &&
    !empty($db_user) && strpos($db_user, '[') === false
);

if ($hasValidDbConfig) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Don't crash the app on connection failure in production hosting
        error_log('Database connection failed: ' . $e->getMessage());
        $pdo = null;
    }
}

// --- SESSION MANAGEMENT & HELPERS ---
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    } else {
        // Best-effort fallback for older PHP versions
        session_set_cookie_params(0, '/; samesite=Lax', '', $isHttps, true);
    }
    session_start();
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    // Accept absolute URLs as-is
    if (preg_match('#^https?://#i', $path)) {
        $location = $path;
    } else {
        // If a leading slash was provided, use it as an absolute path on this host
        if (strpos($path, '/') === 0) {
            $location = $path;
        } else {
            // Build a path relative to the executing script directory (works in subfolders on Hostinger)
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            $location = ($base === '' || $base === '/') ? '/' . ltrim($path, '/') : $base . '/' . ltrim($path, '/');
        }
    }
    header('Location: ' . $location, true, 302);
    exit();
}
?>
