<?php
// CSRF token generation and validation.

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION[CSRF_TOKEN_KEY])) {
        $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_KEY];
}

function csrf_validate(?string $token): bool
{
    start_secure_session();
    if (!$token || empty($_SESSION[CSRF_TOKEN_KEY])) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
}
