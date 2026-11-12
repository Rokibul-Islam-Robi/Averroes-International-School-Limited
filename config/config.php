<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Global Configuration & Environment Settings
 */

// Application root filesystem path
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Load environment variables from .env file if it exists
$envFile = APP_ROOT . DIRECTORY_SEPARATOR . '.env';
if (file_exists($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);
        // Strip surrounding quotes
        if (preg_match('/^"(.*)"$/', $val, $m) || preg_match("/^'(.*)'$/", $val, $m)) {
            $val = $m[1];
        }
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $val;
            putenv("$key=$val");
        }
    }
}

// Helper to read environment variables
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $val = $_ENV[$key] ?? getenv($key);
        if ($val === false || $val === null) {
            return $default;
        }
        switch (strtolower((string)$val)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        return $val;
    }
}

// Environment: 'development' or 'production'
if (!defined('APP_ENV')) {
    define('APP_ENV', env('APP_ENV', 'development'));
}

// Error reporting settings
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}
ini_set('log_errors', '1');

// Database Connection Credentials
$dbUrl = env('DATABASE_URL', env('MYSQL_URL'));
if (!empty($dbUrl) && is_string($dbUrl) && (str_starts_with($dbUrl, 'mysql://') || str_starts_with($dbUrl, 'mysql:'))) {
    $parsed = parse_url($dbUrl);
    if ($parsed !== false) {
        defined('DB_HOST') || define('DB_HOST', $parsed['host'] ?? '127.0.0.1');
        defined('DB_PORT') || define('DB_PORT', (string)($parsed['port'] ?? 3306));
        defined('DB_NAME') || define('DB_NAME', ltrim($parsed['path'] ?? 'averroes_db', '/'));
        defined('DB_USER') || define('DB_USER', $parsed['user'] ?? 'root');
        defined('DB_PASS') || define('DB_PASS', $parsed['pass'] ?? '');
    }
}

defined('DB_HOST')    || define('DB_HOST', (string)env('DB_HOST', '127.0.0.1'));
defined('DB_PORT')    || define('DB_PORT', (string)env('DB_PORT', '3306'));
defined('DB_NAME')    || define('DB_NAME', (string)env('DB_NAME', 'averroes_db'));
defined('DB_USER')    || define('DB_USER', (string)env('DB_USER', 'root'));
defined('DB_PASS')    || define('DB_PASS', (string)env('DB_PASS', ''));
defined('DB_CHARSET') || define('DB_CHARSET', (string)env('DB_CHARSET', 'utf8mb4'));

// Start session safely if not active
if (session_status() === PHP_SESSION_NONE) {
    if (php_sapi_name() !== 'cli') {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        @session_start();
    } else {
        @session_start();
    }
}

/**
 * Return the application's base URL path (e.g. '/averroes/' or '/')
 */
function base_url(string $path = ''): string {
    static $base = null;
    if ($base === null) {
        if (php_sapi_name() === 'cli') {
            $base = '/averroes';
        } else {
            $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/averroes/index.php');
            $dir = dirname($script);
            // If executing from inside admin/, includes/, api/ or scripts/, get project root URL
            if (in_array(basename($dir), ['admin', 'includes', 'api', 'scripts'], true)) {
                $dir = dirname($dir);
            }
            $base = ($dir === '/' || $dir === '\\' || $dir === '.') ? '' : rtrim($dir, '/');
        }
    }

    if ($path === '' || $path === '/') {
        return ($base === '' ? '/' : $base . '/');
    }

    // Anchor or external URL check
    if (str_starts_with($path, '#') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:')) {
        return $path;
    }

    return ($base === '' ? '/' : $base . '/') . ltrim($path, '/');
}

/**
 * Resolve asset URLs dynamically (CSS, JS, images, uploads)
 */
function asset_url(string $path = ''): string {
    if ($path === '') {
        return base_url('assets/');
    }

    // External URL check
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
        return $path;
    }

    // Strip leading 'assets/' if passed in
    $clean = preg_replace('#^/?assets/#i', '', $path);
    return base_url('assets/' . ltrim($clean, '/'));
}

/**
 * Admin URL helper
 */
function admin_url(string $path = ''): string {
    if ($path === '') {
        return base_url('admin/');
    }
    return base_url('admin/' . ltrim($path, '/'));
}
