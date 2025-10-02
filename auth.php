<?php
include_once 'config.php';

function login_user($username, $password) {
    global $pdo;

    $username = sanitize_input($username);

    if (!$pdo) {
        return false;
    }
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
    if (!$pdo) {
        // Provide a minimal session-derived user object to avoid notices in views
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'user',
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? 'user',
            'wallet_balance' => 0.00
        ];
    }
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, role, wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if (!$user) {
            // If the user is not found in DB but session exists, log out and force re-login
            logout_user();
            redirect('login.php');
        }
        return $user;
    } catch (PDOException $e) {
        error_log('get_current_user query failed: ' . $e->getMessage());
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'user',
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? 'user',
            'wallet_balance' => 0.00
        ];
    }
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
