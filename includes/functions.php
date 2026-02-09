<?php
// Shared utility functions.

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => SESSION_SECURE,
            'httponly' => SESSION_HTTP_ONLY,
            'samesite' => SESSION_SAMESITE,
        ]);
        session_start();
    }
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_user_id(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function require_login(): void
{
    if (!current_user_id()) {
        redirect('/auth/login.php');
    }
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect('/admin/login.php');
    }
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}
