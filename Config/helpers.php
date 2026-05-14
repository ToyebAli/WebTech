<?php
function redirect(string $path): void {
    header("Location: $path");
    exit;
}

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function session_guard(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) redirect('/login');
}

function require_admin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        redirect('/login');
    }
}

function json_response(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $submitted = $_POST['csrf_token'] ?? '';
    $expected  = $_SESSION['csrf_token'] ?? '';
    if (!$submitted || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        die('Security check failed. Go back and try again.');
    }
}

function flash(string $key, ?string $msg = null): ?string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return null; }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}