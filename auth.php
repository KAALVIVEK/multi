<?php
include_once 'config.php';

function login_user($username, $password) {
    global $pdo;

    $username = sanitize_input($username);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'] ?? ''; 
        return $user;
    }
    return false;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'owner']);
}

function get_user_role() {
    return $_SESSION['role'] ?? 'guest';
}

function logout_user() {
    session_unset();
    session_destroy();
}

function get_current_user() {
    if (!is_logged_in()) {
        return false;
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, username, email, role, wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function secure_page($admin_only = false) {
    if (!is_logged_in()) {
        redirect('login.php');
    }
    if ($admin_only && !is_admin()) {
        redirect('dashboard.php?error=unauthorized');
    }
}
?>
