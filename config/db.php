<?php
/**
 * Grand Furniture Vizag — Database & Site Configuration
 * Update DB_HOST, DB_USER, DB_PASS, DB_NAME and SITE_URL
 * before deploying to Hostinger (or any other host).
 */

// ─── Database ───────────────────────────────────────────────
define('DB_HOST',    '127.0.0.1');
define('DB_USER',    'root');
define('DB_PASS',    'root');
define('DB_NAME',    'grand_furniture');    // e.g. u123456789_grand_furniture
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT',    8889);

// ─── Site ────────────────────────────────────────────────────
$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$httpHost  = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('SITE_URL',   $scheme . $httpHost);
define('ADMIN_URL',  '/admin');
define('SITE_NAME',  'Grand Furniture Vizag');

// ─── Uploads ─────────────────────────────────────────────────
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('UPLOAD_URL', '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);  // 5 MB
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// ─── Admin session name ──────────────────────────────────────
define('SESSION_NAME',          'gf_admin_session');
define('SESSION_LIFETIME',      7200);           // 2 hours
define('CSRF_TOKEN_NAME',       'gf_csrf_token');

// ─── Rate limiting ───────────────────────────────────────────
define('LOGIN_RATE_LIMIT',      5);              // attempts
define('LOGIN_RATE_WINDOW',     900);            // 15 minutes (seconds)

// ─── Timezone ────────────────────────────────────────────────
date_default_timezone_set('Asia/Kolkata');

// ─── Error reporting (set to 0 in production) ───────────────
define('DEBUG_MODE', false);
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', dirname(__DIR__) . '/logs/error.log');
}

// ─── Session hardening ───────────────────────────────────────
ini_set('session.cookie_httponly',  '1');
ini_set('session.cookie_secure',    '0');   // Set to '1' in production with HTTPS
ini_set('session.cookie_samesite',  'Strict');
ini_set('session.use_strict_mode',  '1');
ini_set('session.gc_maxlifetime',   (string) SESSION_LIFETIME);
session_name(SESSION_NAME);

// ─── PDO connection ──────────────────────────────────────────
try {
    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        ),
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
            PDO::MYSQL_ATTR_FOUND_ROWS   => true,
        ]
    );
} catch (PDOException $e) {
    error_log('DB Connection failed: ' . $e->getMessage());
    $pdo = null;
    // Don't die — allow graceful fallback with static data
}
