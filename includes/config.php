<?php
// Global configuration and secure defaults.

declare(strict_types=1);

// Security headers (set in each request).
const APP_NAME = 'SnapIt';
const APP_URL = 'https://yourdomain.com';
const APP_ENV = 'production';

// Database configuration.
const DB_HOST = 'localhost';
const DB_NAME = 'snapit';
const DB_USER = 'snapit_user';
const DB_PASS = 'change_me';
const DB_CHARSET = 'utf8mb4';

// SMTP configuration (PHPMailer).
const SMTP_HOST = 'smtp.yourprovider.com';
const SMTP_PORT = 587;
const SMTP_USER = 'no-reply@yourdomain.com';
const SMTP_PASS = 'change_me';
const SMTP_FROM = 'no-reply@yourdomain.com';
const SMTP_FROM_NAME = 'SnapIt Notifications';

// Session settings.
const SESSION_NAME = 'snapit_session';
const SESSION_LIFETIME = 0; // Session cookie.
const SESSION_SECURE = true; // Set true in production with HTTPS.
const SESSION_HTTP_ONLY = true;
const SESSION_SAMESITE = 'Lax';

// CSRF token name.
const CSRF_TOKEN_KEY = '_csrf_token';

// Timezone.
date_default_timezone_set('UTC');
