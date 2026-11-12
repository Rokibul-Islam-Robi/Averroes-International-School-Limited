<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Navigation & Menu Items Data Repository
 */

if (!function_exists('get_topbar_items')) {
    /**
     * Get topbar items by side ('left' or 'right')
     */
    function get_topbar_items(?PDO $pdo, string $side): array {
        $fallback = [
            'left' => [
                ['text' => 'Lalmatia: admin@aisl.edu.bd', 'has_icon' => 0],
            ],
            'right' => [
                ['text' => 'Lalmatia : +880 1954-123 123', 'has_icon' => 1],
                ['text' => 'Banasree : +88 01901-048844', 'has_icon' => 1],
            ],
        ];

        if ($pdo === null) {
            return $fallback[$side] ?? [];
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM topbar_items WHERE side = :side AND is_active = 1 ORDER BY sort_order ASC, id ASC");
            $stmt->execute(['side' => $side]);
            $rows = $stmt->fetchAll();
            return !empty($rows) ? $rows : ($fallback[$side] ?? []);
        } catch (Throwable $e) {
            error_log('[get_topbar_items] ' . $e->getMessage());
            return $fallback[$side] ?? [];
        }
    }
}

if (!function_exists('get_nav_items')) {
    /**
     * Get navigation items by location
     */
    function get_nav_items(?PDO $pdo, string $location): array {
        $fallback = [
            'header' => [
                ['label' => 'First in Bangladesh', 'url' => '#', 'has_dropdown' => 1, 'css_class' => null],
                ['label' => 'Why Averroes', 'url' => 'why-averroes.php', 'has_dropdown' => 0, 'css_class' => null],
                ['label' => 'Life at Averroes', 'url' => 'life-at-averroes.php', 'has_dropdown' => 0, 'css_class' => null],
                ['label' => 'Admissions', 'url' => 'admission.php', 'has_dropdown' => 1, 'css_class' => null],
                ['label' => 'Contact Us', 'url' => 'contact.php', 'has_dropdown' => 0, 'css_class' => null],
            ],
            'header_btn' => [
                ['label' => 'Apply Now', 'url' => 'admission.php', 'css_class' => 'pri'],
                ['label' => 'Student Portal', 'url' => '#', 'css_class' => 'out'],
            ],
            'footer' => [
                ['label' => 'Home', 'url' => 'index.php'],
                ['label' => 'Why Averroes', 'url' => 'why-averroes.php'],
                ['label' => 'Apply at Averroes Lalmatia', 'url' => 'admission.php?campus=lalmatia'],
                ['label' => 'Averroes Portal', 'url' => '#'],
                ['label' => 'Contact Us', 'url' => 'contact.php'],
                ['label' => 'Life at Averroes', 'url' => 'life-at-averroes.php'],
                ['label' => 'Apply at Averroes Banasree', 'url' => 'admission.php?campus=banasree'],
            ],
            'legal' => [
                ['label' => 'Terms & Conditions', 'url' => '#'],
                ['label' => 'Privacy Policy', 'url' => '#'],
            ],
        ];

        if ($pdo === null) {
            return $fallback[$location] ?? [];
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM nav_items WHERE location = :loc AND is_active = 1 ORDER BY sort_order ASC, id ASC");
            $stmt->execute(['loc' => $location]);
            $rows = $stmt->fetchAll();
            if (!empty($rows)) {
                foreach ($rows as &$item) {
                    if (trim((string)($item['label'] ?? '')) === 'Why Averroes' || ($item['url'] ?? '') === '#why') {
                        $item['url'] = 'why-averroes.php';
                    } elseif (trim((string)($item['label'] ?? '')) === 'Life at Averroes' || ($item['url'] ?? '') === '#life' || ($item['url'] ?? '') === '#') {
                        if (trim((string)($item['label'] ?? '')) === 'Life at Averroes') {
                            $item['url'] = 'life-at-averroes.php';
                        }
                    }
                }
                unset($item);
                return $rows;
            }
            return $fallback[$location] ?? [];
        } catch (Throwable $e) {
            error_log('[get_nav_items] ' . $e->getMessage());
            return $fallback[$location] ?? [];
        }
    }
}
