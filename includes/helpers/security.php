<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Security & Session Helpers (XSS, CSRF, Flash Messages)
 */

if (!function_exists('e')) {
    /**
     * Escape HTML output to prevent Cross-Site Scripting (XSS)
     */
    function e(?string $value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve CSRF token
     */
    function csrf_token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render a hidden CSRF token input
     */
    function csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    /**
     * Verify submitted CSRF token
     */
    function verify_csrf_token(?string $token): bool {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('set_flash')) {
    /**
     * Set flash alert message
     */
    function set_flash(string $type, string $message): void {
        $_SESSION['flash'] = [
            'type'    => $type, // 'success', 'danger', 'info', 'warning'
            'message' => $message,
        ];
    }
}

if (!function_exists('get_flash')) {
    /**
     * Get and clear flash alert message
     */
    function get_flash(): ?array {
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('render_flash')) {
    /**
     * Render flash alert banner if one exists
     */
    function render_flash(): string {
        $flash = get_flash();
        if (!$flash) {
            return '';
        }
        $cls = 'alert-' . ($flash['type'] === 'danger' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info'));
        return '<div class="alert ' . $cls . ' alert-dismissible" role="alert">' .
               e($flash['message']) .
               '<button type="button" class="btn-close" onclick="this.parentElement.remove();">&times;</button></div>';
    }
}
