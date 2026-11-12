<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Settings Data Repository
 */

if (!function_exists('get_all_settings')) {
    /**
     * Get all site settings
     */
    function get_all_settings(?PDO $pdo): array {
        $defaults = [
            'site_title'        => 'Averroes International School | One School Serving Here & Hereafter',
            'meta_description'  => 'Averroes International School (Lalmatia & Banasree) is an award-winning Islamic English-medium institution integrating British curriculum with Hifzul Quran and modern STEM education.',
            'favicon'           => 'assets/img/logo.jpeg',
            'logo'              => 'assets/img/logo-main.jpg',
            'logo_link'         => '#',
            'hero_video_url'    => 'https://youtu.be/lWlHTIiGMvk',
            'footer_logo'       => 'assets/img/Footer logo.png',
            'copyright_text'    => '© 2026 Averroes International School. All Rights Reserved.',
            'whatsapp_url'      => 'https://wa.me/8801954123123',
            'chat_url'          => '#',
            'chat_text'         => 'Chat with Us',
        ];

        if ($pdo === null) {
            return $defaults;
        }

        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $defaults[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $e) {
            error_log('[get_all_settings] ' . $e->getMessage());
        }

        return $defaults;
    }
}
