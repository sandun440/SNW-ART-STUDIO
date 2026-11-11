<?php
declare(strict_types=1);

/**
 * Lightweight .env parser to support Environment Variables
 * Works without composer or external packages.
 */
function load_env(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

function env(string $key, mixed $default = null): mixed {
    $value = getenv($key);
    if ($value === false) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    return $value;
}

// Load environment variables if .env exists
load_env(__DIR__ . '/../.env');

// Detect environment (Localhost Windows vs InfinityFree Linux Production)
$isLocalhost = (PHP_OS_FAMILY === 'Windows');

// Dynamically detect scheme (HTTP or HTTPS) to prevent mixed-content blocks on mobile
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
           || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
           || (isset($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'));

$scheme = $isHttps ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? ($isLocalhost ? 'localhost' : 'snwartstudio.infinityfree.io');

if ($isLocalhost) {
    // Localhost Environment Settings (XAMPP Windows - supports localhost or local LAN IP like 192.168.x.x)
    define('APP_NAME', 'SNW ART STUDIO');
    define('BASE_URL', $scheme . $host . '/SNW%20ART%20STUDIO');
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'snw_art_studio');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // InfinityFree Linux Server Settings (dynamically matches scheme https:// or http://)
    define('APP_NAME', 'SNW ART STUDIO');
    define('BASE_URL', $scheme . $host);
    define('DB_HOST', 'sql310.infinityfree.com');
    define('DB_NAME', 'if0_43405173_snw_art_studio');
    define('DB_USER', 'if0_43405173');
    define('DB_PASS', 'tACT23omLuVKBk4');
}

date_default_timezone_set('Asia/Colombo');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

/**
 * Retrieve a studio configuration setting from database (studio_settings table)
 * with graceful fallback to .env or code default.
 */
function studio_setting(string $key, ?string $default = null): string {
    static $settingsCache = null;
    if ($settingsCache === null) {
        $settingsCache = [];
        try {
            $pdo = db();
            // Automatically ensure settings table exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS studio_settings (
                setting_key VARCHAR(64) PRIMARY KEY,
                setting_value TEXT NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB");

            $rows = $pdo->query("SELECT setting_key, setting_value FROM studio_settings")->fetchAll();
            foreach ($rows as $r) {
                $settingsCache[$r['setting_key']] = $r['setting_value'];
            }
        } catch (Throwable) {
            // DB not ready or offline
        }
    }
    return (!empty($settingsCache[$key])) ? $settingsCache[$key] : ($default ?? '');
}

// Studio Contact & Settings (from Database, fallback to .env / defaults)
define('WHATSAPP_NUMBER', (string)studio_setting('whatsapp_number', (string)env('WHATSAPP_NUMBER', '94765237415')));
define('STUDIO_EMAIL', (string)studio_setting('studio_email', (string)env('STUDIO_EMAIL', 'studio@snwartstudio.com')));
define('STUDIO_HOURS', (string)studio_setting('studio_hours', (string)env('STUDIO_HOURS', 'Monday — Saturday: 09:00 AM — 06:00 PM (IST)')));
define('CURRENCY_SYMBOL', (string)env('CURRENCY_SYMBOL', 'Rs.'));

// Studio Bank Account Details (from Database, fallback to .env / defaults)
define('BANK_NAME', (string)studio_setting('bank_name', (string)env('BANK_NAME', 'Commercial Bank of Ceylon')));
define('BANK_ACCOUNT_NAME', (string)studio_setting('bank_account_name', (string)env('BANK_ACCOUNT_NAME', 'SNW ART STUDIO')));
define('BANK_ACCOUNT_NUMBER', (string)studio_setting('bank_account_number', (string)env('BANK_ACCOUNT_NUMBER', '8001234567')));
define('BANK_BRANCH', (string)studio_setting('bank_branch', (string)env('BANK_BRANCH', 'Colombo Main Branch')));
define('BANK_SWIFT', (string)studio_setting('bank_swift', (string)env('BANK_SWIFT', 'CCEYLKKL')));
