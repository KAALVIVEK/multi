<?php
// ====================================================================
// AUTH PANEL CONFIGURATION
// --- REPLACE THESE VALUES WITH YOUR HOSTINGER CREDENTIALS ---
// ====================================================================

$host = 'localhost';          // On Hostinger, this is usually 'localhost'
$db_name = '[YOUR_HOSTINGER_DB_NAME]'; // e.g., u123456789_authdb
$db_user = '[YOUR_HOSTINGER_DB_USER]'; // e.g., u123456789_user
$db_pass = '[YOUR_HOSTINGER_DB_PASSWORD]';// The password you set for the user

// --- CORE SETTINGS ---
define('SITE_URL', 'https://yourdomain.com/'); // UPDATE THIS TO YOUR LIVE DOMAIN
define('SECRET_KEY', 'Your_Strong_Random_Secret_Key_For_Sessions_And_Hashing'); 

// --- DATABASE CONNECTION (PDO) ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// --- SESSION MANAGEMENT & HELPERS ---
session_start();

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: " . SITE_URL . $path);
    exit();
}
?>
