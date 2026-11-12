<?php
/**
 * Life at Averroes Subpage
 *
 * Implements Module 1 & Module 2 of the Glenrich-inspired alternating visual design system,
 * showcasing student daily routine, Tarbiyah, clubs, athletics, and pastoral care.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Database connection
$pdo = get_db(false);

$page_title       = "Life at Averroes - Averroes International School";
$page_description = "Explore vibrant student life, daily routine, Salah, sports, clubs, and pastoral care at Averroes International School.";

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
$life_data = get_life_at_averroes_data($pdo);
$hero      = $life_data['hero'] ?? [];
$sections  = $life_data['sections'] ?? [];

include __DIR__ . '/includes/header.php';
?>

<main id="main-content" class="life-averroes-page">
    <!-- Section 0: Hero Section -->
    <section class="life-hero-section" aria-labelledby="life-hero-heading">
        <div class="life-wrap">
            <div class="life-hero-grid">
                <div class="life-hero-text" data-aos="fade-right" data-aos-duration="750">
                    <span class="life-eyebrow"><?= e($hero['eyebrow'] ?? 'STUDENT LIFE AT AVERROES') ?></span>
                    <h1 id="life-hero-heading" class="life-hero-title"><?= e($hero['title'] ?? 'Every Day Inspires New Ideas, Passion, and Character') ?></h1>
                    <p class="life-hero-lead"><?= e($hero['lead'] ?? '') ?></p>

                    <?php if (!empty($hero['metrics'])): ?>
                        <div class="life-stat-grid">
                            <?php foreach ($hero['metrics'] as $mIdx => $metric): ?>
                                <div class="life-stat-box" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?= $mIdx * 80 ?>">
                                    <span class="life-stat-val"><?= e($metric['val']) ?></span>
                                    <span class="life-stat-lbl"><?= e($metric['lbl']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="life-hero-visual" data-aos="fade-left" data-aos-duration="750" data-aos-delay="100">
                    <div class="life-visual-card">
                        <!-- Hero Floating Badge -->
                        <div class="why-overlay-badge purple tl">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 14 14"></polyline>
                            </svg>
                            <span>Here &amp; Hereafter</span>
                        </div>

                        <!-- Floating VR Sensory & Star Doodles (Matching Glenrich Hero) -->
                        <div class="life-doodle life-doodle-tr" aria-hidden="true">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a9 9 0 0 0-14.8 0"></path>
                                <path d="M4.6 9a9 9 0 0 0 14.8 0"></path>
                            </svg>
                        </div>
                        <div class="life-doodle life-doodle-bl" aria-hidden="true">
                            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>

                        <img src="<?= asset_url($hero['image'] ?? 'img/subpages/aisl-student-life-hero.jpg') ?>"
                             alt="<?= e($hero['image_alt'] ?? 'Averroes Student Explorer') ?>"
                             class="life-card-img"
                             loading="eager">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sections 1 to 5: Alternating Zig-Zag Grid -->
    <section class="life-main-section" aria-label="Life at Averroes Modular Features">
        <div class="life-wrap">
            <?php foreach ($sections as $index => $section): ?>
                <?php
                $isReversed = !empty($section['reverse']);
                $doodleType = $section['doodle_type'] ?? 'general';
                $rowId      = 'section-' . (!empty($section['id']) ? $section['id'] : $index);
                ?>
                <div class="life-section-row <?= $isReversed ? 'life-reverse' : '' ?>" id="<?= e($rowId) ?>">
                    <!-- Text Column -->
                    <div class="life-col-text" data-aos="<?= $isReversed ? 'fade-left' : 'fade-right' ?>" data-aos-duration="700">
                        <!-- Floating Orange Mortarboard Cap Badge -->
                        <div class="life-cap-badge" aria-hidden="true">
                            <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="life-cap-img" width="82" height="58">
                        </div>

                        <!-- Two-Tone Section Title -->
                        <h2 class="life-section-title">
                            <?= e($section['title_base']) ?><em><?= e($section['title_highlight']) ?></em>
                        </h2>

                        <!-- Lead Narrative -->
                        <p class="life-lead-text"><?= e($section['lead']) ?></p>

                        <!-- Structured Bullet List -->
                        <?php if (!empty($section['bullets'])): ?>
                            <ul class="life-bullet-list">
                                <?php foreach ($section['bullets'] as $bIdx => $bullet): ?>
                                    <li class="life-bullet-item">
                                        <div class="life-bullet-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                        <div class="life-bullet-content">
                                            <h3 class="life-bullet-title"><?= e($bullet['title']) ?></h3>
                                            <p class="life-bullet-desc"><?= e($bullet['desc']) ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <!-- Visual Column with Themed Vector Doodles -->
                    <div class="life-col-visual" data-aos="<?= $isReversed ? 'fade-right' : 'fade-left' ?>" data-aos-duration="700" data-aos-delay="120">
                        <div class="life-visual-card">
                            <?php if ($doodleType === 'aura'): ?>
                                <!-- Section 1: Seated Student Circular Aura Doodle -->
                                <div class="why-overlay-badge orange tr">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 14 14"></polyline>
                                    </svg>
                                    <span>8 Daily Periods</span>
                                </div>
                                <div class="life-doodle life-doodle-tr" aria-hidden="true">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9" stroke-dasharray="3 3"></circle>
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                </div>
                                <div class="life-doodle life-doodle-bl" aria-hidden="true">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </div>

                            <?php elseif ($doodleType === 'motion'): ?>
                                <!-- Section 2: Movement & Athlete Velocity Lines -->
                                <div class="why-overlay-badge blue tl">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M2 12h20"></path>
                                    </svg>
                                    <span>Sports &amp; Athletics</span>
                                </div>
                                <div class="life-doodle life-doodle-tr" aria-hidden="true">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </div>
                                <div class="life-doodle life-doodle-bl" aria-hidden="true">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12c2.5-3 5-3 7.5 0s5 3 7.5 0 5-3 7 0"></path>
                                        <path d="M2 18c2.5-3 5-3 7.5 0s5 3 7.5 0 5-3 7 0"></path>
                                    </svg>
                                </div>

                            <?php elseif ($doodleType === 'bus'): ?>
                                <!-- Section 3: Beyond Classroom School Bus & Skyline -->
                                <div class="why-overlay-badge orange tr">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                    </svg>
                                    <span>GPS School Bus Fleet</span>
                                </div>
                                <div class="life-doodle life-doodle-tr" aria-hidden="true">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                        <polyline points="2 17 12 22 22 17"></polyline>
                                        <polyline points="2 12 12 17 22 12"></polyline>
                                    </svg>
                                </div>
                                <div class="life-doodle life-doodle-bl" aria-hidden="true">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 21h18M5 21V7l8-4v18M13 21V3l6 4v14"/>
                                    </svg>
                                </div>

                            <?php elseif ($doodleType === 'trophy'): ?>
                                <!-- Section 4: Achievement Golden Trophy & Podium -->
                                <div class="why-overlay-badge purple tl">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    <span>Championship Honors</span>
                                </div>
                                <div class="life-doodle life-doodle-tr" aria-hidden="true">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="6"></circle>
                                        <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
                                    </svg>
                                </div>
                                <div class="life-doodle life-doodle-bl" aria-hidden="true">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                        <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                        <path d="M4 22h16"></path>
                                        <path d="M10 14.66V17c0 .55-.45 1-1 1H7"></path>
                                        <path d="M14 14.66V17c0 .55.45 1 1 1h2"></path>
                                    </svg>
                                </div>

                            <?php elseif ($doodleType === 'jigsaw'): ?>
                                <!-- Section 5: Pastoral Care Nurturing Jigsaw Sprout -->
                                <div class="why-overlay-badge orange tr">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    <span>Tarbiyah &amp; Pastoral Care</span>
                                </div>
                                <div class="life-doodle life-doodle-tr" aria-hidden="true">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </div>
                                <div class="life-doodle life-doodle-bl" aria-hidden="true">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 21a9 9 0 0 0 9-9c0-4.97-4.03-9-9-9s-9 4.03-9 9a9 9 0 0 0 9 9z"></path>
                                        <path d="M12 7v5l3 3"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <img src="<?= asset_url($section['image']) ?>"
                                 alt="<?= e($section['image_alt'] ?? 'Life at Averroes Visual') ?>"
                                 class="life-card-img"
                                 loading="lazy">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <section class="life-cta-section" aria-label="Admissions & Campus Tour Call to Action">
        <div class="life-wrap">
            <div class="life-cta-card" data-aos="fade-up" data-aos-duration="700">
                <h2 class="life-cta-title">Experience Life at Averroes Firsthand</h2>
                <p class="life-cta-lead">We invite prospective parents and students to tour our campuses at Lalmatia, Banasree, or Mirpur. Witness our engaging classes, modern athletic facilities, and spiritually inspiring atmosphere in person.</p>
                <div class="life-cta-btns">
                    <a href="<?= base_url('#') ?>" class="life-btn-primary">Apply Online Now</a>
                    <a href="<?= base_url('#footer') ?>" class="life-btn-outline">Book a Campus Tour</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include __DIR__ . '/includes/footer.php';
