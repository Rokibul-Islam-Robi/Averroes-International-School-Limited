<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Dynamic Subpage Template - Glenrich 1:1 Design System & Dual-Archetype Rhythm
 * Reference: https://glenrich.edu.bd/robotics-program/
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Database connection
$pdo = get_db(false);

// Fetch program slug or ID from request
$slug = trim($_GET['slug'] ?? '');
if ($slug === '' && php_sapi_name() === 'cli' && isset($argv[1])) {
    $slug = trim($argv[1]);
}
if ($slug === '' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $slug = (string)$_GET['id'];
}
if ($slug === '') {
    $slug = 'hifzul-quran-with-british-curriculum';
}

// Fetch program data
$program = get_program_by_slug($pdo, $slug);

// Handle page metadata
if (!$program) {
    http_response_code(404);
    $custom_page_title = 'Program Not Found';
    $custom_meta_description = 'The requested program could not be found.';
} else {
    $custom_page_title = $program['title'];
    $custom_meta_description = !empty($program['meta_description']) ? $program['meta_description'] : $program['summary'];
}

// Global site data
$settings     = get_all_settings($pdo);
$topbar_left  = get_topbar_items($pdo, 'left');
$topbar_right = get_topbar_items($pdo, 'right');
$header_nav   = get_nav_items($pdo, 'header');
$header_btns  = get_nav_items($pdo, 'header_btn');
$programs     = get_programs($pdo);
$footer_nav   = get_nav_items($pdo, 'footer');
$legal_nav    = get_nav_items($pdo, 'legal');

// Contextual authentic high-resolution Averroes International School images for Row 1 & Row 2
// Row 1: Real campus facility & action photography / program banners
// Row 2: Real Averroes student-centric portraits / active learners framed inside the rounded peach card
// All other subpages kept cleanly blank until user provides approved HD assets
$uniqueImageMap = [
    'hifzul-quran-with-british-curriculum' => [
        'row1' => 'assets/img/subpages/aisl-hifz-convocation-banner.jpg',
        'row2' => 'assets/img/subpages/aisl-hifz-student-felicitation.jpg',
    ],
    'qiraat-competition' => [
        'row1' => 'assets/img/subpages/aisl-qiraat-students-group.jpg',
        'row2' => 'assets/img/subpages/aisl-qiraat-stage-reciter.jpg',
    ],
    'seerah-exhibition' => [
        'row1' => 'assets/img/subpages/aisl-seerah-sealed-nectar.jpg',
        'row2' => 'assets/img/subpages/aisl-seerah-arabic-conversation.jpg',
    ],
    'averroes-swimming-club' => [
        'row1' => 'assets/img/subpages/aisl-swimming-pool-students.jpg',
        'row2' => 'assets/img/subpages/aisl-swimming-club-splash.jpg',
    ],
    'averroes-turf' => [
        'row1' => 'assets/img/subpages/aisl-turf-camp-squad.jpg',
        'row2' => 'assets/img/subpages/aisl-turf-green-arena.jpg',
    ],
    'national-international-university-progression' => [
        'row1' => 'assets/img/subpages/aisl-progression-auditorium.jpg',
        'row2' => 'assets/img/subpages/aisl-university-progression-demo.jpg',
    ],
    'summer-camp' => [
        'row1' => 'assets/img/subpages/aisl-summer-camp-banner-squad.jpg',
        'row2' => 'assets/img/subpages/aisl-summer-camp-coaching-lineup.jpg',
        'row2_full' => true,
    ],
    'guardians-waiting-lounge' => [
        'row1' => 'assets/img/subpages/aisl-lounge-interior-lighting.jpg',
        'row2' => 'assets/img/subpages/aisl-lounge-parents-meeting.jpg',
    ],
    'school-bus-service' => [
        'row1' => 'assets/img/subpages/aisl-bus-ribbon-cutting.jpg',
        'row2' => 'assets/img/subpages/aisl-bus-service-launched.jpg',
    ],
    'averroes-book-shop' => [
        'row1' => 'assets/img/subpages/aisl-bookshop-main-hall.jpg',
        'row2' => 'assets/img/subpages/aisl-bookshop-aisle-shelves.jpg',
    ],
];

