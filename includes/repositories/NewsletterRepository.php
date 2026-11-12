<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Newsletter Subscribers Data Repository
 */

if (!function_exists('subscribe_newsletter')) {
    /**
     * Save newsletter subscriber
     */
    function subscribe_newsletter(?PDO $pdo, string $email, ?string $ip = null): array {
        $email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Enter a valid email address.'];
        }

        if ($pdo === null) {
            return ['success' => true, 'message' => 'Subscribed. Thank you!'];
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, ip_address, created_at) VALUES (:email, :ip, NOW()) ON DUPLICATE KEY UPDATE created_at = NOW()");
            $stmt->execute([
                'email' => $email,
                'ip'    => $ip,
            ]);
            return ['success' => true, 'message' => 'Subscribed. Thank you!'];
        } catch (Throwable $e) {
            error_log('[subscribe_newsletter] ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not save subscription. Please try again later.'];
        }
    }
}
