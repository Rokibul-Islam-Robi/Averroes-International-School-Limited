<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Admissions Portal & Dynamic Content Manager
 * Manages: admission.php (Hero, Structured Assessment, Sessions & Documents, 7-Step Process Roadmap, FAQs)
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Admissions Portal Content & Criteria Manager';

// Handle Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: admission.php');
        exit;
    }

    $action = $_POST['form_action'] ?? '';

    // 1. Save Admissions Hero Banner
    if ($action === 'save_hero') {
        try {
            $stmt = $pdo->prepare("
                UPDATE subpage_contents 
                SET eyebrow = :eyebrow, title_base = :title_base, title_highlight = :title_highlight, lead_text = :lead_text
                WHERE page_key = 'admission' AND section_key = 'hero'
            ");
            $stmt->execute([
                'eyebrow'         => trim($_POST['eyebrow'] ?? ''),
                'title_base'      => trim($_POST['title_base'] ?? ''),
                'title_highlight' => trim($_POST['title_highlight'] ?? ''),
                'lead_text'       => trim($_POST['lead_text'] ?? ''),
            ]);
            set_flash('success', 'Admissions Hero banner updated successfully!');
        } catch (Throwable $e) {
            set_flash('error', 'Error updating hero: ' . $e->getMessage());
        }
        header('Location: admission.php');
        exit;
    }

    // 2. Save Structured Assessment Criteria (Cards Add / Edit / Delete)
    if ($action === 'save_assessment') {
        try {
            $pdo->beginTransaction();

            // Update Section Headers & Policy Notice
            $upSec = $pdo->prepare("
                UPDATE subpage_contents
                SET title_base = :tb, title_highlight = :th, lead_text = :lead, body_html = :notice
                WHERE page_key = 'admission' AND section_key = 'assessment'
            ");
            $upSec->execute([
                'tb'     => trim($_POST['title_base'] ?? 'Structured '),
                'th'     => trim($_POST['title_highlight'] ?? 'Assessment'),
                'lead'   => trim($_POST['lead_text'] ?? ''),
                'notice' => trim($_POST['notice_text'] ?? ''),
            ]);

            $assessId = (int)$pdo->query("SELECT id FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'assessment'")->fetchColumn();

            // Re-sync Assessment Cards (Delete existing, insert posted)
            $pdo->prepare("DELETE FROM subpage_items WHERE page_key = 'admission' AND section_key = 'assessment'")->execute();

            if (isset($_POST['assess_items']) && is_array($_POST['assess_items'])) {
                $insItem = $pdo->prepare("
                    INSERT INTO subpage_items (content_id, page_key, section_key, item_type, lbl, title, description, sort_order, is_active)
                    VALUES (?, 'admission', 'assessment', 'card', ?, ?, ?, ?, 1)
                ");
                $sort = 1;
                foreach ($_POST['assess_items'] as $item) {
                    $lvl = trim($item['level'] ?? '');
                    if ($lvl !== '') {
                        $insItem->execute([
                            $assessId,
                            trim($item['badge'] ?? 'Standard'),
                            $lvl,
                            trim($item['body'] ?? ''),
                            $sort++
                        ]);
                    }
                }
            }

            $pdo->commit();
            set_flash('success', 'Structured Assessment criteria and cards saved successfully!');
        } catch (Throwable $e) {
            $pdo->rollBack();
            set_flash('error', 'Error updating assessment section: ' . $e->getMessage());
        }
        header('Location: admission.php');
        exit;
    }

    // 3. Save Academic Sessions & Required Documents (Add / Edit / Delete)
    if ($action === 'save_sessions_docs') {
        try {
            $pdo->beginTransaction();

            // Update Section Header
            $upSec = $pdo->prepare("
                UPDATE subpage_contents
                SET title_base = :tb, title_highlight = :th, lead_text = :lead
                WHERE page_key = 'admission' AND section_key = 'session_docs'
            ");
            $upSec->execute([
                'tb'   => trim($_POST['title_base'] ?? 'Academic Session & Required '),
                'th'   => trim($_POST['title_highlight'] ?? 'Documents'),
                'lead' => trim($_POST['lead_text'] ?? ''),
            ]);

            $docsId = (int)$pdo->query("SELECT id FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'session_docs'")->fetchColumn();

            // Re-sync Academic Sessions
            $pdo->prepare("DELETE FROM subpage_items WHERE page_key = 'admission' AND section_key = 'academic_sessions'")->execute();
            if (isset($_POST['sessions']) && is_array($_POST['sessions'])) {
                $insSess = $pdo->prepare("
                    INSERT INTO subpage_items (content_id, page_key, section_key, item_type, title, subtitle, sort_order, is_active)
                    VALUES (?, 'admission', 'academic_sessions', 'session', ?, ?, ?, 1)
                ");
                $sSort = 1;
                foreach ($_POST['sessions'] as $sess) {
                    $stream = trim($sess['stream'] ?? '');
                    if ($stream !== '') {
                        $insSess->execute([$docsId, $stream, trim($sess['period'] ?? ''), $sSort++]);
                    }
                }
            }

            // Re-sync Document Checklist
            $pdo->prepare("DELETE FROM subpage_items WHERE page_key = 'admission' AND section_key = 'document_checklist'")->execute();
            if (isset($_POST['documents']) && is_array($_POST['documents'])) {
                $insDoc = $pdo->prepare("
                    INSERT INTO subpage_items (content_id, page_key, section_key, item_type, title, sort_order, is_active)
                    VALUES (?, 'admission', 'document_checklist', 'doc', ?, ?, 1)
                ");
                $dSort = 1;
                foreach ($_POST['documents'] as $docText) {
                    $dClean = trim((string)$docText);
                    if ($dClean !== '') {
                        $insDoc->execute([$docsId, $dClean, $dSort++]);
                    }
                }
            }

            $pdo->commit();
            set_flash('success', 'Academic Sessions and Document Checklist updated successfully!');
        } catch (Throwable $e) {
            $pdo->rollBack();
            set_flash('error', 'Error updating sessions & documents: ' . $e->getMessage());
        }
        header('Location: admission.php');
        exit;
    }

    // 4. Save Admissions Timeline Steps
    if ($action === 'save_timeline' && isset($_POST['timeline']) && is_array($_POST['timeline'])) {
        try {
            $pdo->beginTransaction();
            foreach ($_POST['timeline'] as $id => $step) {
                $stmt = $pdo->prepare("UPDATE admission_timeline SET title = :t, description = :d WHERE id = :id");
                $stmt->execute([
                    't'  => trim($step['title'] ?? ''),
                    'd'  => trim($step['desc'] ?? ''),
                    'id' => (int)$id,
                ]);
            }
            $pdo->commit();
            set_flash('success', 'Admissions timeline steps updated successfully!');
        } catch (Throwable $e) {
            $pdo->rollBack();
            set_flash('error', 'Update error: ' . $e->getMessage());
        }
        header('Location: admission.php');
        exit;
    }

    // 5. Add / Delete FAQ
    if ($action === 'add_faq') {
        $q = trim($_POST['question'] ?? '');
        $a = trim($_POST['answer'] ?? '');
        $cat = trim($_POST['category'] ?? 'General');

        if ($q !== '' && $a !== '') {
            try {
                $maxSort = (int)$pdo->query("SELECT MAX(sort_order) FROM admission_faqs")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO admission_faqs (category, question, answer, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$cat, $q, $a, $maxSort + 1]);
                set_flash('success', 'New FAQ added successfully!');
            } catch (Throwable $e) {
                set_flash('error', 'Error adding FAQ: ' . $e->getMessage());
            }
        }
        header('Location: admission.php');
        exit;
    }

    if ($action === 'delete_faq') {
        $id = (int)($_POST['faq_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare("DELETE FROM admission_faqs WHERE id = ?")->execute([$id]);
                set_flash('success', 'FAQ deleted.');
            } catch (Throwable $e) {
                set_flash('error', 'Error deleting FAQ: ' . $e->getMessage());
            }
        }
        header('Location: admission.php');
        exit;
    }
}

// Fetch all admission data from database
$admHero = [];
$admAssess = [];
$assessCards = [];
$admDocsSec = [];
$sessionItems = [];
$docChecklist = [];
$steps = [];
$faqs = [];

if ($pdo !== null) {
    try {
        $admHero     = $pdo->query("SELECT * FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'hero' LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        $admAssess   = $pdo->query("SELECT * FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'assessment' LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        $assessCards = $pdo->query("SELECT * FROM subpage_items WHERE page_key = 'admission' AND section_key = 'assessment' ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        $admDocsSec  = $pdo->query("SELECT * FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'session_docs' LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        $sessionItems = $pdo->query("SELECT * FROM subpage_items WHERE page_key = 'admission' AND section_key = 'academic_sessions' ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
        $docChecklist = $pdo->query("SELECT * FROM subpage_items WHERE page_key = 'admission' AND section_key = 'document_checklist' ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $steps       = $pdo->query("SELECT * FROM admission_timeline ORDER BY sort_order ASC, step_num ASC")->fetchAll(PDO::FETCH_ASSOC);
        $faqs        = $pdo->query("SELECT * FROM admission_faqs ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Admission Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Admissions Portal Content & Criteria Manager
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Add, edit, or delete any content, assessment stages, session intakes, and checklist items directly.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="<?= base_url('admission.php') ?>" target="_blank" class="adm-btn adm-btn-secondary">
            👁️ View Public Admissions Page
        </a>
    </div>
</div>

<!-- =========================================================================
     0. ADMISSIONS HERO SECTION (Marked in Image 1)
     ========================================================================= -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>🎓 Section 0: Admissions Page Hero Banner (Marked in Image 1)</span>
        </div>
        <span class="adm-pill orange">Hero Header</span>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="save_hero">

            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">Eyebrow Tagline</label>
                    <input type="text" name="eyebrow" class="adm-input" value="<?= htmlspecialchars($admHero['eyebrow'] ?? 'ADMISSIONS OPEN FOR SESSION 2026–2027') ?>" placeholder="e.g. ADMISSIONS OPEN FOR SESSION 2026–2027">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Title Prefix (Normal Text)</label>
                    <input type="text" name="title_base" class="adm-input" value="<?= htmlspecialchars($admHero['title_base'] ?? 'Open the Doors to a ') ?>" placeholder="e.g. Open the Doors to a ">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Title Highlight (Orange Gradient)</label>
                    <input type="text" name="title_highlight" class="adm-input" value="<?= htmlspecialchars($admHero['title_highlight'] ?? 'Brighter Future') ?>" placeholder="e.g. Brighter Future">
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Lead Narrative Paragraph (Direct Text without HTML)</label>
                <textarea name="lead_text" class="adm-textarea" rows="3" placeholder="Enter paragraph text directly..."><?= htmlspecialchars($admHero['lead_text'] ?? '') ?></textarea>
                <div class="adm-hint">Direct plain text is supported. Line breaks are automatically formatted.</div>
            </div>

            <div style="text-align:right;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    💾 Save Admissions Hero
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     1. STRUCTURED ASSESSMENT CRITERIA & CARDS (Marked in Image 2)
     ========================================================================= -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>📊 Section 1: Structured Assessment Criteria (Marked in Image 2)</span>
        </div>
        <span class="adm-pill green">Add • Edit • Delete</span>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="save_assessment">

            <!-- Section Headings -->
            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">Section Title Base</label>
                    <input type="text" name="title_base" class="adm-input" value="<?= htmlspecialchars($admAssess['title_base'] ?? 'Structured ') ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Title Highlight (Orange)</label>
                    <input type="text" name="title_highlight" class="adm-input" value="<?= htmlspecialchars($admAssess['title_highlight'] ?? 'Assessment') ?>">
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Section Narrative / Lead</label>
                <textarea name="lead_text" class="adm-textarea" rows="2"><?= htmlspecialchars($admAssess['lead_text'] ?? '') ?></textarea>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Merit & Vacancy Policy Callout Notice</label>
                <textarea name="notice_text" class="adm-textarea" rows="2"><?= htmlspecialchars($admAssess['body_html'] ?? '') ?></textarea>
            </div>

            <!-- Dynamic Assessment Cards Repeater -->
            <div style="margin-top:20px; border-top:1px dashed #E2E8F0; padding-top:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <div>
                        <h4 style="font-size:14px; font-weight:700; color:#25093B; margin:0;">Assessment Stages & Grade Cards</h4>
                        <p style="font-size:12px; color:#64748B; margin-top:2px;">Edit, add, or delete grade assessment criteria boxes.</p>
                    </div>
                    <button type="button" class="adm-btn adm-btn-secondary" id="btn-add-assess-card" style="font-size:12px; font-weight:600;">
                        + Add Assessment Stage Card
                    </button>
                </div>

                <div id="assess-cards-container" style="display:grid; gap:14px;">
                    <?php foreach ($assessCards as $idx => $card): ?>
                        <div class="repeater-row assess-card-row" style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:16px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <span class="adm-pill purple">Card #<?= $idx + 1 ?></span>
                                <button type="button" class="adm-btn adm-btn-secondary btn-remove-row" style="color:#EF4444; padding:3px 8px; font-size:12px;" title="Delete this card">✕ Delete</button>
                            </div>
                            <div class="adm-form-grid" style="margin-bottom:10px;">
                                <div class="adm-form-group">
                                    <label class="adm-label">Pill / Category Tag</label>
                                    <input type="text" name="assess_items[<?= $idx ?>][badge]" class="adm-input" value="<?= htmlspecialchars($card['lbl'] ?? '') ?>" placeholder="e.g. EARLY YEARS or STANDARD 1-8" required>
                                </div>
                                <div class="adm-form-group">
                                    <label class="adm-label">Class / Stage Title</label>
                                    <input type="text" name="assess_items[<?= $idx ?>][level]" class="adm-input" value="<?= htmlspecialchars($card['title'] ?? '') ?>" placeholder="e.g. Play Group and Nursery" required>
                                </div>
                            </div>
                            <div class="adm-form-group" style="margin-bottom:0;">
                                <label class="adm-label">Assessment Description (Direct Text)</label>
                                <textarea name="assess_items[<?= $idx ?>][body]" class="adm-textarea" rows="3" placeholder="Enter criteria details directly..."><?= htmlspecialchars($card['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="text-align:right; margin-top:20px;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    💾 Save Structured Assessment Criteria
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     2. ACADEMIC SESSIONS & REQUIRED DOCUMENTS (Marked in Image 3)
     ========================================================================= -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>📋 Section 2: Academic Session & Required Documents (Marked in Image 3)</span>
        </div>
        <span class="adm-pill green">Add • Edit • Delete</span>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="save_sessions_docs">

            <!-- Section Headings -->
            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">Section Title Base</label>
                    <input type="text" name="title_base" class="adm-input" value="<?= htmlspecialchars($admDocsSec['title_base'] ?? 'Academic Session & Required ') ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Title Highlight (Orange)</label>
                    <input type="text" name="title_highlight" class="adm-input" value="<?= htmlspecialchars($admDocsSec['title_highlight'] ?? 'Documents') ?>">
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Section Lead Paragraph</label>
                <textarea name="lead_text" class="adm-textarea" rows="2"><?= htmlspecialchars($admDocsSec['lead_text'] ?? '') ?></textarea>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:24px; margin-top:20px; border-top:1px dashed #E2E8F0; padding-top:20px;">
                
                <!-- Column 1: Academic Sessions & Intakes -->
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:18px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <h4 style="font-size:14px; font-weight:700; color:#25093B; margin:0;">Academic Sessions & Intakes</h4>
                            <span style="font-size:11.5px; color:#64748B;">Left Column Cards</span>
                        </div>
                        <button type="button" class="adm-btn adm-btn-secondary" id="btn-add-session-row" style="font-size:11.5px; font-weight:600;">
                            + Add Intake
                        </button>
                    </div>

                    <div id="sessions-container" style="display:grid; gap:10px;">
                        <?php foreach ($sessionItems as $sIdx => $sItem): ?>
                            <div class="repeater-row" style="background:#FFFFFF; border:1px solid #CBD5E1; border-radius:8px; padding:12px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                                    <label class="adm-label" style="margin-bottom:0; font-size:11px;">Intake Stream</label>
                                    <button type="button" class="adm-btn adm-btn-secondary btn-remove-row" style="color:#EF4444; padding:2px 6px; font-size:11px;" title="Delete intake">✕</button>
                                </div>
                                <div style="margin-bottom:8px;">
                                    <input type="text" name="sessions[<?= $sIdx ?>][stream]" class="adm-input" value="<?= htmlspecialchars($sItem['title'] ?? '') ?>" placeholder="e.g. Playgroup to Class 8" required>
                                </div>
                                <div>
                                    <label class="adm-label" style="font-size:11px;">Session Period / Cycle</label>
                                    <input type="text" name="sessions[<?= $sIdx ?>][period]" class="adm-input" value="<?= htmlspecialchars($sItem['subtitle'] ?? '') ?>" placeholder="e.g. July to June Annual Session">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Column 2: Mandatory Document Checklist -->
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:18px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <h4 style="font-size:14px; font-weight:700; color:#25093B; margin:0;">Mandatory Document Checklist</h4>
                            <span style="font-size:11.5px; color:#64748B;">Right Column Checklist</span>
                        </div>
                        <button type="button" class="adm-btn adm-btn-secondary" id="btn-add-doc-row" style="font-size:11.5px; font-weight:600;">
                            + Add Item
                        </button>
                    </div>

                    <div id="docs-container" style="display:grid; gap:10px;">
                        <?php foreach ($docChecklist as $dIdx => $dItem): ?>
                            <div class="repeater-row" style="background:#FFFFFF; border:1px solid #CBD5E1; border-radius:8px; padding:10px; display:flex; gap:8px; align-items:flex-start;">
                                <span style="color:#16A34A; font-weight:700; margin-top:6px;">✓</span>
                                <div style="flex:1;">
                                    <textarea name="documents[]" class="adm-textarea" rows="2" style="min-height:48px;" placeholder="Document requirement text..."><?= htmlspecialchars($dItem['title'] ?? '') ?></textarea>
                                </div>
                                <button type="button" class="adm-btn adm-btn-secondary btn-remove-row" style="color:#EF4444; padding:4px 8px; font-size:12px;" title="Delete item">✕</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <div style="text-align:right; margin-top:20px;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    💾 Save Sessions &amp; Documents
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     3. ADMISSIONS 7-STEP PROCESS TIMELINE
     ========================================================================= -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>📋 Section 3: 7-Step Admissions Process Roadmap</span>
        </div>
        <span class="adm-pill orange">Timeline Steps</span>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="save_timeline">

            <div style="display:grid; gap:14px;">
                <?php foreach ($steps as $s): ?>
                    <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:14px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span class="adm-pill purple"><?= htmlspecialchars($s['pill']) ?></span>
                            <span style="font-size:11.5px; color:#64748B;">Step #<?= $s['step_num'] ?></span>
                        </div>
                        <div style="margin-bottom:8px;">
                            <input type="text" name="timeline[<?= $s['id'] ?>][title]" class="adm-input" style="font-weight:600;" value="<?= htmlspecialchars($s['title']) ?>">
                        </div>
                        <div>
                            <textarea name="timeline[<?= $s['id'] ?>][desc]" class="adm-textarea" style="min-height:55px;"><?= htmlspecialchars($s['description']) ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:16px; text-align:right;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    💾 Save Timeline Steps
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     4. ADMISSIONS FAQS MANAGER
     ========================================================================= -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>❓ Section 4: Admissions Frequently Asked Questions (Accordion)</span>
        </div>
        <span class="adm-pill green"><?= count($faqs) ?> FAQs Active</span>
    </div>
    <div class="adm-card-body">
        <!-- Add New FAQ Form -->
        <div style="background:#FFF0E5; border:1px solid #FCD5C0; border-radius:8px; padding:18px; margin-bottom:24px;">
            <h4 style="font-size:14px; font-weight:700; color:#25093B; margin-bottom:12px;">+ Add New Question & Answer</h4>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="form_action" value="add_faq">

                <div class="adm-form-grid" style="margin-bottom:10px;">
                    <div class="adm-form-group">
                        <label class="adm-label">Question Category</label>
                        <select name="category" class="adm-input">
                            <option value="General">General Inquiries</option>
                            <option value="Hifz">Hifzul Quran & Dual Track</option>
                            <option value="Curriculum">Cambridge & Edexcel Curriculum</option>
                            <option value="Fees">Tuition & Admission Fees</option>
                            <option value="Transport">School Bus & Transport</option>
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">Question Text <span class="req">*</span></label>
                        <input type="text" name="question" class="adm-input" placeholder="e.g. What is the age criteria for Play Group admission?" required>
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Answer Description (Direct Text) <span class="req">*</span></label>
                    <textarea name="answer" class="adm-textarea" rows="3" placeholder="Provide a detailed, helpful answer..." required></textarea>
                </div>

                <div style="text-align:right;">
                    <button type="submit" class="adm-btn adm-btn-primary" style="padding:8px 16px;">
                        + Add Question to Accordion
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing FAQs List -->
        <h4 style="font-size:14px; font-weight:700; color:#25093B; margin-bottom:12px;">Active Admissions FAQs</h4>
        <div style="display:grid; gap:12px;">
            <?php foreach ($faqs as $f): ?>
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:14px; display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                            <span class="adm-pill purple" style="font-size:10.5px;"><?= htmlspecialchars($f['category'] ?? 'General') ?></span>
                            <span style="font-weight:700; color:var(--adm-primary); font-size:14px;"><?= htmlspecialchars($f['question']) ?></span>
                        </div>
                        <p style="font-size:13px; color:#475569; margin:0; line-height:1.6;"><?= nl2br(htmlspecialchars($f['answer'])) ?></p>
                    </div>
                    <form method="POST" action="" onsubmit="return confirm('Delete this FAQ permanently?');">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="form_action" value="delete_faq">
                        <input type="hidden" name="faq_id" value="<?= $f['id'] ?>">
                        <button type="submit" class="adm-btn adm-btn-secondary" style="color:#EF4444; padding:4px 8px; font-size:12px;" title="Delete FAQ">✕</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Dynamic Client-Side JS for Adding Rows -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Add Assessment Card
    var btnAddAssess = document.getElementById('btn-add-assess-card');
    var assessContainer = document.getElementById('assess-cards-container');
    if (btnAddAssess && assessContainer) {
        btnAddAssess.addEventListener('click', function() {
            var count = assessContainer.querySelectorAll('.assess-card-row').length + 1;
            var newCard = document.createElement('div');
            newCard.className = 'repeater-row assess-card-row';
            newCard.style.cssText = 'background:#FFF7ED; border:1px dashed #F58220; border-radius:10px; padding:16px; margin-top:10px;';
            newCard.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span class="adm-pill orange">New Card #${count}</span>
                    <button type="button" class="adm-btn adm-btn-secondary btn-remove-row" style="color:#EF4444; padding:3px 8px; font-size:12px;">✕ Delete</button>
                </div>
                <div class="adm-form-grid" style="margin-bottom:10px;">
                    <div class="adm-form-group">
                        <label class="adm-label">Pill / Category Tag</label>
                        <input type="text" name="assess_items[new_${Date.now()}][badge]" class="adm-input" placeholder="e.g. SENIOR SCHOOL" required>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">Class / Stage Title</label>
                        <input type="text" name="assess_items[new_${Date.now()}][level]" class="adm-input" placeholder="e.g. Advanced Placement Track" required>
                    </div>
                </div>
                <div class="adm-form-group" style="margin-bottom:0;">
                    <label class="adm-label">Assessment Description (Direct Text)</label>
                    <textarea name="assess_items[new_${Date.now()}][body]" class="adm-textarea" rows="3" placeholder="Enter criteria details directly..."></textarea>
                </div>
            `;
            assessContainer.appendChild(newCard);
        });
    }

    // 2. Add Academic Session Row
    var btnAddSess = document.getElementById('btn-add-session-row');
    var sessContainer = document.getElementById('sessions-container');
    if (btnAddSess && sessContainer) {
        btnAddSess.addEventListener('click', function() {
            var newSess = document.createElement('div');
            newSess.className = 'repeater-row';
            newSess.style.cssText = 'background:#FFF7ED; border:1px dashed #F58220; border-radius:8px; padding:12px; margin-top:8px;';
            newSess.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <label class="adm-label" style="margin-bottom:0; font-size:11px;">New Intake Stream</label>
                    <button type="button" class="adm-btn adm-btn-secondary btn-remove-row" style="color:#EF4444; padding:2px 6px; font-size:11px;">✕</button>
                </div>
                <div style="margin-bottom:8px;">
                    <input type="text" name="sessions[new_${Date.now()}][stream]" class="adm-input" placeholder="e.g. Mid-Year Special Intake" required>
                </div>
                <div>
                    <label class="adm-label" style="font-size:11px;">Session Period / Cycle</label>
                    <input type="text" name="sessions[new_${Date.now()}][period]" class="adm-input" placeholder="e.g. October / November Cycle">
                </div>
            `;
            sessContainer.appendChild(newSess);
        });
    }

    // 3. Add Document Checklist Item
    var btnAddDoc = document.getElementById('btn-add-doc-row');
    var docsContainer = document.getElementById('docs-container');
    if (btnAddDoc && docsContainer) {
        btnAddDoc.addEventListener('click', function() {
            var newDoc = document.createElement('div');
            newDoc.className = 'repeater-row';
            newDoc.style.cssText = 'background:#FFF7ED; border:1px dashed #F58220; border-radius:8px; padding:10px; display:flex; gap:8px; align-items:flex-start; margin-top:8px;';
            newDoc.innerHTML = `
                <span style="color:#F58220; font-weight:700; margin-top:6px;">✓</span>
                <div style="flex:1;">
                    <textarea name="documents[]" class="adm-textarea" rows="2" style="min-height:48px;" placeholder="Enter document requirement..."></textarea>
                </div>
                <button type="button" class="adm-btn adm-btn-secondary btn-remove-row" style="color:#EF4444; padding:4px 8px; font-size:12px;">✕</button>
            `;
            docsContainer.appendChild(newDoc);
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
