<?php
// Authentication helpers for users and admins.

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function login_user(int $user_id): void
{
    start_secure_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_last_active'] = time();
}

function logout_user(): void
{
    start_secure_session();
    $_SESSION = [];
    session_destroy();
}

function login_admin(int $admin_id): void
{
    start_secure_session();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_last_active'] = time();
}
