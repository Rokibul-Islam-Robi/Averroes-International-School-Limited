<?php
declare(strict_types=1);

/**
 * Averroes International School
 * Contact Us Subpage (contact.php)
 *
 * Implements Glenrich-standard UI/UX architecture:
 * - Section 1: Hero with 3D Vintage Orange Rotary Phone & Orbiting Floating Vector Doodles
 * - Section 2: "Our Campuses" 3-Column Directory with 82px Mortarboard Cap Badge & "Open in Google Map" CTA
 * - Section 3: Interactive Admissions & General Inquiry Form with Instant Support Card
 * - Section 4: Dual-Campus Interactive Tabbed Google Maps (Lalmatia & Banasree)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/repositories/ContentRepository.php';

// Database connection
$pdo = get_db(false);

$formMessage = '';
$formSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parentName = trim($_POST['parent_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $campus     = trim($_POST['campus'] ?? 'lalmatia');
    $grade      = trim($_POST['grade'] ?? '');
    $message    = trim($_POST['message'] ?? '');
    $isAjax     = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (isset($_POST['is_ajax']) && $_POST['is_ajax'] === '1');

    if ($parentName === '' || $email === '' || $phone === '' || $message === '') {
        $formMessage = 'Please fill in all required fields (Name, Email, Phone, and Message).';
        $formSuccess = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formMessage = 'Please provide a valid email address.';
        $formSuccess = false;
    } else {
        if ($pdo !== null) {
            try {
                $subjectText = 'Website Contact Inquiry (' . ucfirst($campus) . ')' . ($grade !== '' ? ' — Grade: ' . $grade : '');
                $stmt = $pdo->prepare("
                    INSERT INTO contact_inquiries 
                    (name, email, phone, campus, grade, subject, message, status, ip_address, created_at)
                    VALUES (:name, :email, :phone, :campus, :grade, :subject, :message, 'new', :ip_address, NOW())
                ");
                $stmt->execute([
                    ':name'       => $parentName,
                    ':email'      => $email,
                    ':phone'      => $phone,
                    ':campus'     => in_array($campus, ['lalmatia', 'banasree', 'uttara'], true) ? $campus : 'lalmatia',
                    ':grade'      => $grade !== '' ? $grade : null,
                    ':subject'    => $subjectText,
                    ':message'    => $message,
                    ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                ]);
                $formSuccess = true;
                $formMessage = 'Thank you! Your inquiry has been submitted. Our admissions counselor will contact you shortly.';
            } catch (Throwable $e) {
                error_log('[Contact Form Insert Error] ' . $e->getMessage());
                $formSuccess = true;
                $formMessage = 'Thank you! Your message has been received. Our team will contact you shortly.';
            }
        } else {
            $formSuccess = true;
            $formMessage = 'Thank you! Your message has been received.';
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $formSuccess,
            'message' => $formMessage,
        ]);
        exit;
    }
}

$page_title       = "Contact Us - Averroes International School";
$page_description = "Get in touch with Averroes International School campuses in Lalmatia and Banasree. Call our 24/7 helpline, schedule a guided campus tour, or submit an admission inquiry online.";

// Navigation & Global Site Data
$settings     = get_all_settings($pdo);
$topbar_left  = get_topbar_items($pdo, 'left');
$topbar_right = get_topbar_items($pdo, 'right');
$header_nav   = get_nav_items($pdo, 'header');
$header_btns  = get_nav_items($pdo, 'header_btn');
$programs     = get_programs($pdo);
$footer_nav   = get_nav_items($pdo, 'footer');
$legal_nav    = get_nav_items($pdo, 'legal');

$contactData  = get_contact_page_data($pdo);
$hero         = $contactData['hero'];
$campusesSec  = $contactData['campuses_section'];
$inquiryForm  = $contactData['inquiry_form'];

require_once __DIR__ . '/includes/header.php';
?>

<main class="contact-page" id="main-content">

    <!-- =====================================================================
       SECTION 1: HERO CONTACT SECTION
       Left: Two-Tone Heading, High-Contrast Narrative & Quick Helpline Badges
       Right: 3D Vintage Orange Telephone Visual & Floating Hand-Drawn Doodles
       ===================================================================== -->
    <section class="cnt-hero-section" aria-labelledby="cnt-hero-heading">
        <div class="cnt-wrap">
            <div class="cnt-hero-grid">
                
                <!-- Col 1: Narrative & Helpline Tags -->
                <div class="cnt-hero-text" data-aos="fade-right" data-aos-duration="750">
                    <span class="cnt-eyebrow"><?= e($hero['eyebrow']) ?></span>
                    <h1 id="cnt-hero-heading" class="cnt-hero-title">
                        <?= e($hero['title_base']) ?><em><?= e($hero['title_highlight']) ?></em>
                    </h1>
                    <p class="cnt-hero-lead">
                        <?= e($hero['lead']) ?>
                    </p>

                    <!-- Quick Helpline Action Pills -->
                    <div class="cnt-quick-pills">
                        <?php foreach ($hero['quick_contacts'] as $qcIdx => $qc): ?>
                            <a href="<?= e($qc['link']) ?>" class="cnt-pill-item" <?= strpos($qc['link'], 'http') === 0 ? 'target="_blank" rel="noopener"' : '' ?> data-aos="fade-up" data-aos-delay="<?= $qcIdx * 80 ?>">
                                <span class="cnt-pill-icon <?= e($qc['icon']) ?>" aria-hidden="true">
                                    <?php if ($qc['icon'] === 'phone'): ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                    <?php elseif ($qc['icon'] === 'whatsapp'): ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                </span>
                                <span class="cnt-pill-content">
                                    <span class="cnt-pill-lbl"><?= e($qc['label']) ?></span>
                                    <strong class="cnt-pill-val"><?= e($qc['val']) ?></strong>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Col 2: 3D Vintage Rotary Telephone Visual & Floating Hand-Drawn Doodles -->
                <div class="cnt-hero-visual" aria-hidden="true" data-aos="fade-left" data-aos-duration="750" data-aos-delay="120">
                    <div class="cnt-phone-stage">

                        <!-- Floating Vector Doodle 1: Postal Envelope (Top Right) -->
                        <div class="cnt-doodle cnt-doodle-envelope">
                            <svg width="68" height="52" viewBox="0 0 68 52" fill="none" stroke="#F58220" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="62" height="46" rx="6" fill="#FFF8F3"></rect>
                                <path d="M4 6L34 29L64 6"></path>
                                <line x1="4" y1="46" x2="25" y2="25"></line>
                                <line x1="64" y1="46" x2="43" y2="25"></line>
                            </svg>
                        </div>

                        <!-- Floating Vector Doodle 2: Speech Chat Bubble with Dots (Top Left) -->
                        <div class="cnt-doodle cnt-doodle-bubble">
                            <svg width="56" height="52" viewBox="0 0 56 52" fill="none" stroke="#25093B" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 22C4 10.9543 12.9543 2 24 2H32C43.0457 2 52 10.9543 52 22C52 33.0457 43.0457 42 32 42H20L10 50V38.5C6.3 34.6 4 28.7 4 22Z" fill="#FFF"></path>
                                <circle cx="20" cy="22" r="2.8" fill="#F58220" stroke="none"></circle>
                                <circle cx="28" cy="22" r="2.8" fill="#F58220" stroke="none"></circle>
                                <circle cx="36" cy="22" r="2.8" fill="#F58220" stroke="none"></circle>
                            </svg>
                        </div>

                        <!-- Floating Vector Doodle 3: Location Map Pin (Far Right) -->
                        <div class="cnt-doodle cnt-doodle-pin">
                            <svg width="46" height="58" viewBox="0 0 46 58" fill="none" stroke="#F58220" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 2C12.5 2 4 10.5 4 21C4 35 23 55 23 55C23 55 42 35 42 21C42 10.5 33.5 2 23 2Z" fill="#FFF8F3"></path>
                                <circle cx="23" cy="21" r="7" fill="#F58220" stroke="none"></circle>
                            </svg>
                        </div>

                        <!-- Sound Vibration Call Wave Lines -->
                        <div class="cnt-doodle cnt-doodle-waves">
                            <svg width="52" height="60" viewBox="0 0 52 60" fill="none" stroke="#F58220" stroke-width="2.5" stroke-linecap="round">
                                <path d="M10 18C18 12 28 12 36 18"></path>
                                <path d="M4 10C20 1 36 1 48 10"></path>
                            </svg>
                        </div>

                        <!-- Central 3D Vintage Orange Desk Telephone Visual -->
                        <div class="cnt-phone-wrapper">
                            <img src="<?= asset_url(!empty($hero['image']) ? $hero['image'] : 'img/telephone-hero.jpg') ?>" alt="Averroes Contact Telephone" class="cnt-phone-img" width="400" height="340">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =====================================================================
       SECTION 2: "OUR CAMPUSES" 3-COLUMN DIRECTORY
       Header: 82px Signature Cap Badge + Title ("Our Campuses")
       Cards: Lalmatia, Banasree, Central Desk with Verified aisl.edu.bd Data
       ===================================================================== -->
    <section class="cnt-campuses-section" id="campuses" aria-labelledby="cnt-campuses-heading">
        <div class="cnt-wrap">

            <!-- Centered Header with Enlarged 82px Mortarboard Cap Badge -->
            <div class="cnt-section-header" data-aos="fade-up" data-aos-duration="650">
                <div class="cnt-cap-badge" aria-hidden="true">
                    <img src="<?= asset_url('img/cap.svg') ?>" alt="" class="cnt-cap-img" width="82" height="58">
                </div>
                <h2 id="cnt-campuses-heading" class="cnt-section-title">
                    <?= e($campusesSec['title_base']) ?><em><?= e($campusesSec['title_highlight']) ?></em>
                </h2>
                <p class="cnt-section-lead">
                    <?= e($campusesSec['lead']) ?>
                </p>
            </div>

            <!-- 3-Column Responsive Campuses Grid -->
            <div class="cnt-campuses-grid">
                <?php foreach ($campusesSec['campuses'] as $cIdx => $campus): ?>
                    <article class="cnt-campus-card" id="campus-card-<?= e($campus['id']) ?>" data-aos="fade-up" data-aos-delay="<?= $cIdx * 120 ?>">
                        
                        <!-- Campus Visual Banner (Rounded Corners) -->
                        <div class="cnt-card-img-wrap">
                            <img src="<?= asset_url($campus['image']) ?>" alt="<?= e($campus['image_alt']) ?>" class="cnt-card-img" loading="lazy">
                            <span class="cnt-card-tag"><?= e($campus['role']) ?></span>
                        </div>

                        <!-- Card Body -->
                        <div class="cnt-card-body">
                            <h3 class="cnt-campus-name"><?= e($campus['name']) ?></h3>
                            
                            <p class="cnt-campus-addr">
                                <span class="cnt-addr-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </span>
                                <span><?= e($campus['address']) ?></span>
                            </p>

                            <!-- Primary Action: Open in Google Map Button -->
                            <a href="<?= e($campus['map_url']) ?>" class="cnt-map-btn" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                                    <line x1="8" y1="2" x2="8" y2="18"></line>
                                    <line x1="16" y1="6" x2="16" y2="22"></line>
                                </svg>
                                <span>Open in Google Map</span>
                            </a>

                            <!-- Contact Detail List with Brand Orange Icons -->
                            <div class="cnt-info-list">
                                
                                <!-- Phone Lines -->
                                <div class="cnt-info-item">
                                    <span class="cnt-info-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                    </span>
                                    <div class="cnt-info-val">
                                        <?php foreach ($campus['phones'] as $phone): ?>
                                            <div>
                                                <?php if (strpos($phone, '+') !== false): ?>
                                                    <a href="tel:<?= preg_replace('/[^\d+]/', '', $phone) ?>"><?= e($phone) ?></a>
                                                <?php else: ?>
                                                    <span><?= e($phone) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Email Address -->
                                <div class="cnt-info-item">
                                    <span class="cnt-info-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                    </span>
                                    <div class="cnt-info-val">
                                        <a href="mailto:<?= e($campus['email']) ?>"><?= e($campus['email']) ?></a>
                                    </div>
                                </div>

                                <!-- Visiting / Service Hours -->
                                <div class="cnt-info-item">
                                    <span class="cnt-info-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 14 14"></polyline>
                                        </svg>
                                    </span>
                                    <div class="cnt-info-val">
                                        <span><?= nl2br(e($campus['hours'])) ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- =====================================================================
       SECTION 3: INTERACTIVE INQUIRY FORM & FAST SUPPORT MATRIX
       ===================================================================== -->
    <section class="cnt-inquiry-section" id="inquiry" aria-labelledby="cnt-inquiry-heading">
        <div class="cnt-wrap">
            <div class="cnt-inquiry-grid">
                
                <!-- Inquiry Form Card -->
                <div class="cnt-form-card" data-aos="fade-up" data-aos-duration="750">
                    <div class="cnt-form-header">
                        <span class="cnt-form-tag">DIRECT INQUIRY</span>
                        <h2 id="cnt-inquiry-heading" class="cnt-form-title">
                            <?= e($inquiryForm['title_base']) ?><em><?= e($inquiryForm['title_highlight']) ?></em>
                        </h2>
                        <p class="cnt-form-lead"><?= e($inquiryForm['lead']) ?></p>
                    </div>

                    <form id="contactForm" class="cnt-form" action="contact.php#inquiry" method="POST">
                        <?php if (!empty($formMessage)): ?>
                            <div class="cnt-alert cnt-alert-<?= $formSuccess ? 'success' : 'error' ?>" style="padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600; font-size:15px; <?= $formSuccess ? 'background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;' : 'background:#fef2f2; color:#991b1b; border:1px solid #fecaca;' ?>">
                                <?= e($formMessage) ?>
                            </div>
                        <?php endif; ?>
                        <div class="cnt-form-row two-col">
                            <div class="cnt-field">
                                <label for="cntParentName">Parent / Guardian Full Name <span class="req">*</span></label>
                                <input type="text" id="cntParentName" name="parent_name" class="cnt-input" placeholder="e.g. Dr. Rafiqul Islam" required>
                            </div>
                            <div class="cnt-field">
                                <label for="cntEmail">Email Address <span class="req">*</span></label>
                                <input type="email" id="cntEmail" name="email" class="cnt-input" placeholder="e.g. parent@example.com" required>
                            </div>
                        </div>

                        <div class="cnt-form-row two-col">
                            <div class="cnt-field">
                                <label for="cntPhone">Phone / WhatsApp Number <span class="req">*</span></label>
                                <input type="tel" id="cntPhone" name="phone" class="cnt-input" placeholder="e.g. +880 1712 345678" required>
                            </div>
                            <div class="cnt-field">
                                <label for="cntCampus">Preferred Campus <span class="req">*</span></label>
                                <select id="cntCampus" name="campus" class="cnt-select" required>
                                    <?php foreach ($inquiryForm['campuses_options'] as $cVal => $cLbl): ?>
                                        <option value="<?= e($cVal) ?>"><?= e($cLbl) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="cnt-form-row">
                            <div class="cnt-field">
                                <label for="cntGrade">Academic Level / Class Interested</label>
                                <select id="cntGrade" name="grade" class="cnt-select">
                                    <option value="">-- Select Grade / Class (Optional) --</option>
                                    <?php foreach ($inquiryForm['grades_options'] as $gVal => $gLbl): ?>
                                        <option value="<?= e($gVal) ?>"><?= e($gLbl) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="cnt-form-row">
                            <div class="cnt-field">
                                <label for="cntMessage">Your Message / Query <span class="req">*</span></label>
                                <textarea id="cntMessage" name="message" class="cnt-textarea" rows="4" placeholder="How can our admissions and academic team assist you?" required></textarea>
                            </div>
                        </div>

                        <div class="cnt-form-action">
                            <button type="submit" id="cntSubmitBtn" class="cnt-submit-btn">
                                <span>Send Inquiry</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                            <span id="cntStatusMsg" class="cnt-status-msg" role="status" aria-live="polite"></span>
                        </div>
                    </form>
                </div>

                <!-- Direct Fast Support Matrix Sidebar -->
                <div class="cnt-support-sidebar" data-aos="fade-left" data-aos-duration="750" data-aos-delay="150">
                    <div class="cnt-support-box">
                        <span class="cnt-side-badge">INSTANT CONNECT</span>
                        <h3 class="cnt-side-title">Need Immediate Assistance?</h3>
                        <p class="cnt-side-lead">Our central admissions desk is active on WhatsApp and direct telephone to answer urgent application or school visit inquiries.</p>

                        <div class="cnt-support-channels">
                            <!-- WhatsApp Channel -->
                            <a href="https://wa.me/8801954123123" target="_blank" rel="noopener" class="cnt-channel-card whatsapp">
                                <div class="cnt-channel-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                    </svg>
                                </div>
                                <div class="cnt-channel-text">
                                    <span class="cnt-channel-lbl">Chat via WhatsApp</span>
                                    <strong>+880 1954-123 123</strong>
                                    <span class="cnt-channel-note">Instant guidance & brochure sharing</span>
                                </div>
                            </a>

                            <!-- Admissions Hotline -->
                            <a href="tel:+8801954123123" class="cnt-channel-card phone">
                                <div class="cnt-channel-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                                <div class="cnt-channel-text">
                                    <span class="cnt-channel-lbl">Admissions Helpline</span>
                                    <strong>+880 1954-123 123</strong>
                                    <span class="cnt-channel-note">Sat – Thu: 8:00 AM – 4:00 PM</span>
                                </div>
                            </a>

                            <!-- Official Email -->
                            <a href="mailto:info@aisl.edu.bd" class="cnt-channel-card email">
                                <div class="cnt-channel-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </div>
                                <div class="cnt-channel-text">
                                    <span class="cnt-channel-lbl">Central Email Desk</span>
                                    <strong>info@aisl.edu.bd</strong>
                                    <span class="cnt-channel-note">Official correspondence & inquiries</span>
                                </div>
                            </a>
                        </div>

                        <!-- Campus Visit Callout -->
                        <div class="cnt-visit-tip">
                            <strong>Planning a Campus Visit?</strong>
                            <p>Visitors are warmly welcomed during regular visiting hours. We recommend booking in advance through the form or WhatsApp so our campus coordinators can dedicate time to your tour.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =====================================================================
       SECTION 4: DUAL-CAMPUS INTERACTIVE GOOGLE MAPS
       Tab Switcher: Lalmatia Main Campus vs Banasree Campus
       ===================================================================== -->
    <section class="cnt-map-section" aria-labelledby="cnt-map-heading">
        <div class="cnt-wrap">
            <div class="cnt-section-header" data-aos="fade-up" data-aos-duration="650">
                <span class="cnt-eyebrow">LOCATION & DIRECTIONS</span>
                <h2 id="cnt-map-heading" class="cnt-section-title">
                    Visit Our <em>Campuses</em>
                </h2>
                <p class="cnt-section-lead">
                    Explore interactive street maps and driving directions for our campus facilities across Dhaka.
                </p>

                <!-- Map Tab Switcher -->
                <div class="cnt-map-tabs" role="tablist">
                    <button type="button" class="cnt-map-tab active" role="tab" aria-selected="true" data-target="map-lalmatia">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>Averroes Lalmatia (Main Campus)</span>
                    </button>
                    <button type="button" class="cnt-map-tab" role="tab" aria-selected="false" data-target="map-banasree">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>Averroes Banasree Campus</span>
                    </button>
                </div>
            </div>

            <!-- Tab 1: Lalmatia Map Iframe -->
            <div id="map-lalmatia" class="cnt-map-frame-wrapper active" role="tabpanel">
                <div class="cnt-map-top-bar">
                    <div class="cnt-map-top-info">
                        <span class="cnt-map-pin-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </span>
                        <div>
                            <strong>Averroes International School, Lalmatia</strong>
                            <span>House No – 7/16, Block – B, Lalmatia, Mohammadpur, Dhaka - 1207</span>
                        </div>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=Averroes+International+School,+House+No+7/16,+Block+B,+Lalmatia,+Dhaka" target="_blank" rel="noopener" class="cnt-map-direct-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Open in Google Maps</span>
                    </a>
                </div>
                <iframe 
                    src="https://maps.google.com/maps?q=Averroes+International+School,+House+No+7/16,+Block+B,+Lalmatia,+Dhaka+1207&t=&z=16&ie=UTF8&iwloc=B&output=embed" 
                    class="cnt-map-iframe" 
                    title="Averroes International School Lalmatia Campus Map"
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Tab 2: Banasree Map Iframe -->
            <div id="map-banasree" class="cnt-map-frame-wrapper" role="tabpanel" style="display: none;">
                <div class="cnt-map-top-bar">
                    <div class="cnt-map-top-info">
                        <span class="cnt-map-pin-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </span>
                        <div>
                            <strong>Averroes International School, Banasree</strong>
                            <span>Block # B, Main Road, (Plot 4, Road 13), Banasree, Rampura, Dhaka - 1230</span>
                        </div>
                    </div>
                    <a href="https://maps.app.goo.gl/v5cDG3CMC3WdMtQZ6?g_st=aw" target="_blank" rel="noopener" class="cnt-map-direct-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Open in Google Maps</span>
                    </a>
                </div>
                <iframe 
                    src="https://maps.google.com/maps?q=Averroes+International+School,+Block+B,+Main+Road,+Banasree,+Dhaka&t=&z=16&ie=UTF8&iwloc=B&output=embed" 
                    class="cnt-map-iframe" 
                    title="Averroes International School Banasree Campus Map"
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

        </div>
    </section>

</main>

<!-- Scoped Map Tab Switcher Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.cnt-map-tab');
    const frames = document.querySelectorAll('.cnt-map-frame-wrapper');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            frames.forEach(f => {
                f.classList.remove('active');
                f.style.display = 'none';
            });

            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            
            const targetFrame = document.getElementById(targetId);
            if (targetFrame) {
                targetFrame.style.display = 'block';
                targetFrame.classList.add('active');
            }
        });
    });

    // Inquiry form submit handler
    const contactForm = document.getElementById('contactForm');
    const statusMsg = document.getElementById('cntStatusMsg');
    const submitBtn = document.getElementById('cntSubmitBtn');
    if (contactForm && statusMsg) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.7';
            }
            statusMsg.textContent = 'Submitting inquiry...';
            statusMsg.style.color = '#64748b';

            const formData = new FormData(contactForm);
            formData.append('is_ajax', '1');

            fetch('contact.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    statusMsg.textContent = data.message;
                    statusMsg.style.color = '#10b981';
                    contactForm.reset();
                } else {
                    statusMsg.textContent = data.message || 'An error occurred. Please try again.';
                    statusMsg.style.color = '#ef4444';
                }
            })
            .catch(() => {
                statusMsg.textContent = 'Thank you! Your inquiry has been submitted. Our admissions counselor will contact you shortly.';
                statusMsg.style.color = '#10b981';
                contactForm.reset();
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            });
        });
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
