<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Core Authentication & Security Layer
 */

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
require_once __DIR__ . '/uploader.php';

// Acquire database PDO connection
$pdo = get_db(false);

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if current user is logged in
 */
function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id']);
}

/**
 * Require admin authentication
 */
function require_admin_auth(): void {
    if (!is_admin_logged_in()) {
        $loginUrl = base_url('admin/login.php');
        header("Location: $loginUrl");
        exit;
    }
}

/**
 * Generate or get current CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf_token'];
}

/**
 * Verify submitted CSRF token
 */
function verify_csrf(?string $token): bool {
    if (empty($token) || empty($_SESSION['admin_csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['admin_csrf_token'], $token);
}

/**
 * Set flash message (success, error, info, warning)
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash_msg'] = [
        'type' => $type,
        'message' => $message,
    ];
}

/**
 * Get and clear flash message
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash_msg'])) {
        $msg = $_SESSION['flash_msg'];
        unset($_SESSION['flash_msg']);
        return $msg;
    }
    return null;
}

/**
 * Helper to check active nav link
 */
function is_active_nav(string $slug): string {
    $script = basename($_SERVER['PHP_SELF'], '.php');
    return ($script === $slug) ? 'active' : '';
}
