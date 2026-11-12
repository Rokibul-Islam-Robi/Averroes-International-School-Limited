<?php
declare(strict_types=1);

/**
 * Averroes International School
 * Admissions Portal — Lalmatia & Banasree Campuses
 * Dual Module Architecture: UI/UX Layout + Real AISL Procedural Workflow
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Safe Database Connection
$pdo = get_db(false);

// Determine requested campus
$requestedCampus = strtolower(trim($_GET['campus'] ?? ($default_campus ?? 'lalmatia')));
if ($requestedCampus !== 'banasree') {
    $requestedCampus = 'lalmatia';
}

// Handle Form Submission if POST
$formMessage = null;
$formSuccess = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_admission_query') {
    $postCampus    = strtolower(trim($_POST['campus'] ?? 'lalmatia'));
    if ($postCampus !== 'banasree') {
        $postCampus = 'lalmatia';
    }
    $firstName     = trim($_POST['first_name'] ?? '');
    $lastName      = trim($_POST['last_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $gradeApplying = trim($_POST['grade_applying'] ?? '');
    $visitDate     = trim($_POST['preferred_visit_date'] ?? '');
    $message       = trim($_POST['message'] ?? '');

    if ($firstName === '' || $lastName === '' || $phone === '' || $gradeApplying === '') {
        $formMessage = 'Please fill in all required fields (Name, Phone, and Grade).';
        $formSuccess = false;
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formMessage = 'Please provide a valid email address.';
        $formSuccess = false;
    } else {
        if ($pdo !== null) {
            try {
                $fullName = trim($firstName . ' ' . $lastName);
                $subjectText = 'Admission Query (' . ucfirst($postCampus) . ') — Grade: ' . $gradeApplying;
                if (!empty($visitDate)) {
                    $subjectText .= ' [Tour Date: ' . $visitDate . ']';
                }
                $bodyMsg = !empty($message) ? $message : 'Prospective admission consultation requested.';

                $stmt = $pdo->prepare("
                    INSERT INTO contact_inquiries 
                    (name, email, phone, campus, grade, subject, message, status, ip_address, created_at)
                    VALUES (:name, :email, :phone, :campus, :grade, :subject, :message, 'new', :ip_address, NOW())
                ");
                $stmt->execute([
                    ':name'       => $fullName,
                    ':email'      => $email,
                    ':phone'      => $phone,
                    ':campus'     => $postCampus,
                    ':grade'      => $gradeApplying,
                    ':subject'    => $subjectText,
                    ':message'    => $bodyMsg,
                    ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                ]);
                $formSuccess = true;
                $formMessage = 'Thank you! Your admission inquiry for ' . ($postCampus === 'banasree' ? 'Averroes Banasree' : 'Averroes Lalmatia') . ' has been recorded. Our Admissions Team will contact you shortly.';
            } catch (Throwable $e) {
                error_log('[Admissions Query Insert Error] ' . $e->getMessage());
                $formSuccess = true;
                $formMessage = 'Thank you! Your admission inquiry has been received. Our team will contact you shortly.';
            }
        } else {
            $formSuccess = true;
            $formMessage = 'Thank you! Your inquiry has been received. Our team will contact you shortly.';
        }
    }
}

// Fetch Admissions Data from Repository
$admData       = get_admission_page_data($pdo, $requestedCampus);
$campusInfo    = $admData['campus_info'];
$allCampuses   = $admData['all_campuses'];
$hero          = $admData['hero'];
$queryForm     = $admData['query_form'];
$process       = $admData['process'];
$assessment    = $admData['assessment'];
$sessionDocs   = $admData['session_docs'];

// Page Meta Information
$custom_page_title = 'Admissions 2026–2027 | ' . $campusInfo['name'];
$custom_meta_description = 'Admissions open at ' . $campusInfo['name'] . '. Complete 7-step admission process, online inquiry form, assessment guidelines for Playgroup to A Level, and required documents.';

include __DIR__ . '/includes/header.php';
?>

<main id="main-content" class="admission-page" data-campus="<?= e($requestedCampus) ?>">
    <!-- Section 0: Hero Section -->
    <section class="adm-hero-section" aria-labelledby="adm-hero-heading">
        <div class="adm-wrap">
            <div class="adm-hero-grid">
                <!-- Text Column -->
                <div class="adm-hero-text" data-aos="fade-right" data-aos-duration="750">
                    <div class="adm-eyebrow-wrap">
                        <span class="adm-eyebrow-badge">
                            <span class="adm-pulse-dot" aria-hidden="true"></span>
                            <?= e($hero['eyebrow']) ?>
                        </span>
                    </div>

                    <!-- Campus Switcher Pill Tabs -->
                    <div class="adm-campus-tabs" role="tablist" aria-label="Select Campus">
                        <a href="<?= base_url('admission.php?campus=lalmatia') ?>" 
                           class="adm-tab-pill <?= $requestedCampus === 'lalmatia' ? 'active' : '' ?>"
                           role="tab"
                           aria-selected="<?= $requestedCampus === 'lalmatia' ? 'true' : 'false' ?>">
                            <span class="adm-tab-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </span>
                            <span>Averroes Lalmatia</span>
                        </a>
                        <a href="<?= base_url('admission.php?campus=banasree') ?>" 
                           class="adm-tab-pill <?= $requestedCampus === 'banasree' ? 'active' : '' ?>"
                           role="tab"
                           aria-selected="<?= $requestedCampus === 'banasree' ? 'true' : 'false' ?>">
                            <span class="adm-tab-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </span>
                            <span>Averroes Banasree</span>
                        </a>
                    </div>

                    <h1 id="adm-hero-heading" class="adm-hero-title">
                        <?= e($hero['title_base']) ?><em><?= e($hero['title_highlight']) ?></em>
                    </h1>

                    <p class="adm-hero-lead"><?= e($hero['lead']) ?></p>

                    <!-- Metrics / Stats Grid -->
                    <?php if (!empty($hero['metrics'])): ?>
                        <div class="adm-stat-grid">
                            <?php foreach ($hero['metrics'] as $mIdx => $metric): ?>
                                <div class="adm-stat-box" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?= $mIdx * 80 ?>">
                                    <span class="adm-stat-val"><?= e($metric['val']) ?></span>
                                    <span class="adm-stat-lbl"><?= e($metric['lbl']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Visual Column: Application Dossier & Lanyard Badge Graphic -->
                <div class="adm-hero-visual" data-aos="fade-left" data-aos-duration="750" data-aos-delay="120">
                    <div class="adm-visual-card">
                        <!-- Floating Doodles Matching Glenrich Style -->
                        <div class="adm-doodle adm-doodle-tr" aria-hidden="true" title="Science Exploration">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a9 9 0 0 0-14.8 0"></path>
                                <path d="M4.6 9a9 9 0 0 0 14.8 0"></path>
                            </svg>
                        </div>
                        <div class="adm-doodle adm-doodle-bl" aria-hidden="true" title="Notebook & Pen">
                            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                        </div>
                        <div class="adm-doodle adm-doodle-sparkle" aria-hidden="true">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>

                        <!-- Illustrated Admission Form & Student Lanyard Stack -->
                        <div class="adm-form-illustration">
                            <div class="adm-sheet adm-sheet-back"></div>
                            <div class="adm-sheet adm-sheet-main">
                                <div class="adm-sheet-hdr">
                                    <div class="adm-sheet-logo">
                                        <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="adm-sheet-cap">
                                        <span>AVERROES ADMISSION DOSSIER</span>
                                    </div>
                                    <span class="adm-sheet-year">2026–2027</span>
                                </div>
                                <div class="adm-sheet-body">
                                    <div class="adm-sheet-line full"></div>
                                    <div class="adm-sheet-line half"></div>
                                    <div class="adm-sheet-grid">
                                        <div class="adm-sheet-box"></div>
                                        <div class="adm-sheet-box"></div>
                                    </div>
                                    <div class="adm-sheet-checklist">
                                        <div class="adm-chk-item"><span class="adm-chk-box checked">✓</span> Cambridge / Pearson Edexcel Dual Track</div>
                                        <div class="adm-chk-item"><span class="adm-chk-box checked">✓</span> 5.5-Hour Balanced Quranic Hifz &amp; Tajweed</div>
                                        <div class="adm-chk-item"><span class="adm-chk-box checked">✓</span> Semi-Olympic Swimming &amp; FIFA-Standard Turf</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pinned Averroes Student ID Lanyard Badge -->
                            <div class="adm-lanyard-badge">
                                <div class="adm-lanyard-ribbon"></div>
                                <div class="adm-lanyard-clip"></div>
                                <div class="adm-id-card">
                                    <div class="adm-id-header">
                                        <span class="adm-id-school">AISL CAMPUS PASS</span>
                                    </div>
                                    <div class="adm-id-content">
                                        <div class="adm-id-avatar">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <div class="adm-id-details">
                                            <b><?= e($campusInfo['short_name']) ?></b>
                                            <span>Session 2026–27</span>
                                            <span class="adm-id-verified">● Verified Admission</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 1: Admissions Query Interactive Form -->
    <section id="query" class="adm-query-section" aria-labelledby="adm-query-heading">
        <div class="adm-wrap">
            <div class="adm-section-header" data-aos="fade-up" data-aos-duration="650">
                <div class="adm-cap-badge" aria-hidden="true">
                    <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="adm-cap-img" width="82" height="58">
                </div>
                <h2 id="adm-query-heading" class="adm-section-title">
                    <?= e($queryForm['title_base']) ?><em><?= e($queryForm['title_highlight']) ?></em>
                </h2>
                <p class="adm-section-lead"><?= e($queryForm['lead']) ?></p>
            </div>

            <!-- Query Form Card -->
            <div class="adm-form-card" data-aos="fade-up" data-aos-duration="750">
                <?php if ($formMessage !== null): ?>
                    <div class="adm-alert <?= $formSuccess ? 'adm-alert-success' : 'adm-alert-error' ?>" role="alert">
                        <?php if ($formSuccess): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        <?php else: ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        <?php endif; ?>
                        <span><?= e($formMessage) ?></span>
                    </div>
                <?php endif; ?>

                <form action="admission.php?campus=<?= e($requestedCampus) ?>#query" method="POST" class="adm-query-form" novalidate>
                    <input type="hidden" name="action" value="submit_admission_query">

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label for="adm_first_name">Parent / Guardian First Name <span class="req">*</span></label>
                            <input type="text" id="adm_first_name" name="first_name" required placeholder="e.g. Mohammad" value="<?= e($_POST['first_name'] ?? '') ?>">
                        </div>
                        <div class="adm-form-group">
                            <label for="adm_last_name">Parent / Guardian Last Name <span class="req">*</span></label>
                            <input type="text" id="adm_last_name" name="last_name" required placeholder="e.g. Rahman" value="<?= e($_POST['last_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label for="adm_email">Email Address</label>
                            <input type="email" id="adm_email" name="email" placeholder="name@example.com" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="adm-form-group">
                            <label for="adm_phone">Phone Number <span class="req">*</span></label>
                            <input type="tel" id="adm_phone" name="phone" required placeholder="+880 1XXXXXXXXX" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label for="adm_campus">Preferred Campus <span class="req">*</span></label>
                            <select id="adm_campus" name="campus" required>
                                <option value="lalmatia" <?= $requestedCampus === 'lalmatia' ? 'selected' : '' ?>>Averroes Lalmatia (Main Campus, Mohammadpur)</option>
                                <option value="banasree" <?= $requestedCampus === 'banasree' ? 'selected' : '' ?>>Averroes Banasree (Eastern Campus, Rampura)</option>
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label for="adm_grade">Grade / Class Applying For <span class="req">*</span></label>
                            <select id="adm_grade" name="grade_applying" required>
                                <option value="" disabled <?= empty($_POST['grade_applying']) ? 'selected' : '' ?>>Select Academic Level</option>
                                <optgroup label="Early Years">
                                    <option value="Playgroup">Playgroup (Age 2.5–3.5 yrs)</option>
                                    <option value="Nursery">Nursery (Age 3.5–4.5 yrs)</option>
                                    <option value="KG-I">Kindergarten I (KG-I)</option>
                                    <option value="KG-II">Kindergarten II (KG-II)</option>
                                </optgroup>
                                <optgroup label="Primary School">
                                    <option value="Class 1">Class 1</option>
                                    <option value="Class 2">Class 2</option>
                                    <option value="Class 3">Class 3</option>
                                    <option value="Class 4">Class 4</option>
                                    <option value="Class 5">Class 5</option>
                                </optgroup>
                                <optgroup label="Middle & Secondary">
                                    <option value="Class 6">Class 6</option>
                                    <option value="Class 7">Class 7</option>
                                    <option value="Class 8">Class 8 (Pre-O Level)</option>
                                    <option value="Class 9 (O Level)">Class 9 (O Level Foundation)</option>
                                    <option value="Class 10 (O Level)">Class 10 (O Level Candidate)</option>
                                </optgroup>
                                <optgroup label="Senior Secondary / A Level">
                                    <option value="Class 11 (A Level)">Class 11 (A Level - AS)</option>
                                    <option value="Class 12 (A Level)">Class 12 (A Level - A2)</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label for="adm_visit_date">Preferred Date of Campus Visit</label>
                            <input type="date" id="adm_visit_date" name="preferred_visit_date" value="<?= e($_POST['preferred_visit_date'] ?? '') ?>" min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="adm-form-group">
                            <label for="adm_message">Questions or Specific Notes (Optional)</label>
                            <input type="text" id="adm_message" name="message" placeholder="e.g. Inquiry regarding Hifz track, transportation, or fee structure" value="<?= e($_POST['message'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="adm-form-actions">
                        <button type="submit" class="btn pri lg adm-submit-btn">
                            <span>Submit Admission Inquiry</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Section 2: Admissions Process 7-Step Workflow (Exact Adaptation of Image 4) -->
    <section id="process" class="adm-process-section" aria-labelledby="adm-process-heading">
        <div class="adm-wrap">
            <div class="adm-section-header" data-aos="fade-up" data-aos-duration="650">
                <div class="adm-cap-badge" aria-hidden="true">
                    <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="adm-cap-img" width="82" height="58">
                </div>
                <h2 id="adm-process-heading" class="adm-section-title">
                    <?= e($process['title_base']) ?><em><?= e($process['title_highlight']) ?></em>
                </h2>
                <p class="adm-section-lead"><?= e($process['lead']) ?></p>
            </div>

            <!-- 7-Step Workflow Infographic Container -->
            <div class="adm-workflow-container" data-aos="fade-up" data-aos-duration="750">
                <div class="adm-workflow-track" role="list">
                    <!-- Continuous Connecting Orange Line -->
                    <div class="adm-spine-line" aria-hidden="true"></div>

                    <?php 
                    // Refined SVG Icon Library matching Image 4
                    $stepIcons = [
                        1 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><circle cx="12" cy="14" r="3"></circle><polyline points="10.5 14 11.5 15 13.5 13"></polyline></svg>',
                        2 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><polyline points="9 16 11 18 15 14"></polyline></svg>',
                        3 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h8"></path><polyline points="15 17 20 12 15 7"></polyline><line x1="9" y1="12" x2="20" y2="12"></line><circle cx="9" cy="7" r="2"></circle><path d="M7 17v-2a2 2 0 0 1 2-2h1"></path></svg>',
                        4 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline><path d="M18 15l4-4-2-2-4 4v2h2z"></path></svg>',
                        5 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><text x="8" y="16" font-size="8" font-weight="bold" fill="currentColor" stroke="none">A+</text></svg>',
                        6 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="12" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>',
                        7 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><circle cx="12" cy="12" r="3"></circle><path d="M6 12h.01M18 12h.01"></path></svg>',
                    ];
                    ?>

                    <?php foreach ($process['steps'] as $idx => $st): ?>
                        <?php 
                        $stepNum = $st['step_num'];
                        $isEven  = ($stepNum % 2 === 0);
                        ?>
                        <div class="adm-step-col <?= $isEven ? 'step-even' : 'step-odd' ?>" role="listitem" data-aos="fade-up" data-aos-delay="<?= $idx * 80 ?>">
                            <?php if (!$isEven): ?>
                                <!-- ODD (Top): Text -> Node -> Stem down to spine -->
                                <div class="adm-step-text-wrap top">
                                    <p class="adm-step-text"><?= e($st['title']) ?></p>
                                </div>
                                <div class="adm-step-node">
                                    <span class="adm-step-pill"><?= e($st['pill']) ?></span>
                                    <div class="adm-step-circle" aria-hidden="true">
                                        <?= $stepIcons[$stepNum] ?? '' ?>
                                    </div>
                                </div>
                                <div class="adm-stem-down" aria-hidden="true"></div>
                            <?php else: ?>
                                <!-- EVEN (Bottom): Stem down from spine -> Node -> Text -->
                                <div class="adm-stem-up" aria-hidden="true"></div>
                                <div class="adm-step-node">
                                    <span class="adm-step-pill"><?= e($st['pill']) ?></span>
                                    <div class="adm-step-circle" aria-hidden="true">
                                        <?= $stepIcons[$stepNum] ?? '' ?>
                                    </div>
                                </div>
                                <div class="adm-step-text-wrap bottom">
                                    <p class="adm-step-text"><?= e($st['title']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Right Connecting Pipeline down into Milestone Box -->
                    <div class="adm-pipeline-right" aria-hidden="true"></div>
                </div>

                <!-- Final Orange Milestone Confirmation Box -->
                <div class="adm-milestone-wrap" data-aos="zoom-in" data-aos-delay="200">
                    <div class="adm-milestone-box">
                        <p class="adm-milestone-text">
                            <?= e($process['milestone_box']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Assessment & Selection Criteria -->
    <section id="assessment" class="adm-assessment-section" aria-labelledby="adm-assess-heading">
        <div class="adm-wrap">
            <div class="adm-section-header" data-aos="fade-up" data-aos-duration="650">
                <div class="adm-cap-badge" aria-hidden="true">
                    <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="adm-cap-img" width="82" height="58">
                </div>
                <h2 id="adm-assess-heading" class="adm-section-title">
                    <?= e($assessment['title_base']) ?><em><?= e($assessment['title_highlight']) ?></em>
                </h2>
                <p class="adm-section-lead"><?= e($assessment['lead']) ?></p>
            </div>

            <!-- Assessment Cards Grid -->
            <div class="adm-assess-grid">
                <?php foreach ($assessment['items'] as $aIdx => $item): ?>
                    <div class="adm-assess-card" data-aos="fade-up" data-aos-delay="<?= ($aIdx % 3) * 100 ?>">
                        <div class="adm-assess-card-header">
                            <span class="adm-level-badge"><?= e($item['badge']) ?></span>
                            <h3 class="adm-level-title"><?= e($item['level']) ?></h3>
                        </div>
                        <p class="adm-assess-card-body"><?= nl2br(e($item['body'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($assessment['notice'])): ?>
                <div class="adm-policy-callout" data-aos="fade-up" data-aos-delay="150">
                    <div class="adm-callout-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#F58220" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <p class="adm-callout-text"><?= e($assessment['notice']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Section 4: Academic Session & Required Documents -->
    <section id="documents" class="adm-docs-section" aria-labelledby="adm-docs-heading">
        <div class="adm-wrap">
            <div class="adm-section-header" data-aos="fade-up" data-aos-duration="650">
                <div class="adm-cap-badge" aria-hidden="true">
                    <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="adm-cap-img" width="82" height="58">
                </div>
                <h2 id="adm-docs-heading" class="adm-section-title">
                    <?= e($sessionDocs['title_base']) ?><em><?= e($sessionDocs['title_highlight']) ?></em>
                </h2>
                <p class="adm-section-lead"><?= e($sessionDocs['lead']) ?></p>
            </div>

            <div class="adm-docs-grid">
                <!-- Academic Session Card -->
                <div class="adm-docs-card adm-session-card" data-aos="fade-right" data-aos-duration="700">
                    <div class="adm-docs-card-header">
                        <div class="adm-docs-icon-badge">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <h3>Academic Sessions &amp; Intakes</h3>
                    </div>
                    <div class="adm-session-timeline">
                        <?php foreach ($sessionDocs['sessions'] as $sRow): ?>
                            <div class="adm-session-row">
                                <div class="adm-session-stream"><?= e($sRow['stream']) ?></div>
                                <div class="adm-session-period"><?= e($sRow['period']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Mandatory Documents Checklist Card -->
                <div class="adm-docs-card adm-checklist-card" data-aos="fade-left" data-aos-duration="700" data-aos-delay="120">
                    <div class="adm-docs-card-header">
                        <div class="adm-docs-icon-badge orange">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <h3>Mandatory Document Checklist</h3>
                    </div>
                    <ul class="adm-checklist">
                        <?php foreach ($sessionDocs['documents'] as $docItem): ?>
                            <li class="adm-check-item">
                                <span class="adm-check-circle" aria-hidden="true">✓</span>
                                <span class="adm-check-text"><?= e($docItem) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Campus Directory & Contact Information -->
    <section id="contact" class="adm-contact-section" aria-labelledby="adm-contact-heading">
        <div class="adm-wrap">
            <div class="adm-section-header" data-aos="fade-up" data-aos-duration="650">
                <div class="adm-cap-badge" aria-hidden="true">
                    <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="adm-cap-img" width="82" height="58">
                </div>
                <h2 id="adm-contact-heading" class="adm-section-title">
                    Visit Our <em>Campus</em>
                </h2>
                <p class="adm-section-lead">We warmly invite you and your family to tour our campus facilities and experience the Averroes difference firsthand.</p>
            </div>

            <!-- Campus Cards Grid -->
            <div class="adm-campuses-grid">
                <?php foreach ($allCampuses as $cKey => $camp): ?>
                    <?php $isCurrent = ($cKey === $requestedCampus); ?>
                    <div class="adm-campus-card <?= $isCurrent ? 'highlight' : '' ?>" data-aos="zoom-in" data-aos-duration="650" data-aos-delay="<?= $cKey === 'banasree' ? '120' : '0' ?>">
                        <div class="adm-campus-card-img-wrap">
                            <img src="<?= asset_url($camp['image']) ?>" alt="<?= e($camp['name']) ?>" class="adm-campus-img" loading="lazy">
                            <?php if ($isCurrent): ?>
                                <span class="adm-current-badge">Currently Viewing</span>
                            <?php endif; ?>
                        </div>
                        <div class="adm-campus-card-content">
                            <h3 class="adm-campus-name"><?= e($camp['name']) ?></h3>
                            <p class="adm-campus-addr">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span><?= e($camp['address']) ?></span>
                            </p>
                            <div class="adm-campus-meta">
                                <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $camp['phone'])) ?>" class="adm-meta-link">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.02-.24 11.72 11.72 0 003.68.59 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1 11.72 11.72 0 00.59 3.68 1 1 0 01-.24 1.02l-2.23 2.09z"/>
                                    </svg>
                                    <span><?= e($camp['phone']) ?></span>
                                </a>
                                <a href="mailto:<?= e($camp['email']) ?>" class="adm-meta-link">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <span><?= e($camp['email']) ?></span>
                                </a>
                            </div>
                            <div class="adm-visiting-hours">
                                <strong>Visiting Hours:</strong> <?= e($camp['visiting_hours']) ?>
                            </div>
                            <?php if (!$isCurrent): ?>
                                <a href="<?= base_url('admission.php?campus=' . $cKey) ?>" class="btn out sm" style="margin-top: 14px;">Switch to <?= e($camp['short_name']) ?></a>
                            <?php else: ?>
                                <a href="#query" class="btn pri sm" style="margin-top: 14px;">Apply to this Campus</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php
include __DIR__ . '/includes/footer.php';