$slugKey = !empty($program['slug']) ? $program['slug'] : '';
$imgRow1 = !empty($program['image_row1']) ? $program['image_row1'] : ($uniqueImageMap[$slugKey]['row1'] ?? null);
if (!empty($imgRow1) && !file_exists(__DIR__ . '/' . ltrim($imgRow1, '/'))) {
    $imgRow1 = $uniqueImageMap[$slugKey]['row1'] ?? null;
}

$imgRow2 = !empty($program['image_row2']) ? $program['image_row2'] : ($uniqueImageMap[$slugKey]['row2'] ?? null);
if (!empty($imgRow2) && !file_exists(__DIR__ . '/' . ltrim($imgRow2, '/'))) {
    $imgRow2 = $uniqueImageMap[$slugKey]['row2'] ?? null;
}
$isRow2Full = !empty($uniqueImageMap[$slugKey]['row2_full']);

// 1:1 Alternating Rhythm Pattern Map:
// Odd index programs: Pattern A (Robotics style: Action Left / Text Right, Copy Left / Card Right)
// Even index programs: Pattern B (Academic style: Text Left / Action Right, Card Left / Copy Right)
$patternMap = [
    'hifzul-quran-with-british-curriculum'          => 'pattern-a',
    'qiraat-competition'                           => 'pattern-b',
    'seerah-exhibition'                            => 'pattern-a',
    'averroes-swimming-club'                       => 'pattern-b',
    'averroes-turf'                                => 'pattern-a',
    'national-international-university-progression' => 'pattern-b',
    'summer-camp'                                  => 'pattern-a',
    'guardians-waiting-lounge'                     => 'pattern-b',
    'school-bus-service'                           => 'pattern-a',
    'averroes-book-shop'                           => 'pattern-b',
];
$layoutPattern = $patternMap[$slugKey] ?? 'pattern-a';

/**
 * 10 Thematic SVG Vector Doodles strictly customized to each signature offering
 */
