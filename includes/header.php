<?php
declare(strict_types=1);

/**
 * Averroes International School
 * Frontend Header Component
 */

if (!defined('APP_ROOT')) {
    exit('Direct access not permitted.');
}

// Ensure required data arrays are available
$settings     = $settings     ?? get_all_settings($pdo ?? null);
$topbar_left  = $topbar_left  ?? get_topbar_items($pdo ?? null, 'left');
$topbar_right = $topbar_right ?? get_topbar_items($pdo ?? null, 'right');
$header_nav   = $header_nav   ?? get_nav_items($pdo ?? null, 'header');
$header_btns  = $header_btns  ?? get_nav_items($pdo ?? null, 'header_btn');
$programs     = $programs     ?? get_programs($pdo ?? null);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e(!empty($custom_page_title) ? $custom_page_title . ' | ' . ($settings['site_name'] ?? 'Averroes International School') : ($settings['site_title'] ?? 'Averroes International School | School of Life')) ?></title>
    <meta name="description" content="<?= e($custom_meta_description ?? ($settings['meta_description'] ?? '')) ?>">
    <link rel="icon" type="image/x-icon" href="<?= asset_url($settings['favicon'] ?? 'img/logo.jpeg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700;800&family=Kalam:wght@400;700&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?: time() ?>">
</head>

<body>
    <div class="topbar">
        <div class="topbar-mob">
            <span>Lalmatia: +880 1954-123 123 &nbsp;|&nbsp; Banasree: +88 01901-048844</span>
        </div>
        <div class="topbar-in">
            <div class="topbar-left">
                <?php foreach ($topbar_left as $item): ?>
                    <span><?= e($item['text']) ?></span>
                <?php endforeach; ?>
            </div>
            <div class="topbar-right hot">
                <?php foreach ($topbar_right as $item): ?>
                    <span>
                        <?php if (!empty($item['has_icon'])): ?>
                            <svg class="phone-ico" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" style="vertical-align: -1px; margin-right: 5px; opacity: 0.95;">
                                <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.02-.24 11.72 11.72 0 003.68.59 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1 11.72 11.72 0 00.59 3.68 1 1 0 01-.24 1.02l-2.23 2.09z"/>
                            </svg>
                        <?php endif; ?>
                        <?= e($item['text']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Off-Canvas Backdrop -->
    <div id="drawerOverlay" class="drawer-overlay" aria-hidden="true"></div>

    <!-- Off-Canvas Right Drawer -->
    <aside id="sideDrawer" class="side-drawer" aria-label="Site Navigation Drawer" aria-hidden="true">
        <div class="drawer-header">
            <button id="drawerClose" class="drawer-close" aria-label="Close Navigation">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="drawer-content">
            <a href="<?= base_url() ?>" class="drawer-home-link">Home</a>

            <div class="drawer-group">
                <h4 class="drawer-group-title">DISCOVER OUR CAMPUSES</h4>
                <ul class="drawer-sublinks">
                    <li><a href="<?= base_url('admission.php?campus=lalmatia') ?>">Averroes International School, Lalmatia</a></li>
                    <li><a href="<?= base_url('admission.php?campus=banasree') ?>">Averroes International School, Banasree</a></li>
                </ul>
            </div>

            <div class="drawer-group">
                <h4 class="drawer-group-title">LATEST UPDATES</h4>
                <ul class="drawer-sublinks">
                    <li><a href="<?= base_url('admission.php?campus=lalmatia') ?>">Lalmatia Admissions 2026–2027</a></li>
                    <li><a href="<?= base_url('admission.php?campus=banasree') ?>">Banasree Admissions 2026–2027</a></li>
                </ul>
            </div>

            <div class="drawer-group">
                <h4 class="drawer-group-title">QUICK LINKS</h4>
                <ul class="drawer-sublinks">
                    <li><a href="<?= base_url('why-averroes.php') ?>">Why Averroes</a></li>
                    <li><a href="<?= base_url('life-at-averroes.php') ?>">Life at Averroes</a></li>
                    <li><a href="<?= base_url('admission.php') ?>">Admissions</a></li>
                    <li><a href="<?= base_url('contact.php') ?>">Contact Us</a></li>
                </ul>
            </div>

            <div class="drawer-btns">
                <?php foreach ($header_btns as $btn): ?>
                    <a class="btn <?= e($btn['css_class'] ?? 'pri') ?>" href="<?= e(base_url($btn['url'])) ?>"><?= e($btn['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>

    <header class="hdr">
        <div class="hdr-in">
            <a href="<?= base_url() ?>" class="logo"><img src="<?= asset_url($settings['logo'] ?? 'img/logo-main.jpg') ?>" alt="<?= e($settings['site_title'] ?? 'Averroes International School') ?>"></a>
            <nav id="nav" class="nav">
                <?php foreach ($header_nav as $nav): ?>
                    <?php
                    $navLabel  = trim((string)($nav['label'] ?? ''));
                    $targetUrl = $nav['url'] ?? '#';
                    $hasDropdown = !empty($nav['has_dropdown']);

                    if ($navLabel === 'Why Averroes' || $targetUrl === '#why') {
                        $targetUrl = 'why-averroes.php';
                    } elseif ($navLabel === 'Life at Averroes' || $targetUrl === '#life') {
                        $targetUrl = 'life-at-averroes.php';
                    } elseif (stripos($navLabel, 'admission') !== false) {
                        $targetUrl = 'admission.php';
                        $hasDropdown = true;
                    } elseif (stripos($navLabel, 'contact') !== false || $targetUrl === '#footer') {
                        $targetUrl = 'contact.php';
                    }
                    ?>
                    <?php if ($hasDropdown): ?>
                        <div class="nav-item has-dd">
                            <a href="<?= e(base_url($targetUrl)) ?>" class="dd"><?= e($nav['label']) ?> <i></i></a>
                            <div class="dropdown-menu<?= (stripos($nav['label'], 'first') !== false) ? ' dd-grid-2' : '' ?>">
                                <?php if (stripos($nav['label'], 'first') !== false): ?>
                                    <?php foreach ($programs as $prog): ?>
                                        <a href="<?= e(base_url($prog['link_url'] ?? '#')) ?>"><?= e($prog['title']) ?></a>
                                    <?php endforeach; ?>
                                <?php elseif (stripos($nav['label'], 'admission') !== false): ?>
                                    <a href="<?= e(base_url('admission.php?campus=lalmatia')) ?>">Averroes Lalmatia</a>
                                    <a href="<?= e(base_url('admission.php?campus=banasree')) ?>">Averroes Banasree</a>
                                <?php else: ?>
                                    <a href="<?= e(base_url('#')) ?>"><?= e($nav['label']) ?> Overview</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="nav-item">
                            <a href="<?= e(base_url($targetUrl)) ?>"><?= e($nav['label']) ?></a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="mob-btns">
                    <?php foreach ($header_btns as $btn): ?>
                        <a class="btn <?= e($btn['css_class'] ?? 'pri') ?>" href="<?= e(base_url($btn['url'])) ?>"><?= e($btn['label']) ?></a>
                    <?php endforeach; ?>
                </div>
            </nav>
            <button id="burger" class="burger" aria-label="Menu" aria-expanded="false"><b></b><b></b><b></b></button>
            <div class="hbtns">
                <?php foreach ($header_btns as $btn): ?>
                    <a class="btn <?= e($btn['css_class'] ?? 'pri') ?>" href="<?= e(base_url($btn['url'])) ?>"><?= e($btn['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </header>
