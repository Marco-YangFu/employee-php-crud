<?php

// --- CSRF ---
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars(csrf_token(),ENT_QUOTES, 'UTF-8').'">';
}

function verify_csrf(string $token): void {
    $ok = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    if (!$ok) {
        http_response_code(400);
        exit('Bad CSRF token');
    }
}

$dsn = 'mysql:host=localhost;dbname=testdb;charset=utf8mb4';
$user = 'dev';
$pass = 'marco';

try {
    $pdo = new PDO($dsn, $user, $pass,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit('DB Connection failed:' . $e->getMessage());
}

function e(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path = '/'): void {
    header("Location: {$path}");
    exit;
}