function get_program_doodles(string $slug): array {
    $doodles = [
        'hifzul-quran-with-british-curriculum' => [
            'doodle1' => '<svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M6 6h10"/><path d="M6 10h7"/></svg>',
            'doodle2' => '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>',
            'doodle3' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="#f58220"><circle cx="12" cy="12" r="6"/></svg>',
        ],
        'qiraat-competition' => [
            'doodle1' => '<svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>',
            'doodle2' => '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 10v4"/><path d="M6 7v10"/><path d="M10 4v16"/><path d="M14 7v10"/><path d="M18 10v4"/></svg>',
            'doodle3' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        ],
        'seerah-exhibition' => [
            'doodle1' => '<svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8 6 4 9 4 14v8h16v-8c0-5-4-8-8-12z"/><path d="M9 22v-6a3 3 0 0 1 6 0v6"/></svg>',
            'doodle2' => '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17V5a2 2 0 0 0-2-2H4"/><path d="M8 21h12a2 2 0 0 0 2-2v-1a1 1 0 0 0-1-1H11a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1z"/><path d="M4 5v14a2 2 0 0 0 2 2h2"/></svg>',
            'doodle3' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="14" height="14" rx="1"/><rect x="5" y="5" width="14" height="14" rx="1" transform="rotate(45 12 12)"/></svg>',
        ],
        'averroes-swimming-club' => [
            'doodle1' => '<svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/><path d="M2 12c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/><path d="M2 18c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/></svg>',
            'doodle2' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="12" r="4"/><circle cx="17" cy="12" r="4"/><line x1="11" y1="12" x2="13" y2="12"/><path d="M3 12H1m22 0h-2"/></svg>',
            'doodle3' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="#0284c7"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>',
        ],
        'averroes-turf' => [
            'doodle1' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="12 7 15.5 9.5 14 13.5 10 13.5 8.5 9.5"/><line x1="12" y1="2" x2="12" y2="7"/><line x1="21.5" y1="9" x2="15.5" y2="9.5"/><line x1="18" y1="20" x2="14" y2="13.5"/><line x1="6" y1="20" x2="10" y2="13.5"/><line x1="2.5" y1="9" x2="8.5" y2="9.5"/></svg>',
            'doodle2' => '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h12"/><path d="M6 12h14"/><path d="M4 16h10"/></svg>',
            'doodle3' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="#16a34a"><circle cx="12" cy="12" r="6"/></svg>',
        ],
        'national-international-university-progression' => [
            'doodle1' => '<svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
            'doodle2' => '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
            'doodle3' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
        ],
        'summer-camp' => [
            'doodle1' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20"/><path d="M12 3L2 21"/><path d="M12 3l10 18"/><path d="M12 3v18"/><path d="M8 21l4-7 4 7"/></svg>',
            'doodle2' => '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>',
            'doodle3' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>',
        ],
        'guardians-waiting-lounge' => [
            'doodle1' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"/><path d="M3 11a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6H3v-6z"/><path d="M5 17v4"/><path d="M19 17v4"/></svg>',
            'doodle2' => '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>',
            'doodle3' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
        ],
        'school-bus-service' => [
            'doodle1' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16v1a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-1h6v1a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-1M4 16H2V6a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v10h-2M4 16h16"/><circle cx="7.5" cy="11.5" r="1.5"/><circle cx="16.5" cy="11.5" r="1.5"/></svg>',
            'doodle2' => '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="7" x2="15" y2="7"/><line x1="7" y1="12" x2="21" y2="12"/><line x1="2" y1="17" x2="13" y2="17"/></svg>',
            'doodle3' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
        ],
        'averroes-book-shop' => [
            'doodle1' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
            'doodle2' => '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="14" r="4"/><circle cx="18" cy="14" r="4"/><line x1="10" y1="14" x2="14" y2="14"/><path d="M2 14l2-6M22 14l-2-6"/></svg>',
            'doodle3' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15 9 22 12 15 15 12 22 9 15 2 12 9 9 12 2"/></svg>',
        ],
    ];

    return $doodles[$slug] ?? [
        'doodle1' => '<svg width="48" height="48" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="20" stroke="#f58220" stroke-width="3.5"/></svg>',
        'doodle2' => '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#25093b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
        'doodle3' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="#f58220"><circle cx="12" cy="12" r="6"/></svg>',
    ];
}

$doodles = get_program_doodles($slugKey);

// Render Header
require_once __DIR__ . '/includes/header.php';
?>

<?php if (!$program): ?>
    <!-- 404 View -->
    <main class="wrap" style="padding: 100px 24px; text-align: center;">
        <h1 class="h" style="padding-top: 0; font-size: 32px; margin-bottom: 16px;">Offering Not Found</h1>
        <p style="color: #666; margin-bottom: 24px;">The signature program you are looking for does not exist or has been moved.</p>
        <a href="<?= base_url() ?>" class="btn pri" style="display: inline-block;">Return to Homepage</a>
    </main>
