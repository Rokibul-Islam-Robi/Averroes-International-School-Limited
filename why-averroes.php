<?php
/**
 * Why Averroes Subpage
 *
 * Implements Module 1 & Module 2 of the Glenrich-inspired alternating visual design system,
 * showcasing Averroes International School's dual-track British & Hifzul Quran excellence.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Database connection
$pdo = get_db(false);

$page_title       = "Why Averroes - Averroes International School";
$page_description = "Discover why Averroes International School is the premier English-medium Islamic institution in Bangladesh, offering dual-track British curriculum and Hifzul Quran.";

// Navigation & Global Site Data
$settings     = get_all_settings($pdo);
$topbar_left  = get_topbar_items($pdo, 'left');
$topbar_right = get_topbar_items($pdo, 'right');
$header_nav   = get_nav_items($pdo, 'header');
$header_btns  = get_nav_items($pdo, 'header_btn');
$programs     = get_programs($pdo);
$footer_nav   = get_nav_items($pdo, 'footer');
$legal_nav    = get_nav_items($pdo, 'legal');

// Content Repository Data
$why_data = get_why_averroes_data($pdo);
$hero     = $why_data['hero'] ?? [];
$sections = $why_data['sections'] ?? [];

include __DIR__ . '/includes/header.php';
?>

<main id="main-content" class="why-averroes-page">
    <!-- Section 0: Hero Section -->
    <section class="why-hero-section" aria-labelledby="why-hero-heading">
        <div class="why-wrap">
            <div class="why-hero-grid">
                <div class="why-hero-text" data-aos="fade-right" data-aos-duration="750">
                    <span class="why-eyebrow"><?= e($hero['eyebrow'] ?? 'WHY CHOOSE AVERROES') ?></span>
                    <h1 id="why-hero-heading" class="why-hero-title"><?= e($hero['title'] ?? 'Give your Child the Averroes Edge') ?></h1>
                    <p class="why-hero-lead"><?= e($hero['lead'] ?? '') ?></p>

                    <?php if (!empty($hero['metrics'])): ?>
                        <div class="why-stat-grid">
                            <?php foreach ($hero['metrics'] as $mIdx => $metric): ?>
                                <div class="why-stat-box" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?= $mIdx * 80 ?>">
                                    <span class="why-stat-val"><?= e($metric['val']) ?></span>
                                    <span class="why-stat-lbl"><?= e($metric['lbl']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="why-hero-visual" data-aos="fade-left" data-aos-duration="750" data-aos-delay="100">
                    <div class="why-visual-card">
                        <!-- Hero Floating Badge -->
                        <div class="why-overlay-badge purple tl">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 14 14"></polyline>
                            </svg>
                            <span>Here &amp; Hereafter</span>
                        </div>

                        <!-- Floating Vector Doodles -->
                        <div class="why-doodle why-doodle-tr" aria-hidden="true">
                            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <div class="why-doodle why-doodle-bl" aria-hidden="true">
                            <img src="<?= asset_url('img/cap.svg') ?>" alt="" style="width: 44px; height: auto;">
                        </div>

                        <img src="<?= asset_url($hero['image'] ?? 'img/subpages/aisl-hifz-boy-full.jpg') ?>"
                             alt="<?= e($hero['image_alt'] ?? 'Averroes Student') ?>"
                             class="why-card-img"
                             loading="eager">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sections 1 to 5: Alternating Zig-Zag Grid -->
    <section class="why-main-section" aria-label="Why Averroes Modular Features">
        <div class="why-wrap">
            <?php foreach ($sections as $index => $section): ?>
                <?php
                $isReversed = !empty($section['reverse']);
                $doodleType = $section['doodle_type'] ?? 'general';
                $rowId      = 'section-' . (!empty($section['id']) ? $section['id'] : $index);
                ?>
                <div class="why-section-row <?= $isReversed ? 'why-reverse' : '' ?>" id="<?= e($rowId) ?>">
                    <!-- Text Column -->
                    <div class="why-col-text" data-aos="<?= $isReversed ? 'fade-left' : 'fade-right' ?>" data-aos-duration="700">
                        <!-- Floating Orange Mortarboard Cap Badge -->
                        <div class="why-cap-badge" aria-hidden="true">
                            <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="why-cap-img" width="82" height="58">
                        </div>

                        <!-- Two-Tone Section Title -->
                        <h2 class="why-section-title">
                            <?= e($section['title_base']) ?><em><?= e($section['title_highlight']) ?></em>
                        </h2>

                        <!-- Lead Narrative -->
                        <p class="why-lead-text"><?= e($section['lead']) ?></p>

                        <!-- Content Pattern A: 4 Mini-Feature Blocks (Pedagogy) -->
                        <?php if (($section['type'] ?? '') === 'features' && !empty($section['features'])): ?>
                            <div class="why-features-grid">
                                <?php foreach ($section['features'] as $fIdx => $feat): ?>
                                    <div class="why-feature-item">
                                        <h3 class="why-feature-title">
                                            <span class="why-feature-num-badge">0<?= $fIdx + 1 ?></span>
                                            <?= e($feat['title']) ?>
                                        </h3>
                                        <p class="why-feature-desc"><?= e($feat['desc']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <!-- Content Pattern B: Structured Bullet List (Merit, Universities, Sports) -->
                        <?php elseif (!empty($section['bullets'])): ?>
                            <ul class="why-bullet-list">
                                <?php foreach ($section['bullets'] as $bIdx => $bullet): ?>
                                    <li class="why-bullet-item">
                                        <div class="why-bullet-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                        <div class="why-bullet-content">
                                            <h3 class="why-bullet-title"><?= e($bullet['title']) ?></h3>
                                            <p class="why-bullet-desc"><?= e($bullet['desc']) ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <!-- Visual Column with Themed Vector Doodles -->
                    <div class="why-col-visual" data-aos="<?= $isReversed ? 'fade-right' : 'fade-left' ?>" data-aos-duration="700" data-aos-delay="120">
                        <?php if ($doodleType === 'globe'): ?>
                            <!-- Section 4 Signature: Glenrich "Top 100 Global University Placement" Cloud Card -->
                            <div class="why-univ-cloud-card">
                                <div class="why-doodle why-doodle-tr" aria-hidden="true">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                </div>
                                <div class="why-doodle why-doodle-bl" aria-hidden="true">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                </div>

                                <div class="why-univ-cap-icon">
                                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                    </svg>
                                </div>
                                <div class="why-univ-big-num">Top 100</div>
                                <div class="why-univ-big-lbl">Global University Placement</div>

                                <div class="why-univ-badges-cloud">
                                    <span class="why-univ-badge-pill">Oxford (UK)</span>
                                    <span class="why-univ-badge-pill">Cambridge (UK)</span>
                                    <span class="why-univ-badge-pill">Monash (AUS)</span>
                                    <span class="why-univ-badge-pill">Toronto (CAN)</span>
                                    <span class="why-univ-badge-pill">Imperial (UK)</span>
                                    <span class="why-univ-badge-pill">BUET</span>
                                    <span class="why-univ-badge-pill">IBA (DU)</span>
                                    <span class="why-univ-badge-pill">Medical Colleges</span>
                                </div>

                                <div class="why-univ-auditorium-preview">
                                    <img src="<?= asset_url('img/subpages/aisl-progression-auditorium.jpg') ?>"
                                         alt="Averroes Annual Convocation & University Placement"
                                         loading="lazy">
                                    <div class="why-univ-preview-caption">
                                        Averroes Annual Convocation &amp; University Placement
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- Standard Visual Card with Floating Doodles -->
                            <div class="why-visual-card">
                                <?php if ($doodleType === 'science'): ?>
                                    <!-- Science Doodles: Atomic Orbit & Flask -->
                                    <div class="why-overlay-badge orange tr">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 14 14"></polyline>
                                        </svg>
                                        <span>Cambridge &amp; Edexcel</span>
                                    </div>
                                    <div class="why-doodle why-doodle-tr" aria-hidden="true">
                                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M19.4 15a9 9 0 0 0-14.8 0"></path>
                                            <path d="M4.6 9a9 9 0 0 0 14.8 0"></path>
                                        </svg>
                                    </div>
                                    <div class="why-doodle why-doodle-bl" aria-hidden="true">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 2v7.31L4.69 19a2 2 0 0 0 1.71 3h11.2a2 2 0 0 0 1.71-3L14 9.31V2"></path>
                                            <line x1="8.5" y1="2" x2="15.5" y2="2"></line>
                                        </svg>
                                    </div>
                                <?php elseif ($doodleType === 'books'): ?>
                                    <!-- Books / Pedagogy Doodles -->
                                    <div class="why-overlay-badge purple tl">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                        <span>5.5h Daily Hifz</span>
                                    </div>
                                    <div class="why-doodle why-doodle-tr" aria-hidden="true">
                                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="why-doodle why-doodle-bl" aria-hidden="true">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="2" x2="12" y2="6"></line>
                                            <path d="M12 18v4"></path>
                                            <path d="M4.93 4.93l2.83 2.83"></path>
                                            <path d="M16.24 16.24l2.83 2.83"></path>
                                            <path d="M2 12h4"></path>
                                            <path d="M18 12h4"></path>
                                        </svg>
                                    </div>
                                <?php elseif ($doodleType === 'merit'): ?>
                                    <!-- Merit Doodles -->
                                    <div class="why-overlay-badge orange tr">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <span>Merit Scholarships</span>
                                    </div>
                                    <div class="why-doodle why-doodle-tr" aria-hidden="true">
                                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="8" r="6"></circle>
                                            <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
                                        </svg>
                                    </div>
                                    <div class="why-doodle why-doodle-bl" aria-hidden="true">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </div>
                                <?php elseif ($doodleType === 'sports'): ?>
                                    <!-- Sports Doodles -->
                                    <div class="why-overlay-badge blue tr">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M2 12h20"></path>
                                        </svg>
                                        <span>Turf &amp; Olympic Pool</span>
                                    </div>
                                    <div class="why-doodle why-doodle-tr" aria-hidden="true">
                                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                                            <path d="M2 12h20"></path>
                                        </svg>
                                    </div>
                                    <div class="why-doodle why-doodle-bl" aria-hidden="true">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12c2.5-3 5-3 7.5 0s5 3 7.5 0 5-3 7 0"></path>
                                            <path d="M2 18c2.5-3 5-3 7.5 0s5 3 7.5 0 5-3 7 0"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>

                                <img src="<?= asset_url($section['image']) ?>"
                                     alt="<?= e($section['image_alt'] ?? 'Averroes Feature Visual') ?>"
                                     class="why-card-img"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <section class="why-cta-section" aria-label="Admissions Call to Action">
        <div class="why-wrap">
            <div class="why-cta-card" data-aos="fade-up" data-aos-duration="700">
                <h2 class="why-cta-title">Begin your Child's Journey of Excellence</h2>
                <p class="why-cta-lead">Join Bangladesh's premier English-medium Islamic educational institution. Schedule a campus tour at our Lalmatia, Banasree, or Mirpur campuses, or begin your online application today.</p>
                <div class="why-cta-btns">
                    <a href="<?= base_url('#') ?>" class="why-btn-primary">Apply Online Now</a>
                    <a href="<?= base_url('#footer') ?>" class="why-btn-outline">Contact Admissions</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include __DIR__ . '/includes/footer.php';
