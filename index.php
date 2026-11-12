<?php
declare(strict_types=1);

/**
 * Averroes International School
 * Dynamic Homepage
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Database connection
$pdo = get_db(false);

// Load dynamic data
$settings     = get_all_settings($pdo);
$topbar_left  = get_topbar_items($pdo, 'left');
$topbar_right = get_topbar_items($pdo, 'right');
$header_nav   = get_nav_items($pdo, 'header');
$header_btns  = get_nav_items($pdo, 'header_btn');
$footer_nav   = get_nav_items($pdo, 'footer');
$legal_nav    = get_nav_items($pdo, 'legal');

$sec_why      = get_section($pdo, 'why');
$sec_first    = get_section($pdo, 'first');
$sec_camp     = get_section($pdo, 'campuses');

$stats        = get_stats($pdo);
$programs     = get_programs($pdo);
$campuses     = get_campuses($pdo, false);
$camp_grid    = get_campuses($pdo, true);
$partners     = get_partners($pdo);

// Render Header
require_once __DIR__ . '/includes/header.php';
?>

    <section class="hero" aria-label="Averroes Hero Video" data-aos="fade-in" data-aos-duration="900">
        <?= render_hero_video($settings['hero_video_url'] ?? 'https://youtu.be/lWlHTIiGMvk') ?>
    </section>

    <section id="why" class="why wrap">
        <div class="why-txt" data-aos="fade-right" data-aos-duration="750">
            <h2 class="h"><img class="cap" src="<?= asset_url($sec_why['icon_image'] ?? 'img/cap.svg') ?>" alt=""><?= e($sec_why['heading_plain'] ?? 'Why ') ?><em><?= e(rtrim($sec_why['heading_em'] ?? 'Averroes', '?')) ?>?</em></h2>
            <p><?= nl2br(e($sec_why['body'] ?? '')) ?></p>
            <?php if (!empty($sec_why['button_text'])): ?>
                <a href="<?= base_url('why-averroes.php') ?>" class="btn out lg"><?= e($sec_why['button_text']) ?></a>
            <?php endif; ?>
        </div>
        <?php if (!empty($sec_why['image'])): ?>
            <img class="rocket" src="<?= asset_url($sec_why['image']) ?>" alt="<?= e($sec_why['image_alt'] ?? 'Pencil rocket illustration') ?>" data-aos="fade-left" data-aos-duration="750" data-aos-delay="150">
        <?php endif; ?>
    </section>

    <section class="stats wrap">
        <?php foreach ($stats as $sIdx => $st): ?>
            <div data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?= ($sIdx % 4) * 100 ?>">
                <?php if ($st['count_to'] !== null && $st['count_to'] !== ''): ?>
                    <b data-n="<?= e((string)$st['count_to']) ?>" data-s="<?= e((string)($st['suffix'] ?? '')) ?>"><?= e((string)$st['display_value']) ?></b>
                <?php else: ?>
                    <b><?= e((string)$st['display_value']) ?></b>
                <?php endif; ?>
                <span><?= e((string)$st['label']) ?></span>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="first wrap">
        <h2 class="h" data-aos="fade-up" data-aos-duration="650"><img class="cap" src="<?= asset_url($sec_first['icon_image'] ?? 'img/cap.svg') ?>" alt=""><?= e(rtrim($sec_first['heading_plain'] ?? 'First Time In')) ?><br><em><?= e($sec_first['heading_em'] ?? 'Bangladesh') ?></em></h2>
        <div class="slider" data-aos="fade-up" data-aos-duration="700" data-aos-delay="150">
            <div class="track" id="track">
                <?php foreach ($programs as $pIdx => $prog): ?>
                    <article class="card">
                        <div class="ic"><img src="<?= asset_url($prog['icon']) ?>" alt="<?= e($prog['title']) ?>"></div>
                        <h3><?= e($prog['title']) ?></h3>
                        <p><?= nl2br(e($prog['summary'])) ?></p>
                        <a href="<?= e(base_url($prog['link_url'] ?? '#')) ?>"><?= e($prog['link_text'] ?? 'Learn More') ?></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="dots" id="dots"></div>
    </section>

    <section class="camp wrap">
        <h2 class="h" data-aos="fade-up" data-aos-duration="650"><img class="cap" src="<?= asset_url($sec_camp['icon_image'] ?? 'img/cap.svg') ?>" alt=""><?= e(rtrim($sec_camp['heading_plain'] ?? 'Discover Our')) ?><br><em><?= e($sec_camp['heading_em'] ?? 'Campuses') ?></em></h2>
        <div class="camp-grid">
            <?php foreach ($camp_grid as $cgIdx => $cg): ?>
                <a href="<?= e(base_url($cg['card_link'] ?? '#')) ?>" class="cc" data-aos="zoom-in" data-aos-duration="650" data-aos-delay="<?= ($cgIdx % 3) * 120 ?>">
                    <img src="<?= asset_url($cg['image'] ?? 'img/Campus-1.jpg') ?>" alt="<?= e($cg['image_alt'] ?? $cg['name']) ?>">
                    <span><?= e($cg['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="partners wrap" aria-label="Partners & Affiliations" data-aos="fade-up" data-aos-duration="700">
        <div class="partners-marquee" id="partnersMarquee">
            <div class="partners-track" id="partnersTrack">
                <?php foreach ($partners as $partner): ?>
                    <div class="partner-slide <?= e($partner['css_class']) ?>">
                        <?= $partner['label_html'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php
// Render Footer
require_once __DIR__ . '/includes/footer.php';