<?php else: ?>

    <!-- Glenrich 1:1 Signature Alternating Sub-Page Layout -->
    <?php
    $isRow2Portrait = ($slugKey === 'hifzul-quran-with-british-curriculum' || strpos((string)$imgRow2, 'aisl-hifz-boy-full') !== false);
    ?>
    <section class="glen-program-section glen-<?= e($layoutPattern) ?> glen-prog-<?= e($slugKey) ?>">
        <div class="wrap">

            <?php if ($layoutPattern === 'pattern-b'): ?>
                <!-- =============================================================
                     PATTERN B (Academic Style: Lead Left / Image Right, Card Left / Copy Right)
                     ============================================================= -->

                <!-- ROW 1: Title & Left-Aligned Lead Copy Left, Visual Right -->
                <div class="glen-row-1">
                    <div class="glen-row1-content">
                        <h1 class="glen-prog-title"><?= e($program['title']) ?></h1>
                        <?php if (!empty($program['subtitle'])): ?>
                            <h2 class="glen-prog-subtitle"><?= e($program['subtitle']) ?></h2>
                        <?php endif; ?>
                        <div class="glen-lead-text">
                            <p><?= nl2br(e(!empty($program['lead_text']) ? $program['lead_text'] : $program['summary'])) ?></p>
                        </div>
                    </div>
                    <?php if (!empty($imgRow1)): ?>
                        <div class="glen-row1-img-wrap">
                            <img class="glen-row1-img" src="<?= base_url(e($imgRow1)) ?>" alt="<?= e($program['title']) ?> Feature Photo" loading="lazy">
                        </div>
                    <?php else: ?>
                        <div class="glen-row1-img-wrap glen-blank-slot"></div>
                    <?php endif; ?>
                </div>

                <!-- ROW 2: Rounded Peach Card Left, Narrative Copy Right -->
                <div class="glen-row-2">
                    <div class="glen-row2-card-wrap">
                        <?php if (!empty($imgRow2)): ?>
                            <div class="glen-doodle-card <?= $isRow2Full ? 'glen-card-full' : '' ?>">
                                <img class="glen-card-img <?= $isRow2Portrait ? 'glen-card-portrait-top' : '' ?>" src="<?= base_url(e($imgRow2)) ?>" alt="<?= e($program['title']) ?> Student" loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="glen-doodle-card glen-blank-slot"></div>
                        <?php endif; ?>
                    </div>
                    <div class="glen-row2-content">
                        <?= !empty($program['body_html']) ? $program['body_html'] : '<p>' . nl2br(e($program['summary'])) . '</p>' ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- =============================================================
                     PATTERN A (Robotics Style: Visual Left / Lead Right, Copy Left / Card Right)
                     ============================================================= -->

                <!-- ROW 1: Visual Left, Title & Right-Aligned Lead Copy Right -->
                <div class="glen-row-1">
                    <?php if (!empty($imgRow1)): ?>
                        <div class="glen-row1-img-wrap">
                            <img class="glen-row1-img" src="<?= base_url(e($imgRow1)) ?>" alt="<?= e($program['title']) ?> Feature Photo" loading="lazy">
                        </div>
                    <?php else: ?>
                        <div class="glen-row1-img-wrap glen-blank-slot"></div>
                    <?php endif; ?>
                    <div class="glen-row1-content">
                        <h1 class="glen-prog-title"><?= e($program['title']) ?></h1>
                        <?php if (!empty($program['subtitle'])): ?>
                            <h2 class="glen-prog-subtitle"><?= e($program['subtitle']) ?></h2>
                        <?php endif; ?>
                        <div class="glen-lead-text">
                            <p><?= nl2br(e(!empty($program['lead_text']) ? $program['lead_text'] : $program['summary'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- ROW 2: Narrative Copy Left, Rounded Peach Card Right -->
                <div class="glen-row-2">
                    <div class="glen-row2-content">
                        <?= !empty($program['body_html']) ? $program['body_html'] : '<p>' . nl2br(e($program['summary'])) . '</p>' ?>
                    </div>
                    <div class="glen-row2-card-wrap">
                        <?php if (!empty($imgRow2)): ?>
                            <div class="glen-doodle-card <?= $isRow2Full ? 'glen-card-full' : '' ?>">
                                <img class="glen-card-img <?= $isRow2Portrait ? 'glen-card-portrait-top' : '' ?>" src="<?= base_url(e($imgRow2)) ?>" alt="<?= e($program['title']) ?> Student" loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="glen-doodle-card glen-blank-slot"></div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>

<?php endif; ?>

<?php
// Render Footer
require_once __DIR__ . '/includes/footer.php';
