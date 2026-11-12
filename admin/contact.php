<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Contact Us & Campuses Geolocation Manager
 * Manages: contact.php
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Contact Us & Campus Geolocation Manager';

// Handle Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: contact.php');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Update Contact Hero
        if (isset($_POST['hero'])) {
            $h = $_POST['hero'];
            $heroImg = resolve_image_path('hero_image_file', $h['image'] ?? '', 'img/telephone-hero.jpg');

            $stmt = $pdo->prepare("
                UPDATE subpage_contents
                SET eyebrow = :eyebrow, title_base = :tbase, title_highlight = :thigh, lead_text = :lead, image = :img
                WHERE page_key = 'contact' AND section_key = 'hero'
            ");
            $stmt->execute([
                'eyebrow' => trim($h['eyebrow'] ?? ''),
                'tbase'   => trim($h['title_base'] ?? ''),
                'thigh'   => trim($h['title_highlight'] ?? ''),
                'lead'    => trim($h['lead_text'] ?? ''),
                'img'     => $heroImg,
            ]);

            // Quick contacts
            if (isset($_POST['quick']) && is_array($_POST['quick'])) {
                $pdo->prepare("DELETE FROM subpage_items WHERE page_key = 'contact' AND section_key = 'hero'")->execute();
                $heroId = (int)$pdo->query("SELECT id FROM subpage_contents WHERE page_key = 'contact' AND section_key = 'hero'")->fetchColumn();

                $qStmt = $pdo->prepare("INSERT INTO subpage_items (content_id, page_key, section_key, item_type, lbl, val, link, icon, sort_order, is_active) VALUES (?, 'contact', 'hero', 'quick_contact', ?, ?, ?, ?, ?, 1)");
                $qSort = 1;
                foreach ($_POST['quick'] as $qc) {
                    if (!empty(trim($qc['val'] ?? ''))) {
                        $qStmt->execute([$heroId, trim($qc['lbl'] ?? ''), trim($qc['val']), trim($qc['link'] ?? ''), trim($qc['icon'] ?? 'phone'), $qSort++]);
                    }
                }
            }
        }

        // 2. Update Campus Cards
        if (isset($_POST['campuses']) && is_array($_POST['campuses'])) {
            foreach ($_POST['campuses'] as $cId => $cData) {
                $cStmt = $pdo->prepare("
                    UPDATE campuses
                    SET name = :name, address = :addr, phone = :phone, email = :email, map_url = :map
                    WHERE id = :id
                ");
                $cStmt->execute([
                    'name'  => trim($cData['name'] ?? ''),
                    'addr'  => trim($cData['address'] ?? ''),
                    'phone' => trim($cData['phone'] ?? ''),
                    'email' => trim($cData['email'] ?? ''),
                    'map'   => trim($cData['map_url'] ?? '#'),
                    'id'    => (int)$cId,
                ]);
            }
        }

        $pdo->commit();
        set_flash('success', 'Contact page & branch coordinates saved successfully!');
    } catch (Throwable $e) {
        $pdo->rollBack();
        set_flash('error', 'Update error: ' . $e->getMessage());
    }

    header('Location: contact.php');
    exit;
}

// Fetch live data
$hero = [];
$quickContacts = [];
$campuses = [];

if ($pdo !== null) {
    try {
        $hero = $pdo->query("SELECT * FROM subpage_contents WHERE page_key = 'contact' AND section_key = 'hero' LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        $quickContacts = $pdo->query("SELECT * FROM subpage_items WHERE page_key = 'contact' AND section_key = 'hero' ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
        $campuses = $pdo->query("SELECT * FROM campuses ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Contact Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Contact Page & Campus Coordinates Manager
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Manage contact headlines, quick helplines, campus cards, and map links.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="<?= base_url('contact.php') ?>" target="_blank" class="adm-btn adm-btn-secondary">
            👁️ View Public Contact Page
        </a>
    </div>
</div>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <!-- 1. Hero & Helplines -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <span>Section 0: Contact Us Banner & Quick Helplines</span>
            </div>
            <span class="adm-pill orange">Hero</span>
        </div>
        <div class="adm-card-body">
            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">Eyebrow Tagline</label>
                    <input type="text" name="hero[eyebrow]" class="adm-input" value="<?= htmlspecialchars($hero['eyebrow'] ?? '') ?>" placeholder="e.g. CONNECT WITH AVERROES">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Title Normal Text</label>
                    <input type="text" name="hero[title_base]" class="adm-input" value="<?= htmlspecialchars($hero['title_base'] ?? "We'd Love to ") ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Title Highlight (Orange)</label>
                    <input type="text" name="hero[title_highlight]" class="adm-input" value="<?= htmlspecialchars($hero['title_highlight'] ?? 'Hear From You') ?>">
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Introductory Narrative</label>
                <textarea name="hero[lead_text]" class="adm-textarea" rows="3"><?= htmlspecialchars($hero['lead_text'] ?? '') ?></textarea>
            </div>

            <div style="margin-top:14px;">
                <?= render_image_upload_field('Contact Visual / 3D Telephone Illustration', 'hero_image_file', 'hero[image]', $hero['image'] ?? 'img/telephone-hero.jpg', 'Upload a photo or 3D rotary telephone graphic directly from your computer.') ?>
            </div>

            <!-- Quick Helplines -->
            <div style="margin-top:16px; border-top:1px dashed #E2E8F0; padding-top:16px;">
                <label class="adm-label">Quick Helplines (Phone, WhatsApp, Email)</label>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:14px;">
                    <?php 
                    $icons = ['phone', 'whatsapp', 'email'];
                    for ($k = 0; $k < 3; $k++): 
                        $qc = $quickContacts[$k] ?? ['lbl' => '', 'val' => '', 'link' => '', 'icon' => $icons[$k]];
                    ?>
                        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:12px;">
                            <input type="hidden" name="quick[<?= $k ?>][icon]" value="<?= htmlspecialchars($qc['icon'] ?? $icons[$k]) ?>">
                            <div style="margin-bottom:6px;">
                                <label style="font-size:11px; font-weight:700; color:#64748B;">Badge Label</label>
                                <input type="text" name="quick[<?= $k ?>][lbl]" class="adm-input" value="<?= htmlspecialchars($qc['lbl'] ?? '') ?>" placeholder="e.g. Direct Helpline">
                            </div>
                            <div style="margin-bottom:6px;">
                                <label style="font-size:11px; font-weight:700; color:#64748B;">Display Value / Number</label>
                                <input type="text" name="quick[<?= $k ?>][val]" class="adm-input" value="<?= htmlspecialchars($qc['val'] ?? '') ?>" placeholder="+880 1954-123 123">
                            </div>
                            <div>
                                <label style="font-size:11px; font-weight:700; color:#64748B;">Target Action Link</label>
                                <input type="text" name="quick[<?= $k ?>][link]" class="adm-input" value="<?= htmlspecialchars($qc['link'] ?? '') ?>" placeholder="tel:+8801954123123 or https://wa.me/...">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Dual Campus Cards (Lalmatia & Banasree) -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <span>🏫 Campus Branch Information & Google Maps Link</span>
            </div>
            <span class="adm-pill purple">2 Campuses</span>
        </div>
        <div class="adm-card-body">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap:20px;">
                <?php foreach ($campuses as $cmp): ?>
                    <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:16px;">
                        <h4 style="font-size:15px; font-weight:700; color:var(--adm-primary); margin-bottom:12px;">
                            <?= htmlspecialchars($cmp['name']) ?>
                        </h4>

                        <div class="adm-form-group">
                            <label class="adm-label">Branch Name</label>
                            <input type="text" name="campuses[<?= $cmp['id'] ?>][name]" class="adm-input" value="<?= htmlspecialchars($cmp['name']) ?>">
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-label">Campus Physical Address</label>
                            <textarea name="campuses[<?= $cmp['id'] ?>][address]" class="adm-textarea" rows="2"><?= htmlspecialchars($cmp['address']) ?></textarea>
                        </div>

                        <div class="adm-form-grid">
                            <div class="adm-form-group">
                                <label class="adm-label">Helpline Phone Numbers</label>
                                <textarea name="campuses[<?= $cmp['id'] ?>][phone]" class="adm-textarea" rows="2"><?= htmlspecialchars($cmp['phone']) ?></textarea>
                            </div>
                            <div class="adm-form-group">
                                <label class="adm-label">Official Email</label>
                                <input type="email" name="campuses[<?= $cmp['id'] ?>][email]" class="adm-input" value="<?= htmlspecialchars($cmp['email']) ?>">
                            </div>
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-label">Google Maps Link (Opened on "Open in Google Map" Button)</label>
                            <input type="text" name="campuses[<?= $cmp['id'] ?>][map_url]" class="adm-input" value="<?= htmlspecialchars($cmp['map_url']) ?>" placeholder="https://maps.google.com/...">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div style="text-align:right; margin-bottom:40px;">
        <button type="submit" class="adm-btn adm-btn-primary" style="padding:12px 24px;">
            💾 Save Contact & Campus Details
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
