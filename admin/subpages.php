<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Universal Subpage Content & Block Editor
 * Manages: 'why_averroes' (why-averroes.php) & 'life_at_averroes' (life-at-averroes.php)
 */

require_once __DIR__ . '/includes/auth.php';

$pageSlug = $_GET['page'] ?? 'why_averroes';
if ($pageSlug !== 'life_at_averroes') {
    $pageSlug = 'why_averroes';
}

$pageTitles = [
    'why_averroes' => 'Why Averroes Subpage Editor',
    'life_at_averroes' => 'Life at Averroes Subpage Editor',
];

$liveUrls = [
    'why_averroes' => base_url('why-averroes.php'),
    'life_at_averroes' => base_url('life-at-averroes.php'),
];

$pageTitle = $pageTitles[$pageSlug];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF). Please refresh.');
        header("Location: subpages.php?page=$pageSlug");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Update Hero Section
        if (isset($_POST['hero'])) {
            $hero = $_POST['hero'];
            $heroImage = resolve_image_path('hero_image_file', $hero['image'] ?? '', '');

            $stmt = $pdo->prepare("
                UPDATE subpage_contents 
                SET eyebrow = :eyebrow, title_base = :title, lead_text = :lead, image = :img, image_alt = :alt
                WHERE page_key = :page AND section_key = 'hero'
            ");
            $stmt->execute([
                'eyebrow' => trim($hero['eyebrow'] ?? ''),
                'title'   => trim($hero['title'] ?? ''),
                'lead'    => trim($hero['lead'] ?? ''),
                'img'     => $heroImage,
                'alt'     => trim($hero['image_alt'] ?? ''),
                'page'    => $pageSlug,
            ]);

            // Hero metrics
            if (isset($_POST['hero_metrics']) && is_array($_POST['hero_metrics'])) {
                $heroId = (int)$pdo->query("SELECT id FROM subpage_contents WHERE page_key = '$pageSlug' AND section_key = 'hero'")->fetchColumn();
                $pdo->prepare("DELETE FROM subpage_items WHERE page_key = ? AND section_key = 'hero'")->execute([$pageSlug]);

                $mStmt = $pdo->prepare("INSERT INTO subpage_items (content_id, page_key, section_key, item_type, val, lbl, sort_order, is_active) VALUES (?, ?, 'hero', 'metric', ?, ?, ?, 1)");
                $mSort = 1;
                foreach ($_POST['hero_metrics'] as $m) {
                    if (!empty(trim($m['val'] ?? ''))) {
                        $mStmt->execute([$heroId, $pageSlug, trim($m['val']), trim($m['lbl'] ?? ''), $mSort++]);
                    }
                }
            }
        }

        // 2. Update Content Sections
        if (isset($_POST['sections']) && is_array($_POST['sections'])) {
            foreach ($_POST['sections'] as $sKey => $sData) {
                $secFileKey = 'sec_image_file_' . preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$sKey);
                $secImage = resolve_image_path($secFileKey, $sData['image'] ?? '', '');

                $secStmt = $pdo->prepare("
                    UPDATE subpage_contents
                    SET title_base = :tbase, title_highlight = :thigh, lead_text = :lead, image = :img, image_alt = :alt, layout_mode = :layout
                    WHERE page_key = :page AND section_key = :skey
                ");
                $secStmt->execute([
                    'tbase'   => trim($sData['title_base'] ?? ''),
                    'thigh'   => trim($sData['title_highlight'] ?? ''),
                    'lead'    => trim($sData['lead_text'] ?? ''),
                    'img'     => $secImage,
                    'alt'     => trim($sData['image_alt'] ?? ''),
                    'layout'  => !empty($sData['reverse']) ? 'reverse' : 'normal',
                    'page'    => $pageSlug,
                    'skey'    => $sKey,
                ]);

                // Update child items
                if (isset($sData['items']) && is_array($sData['items'])) {
                    $secId = (int)$pdo->query("SELECT id FROM subpage_contents WHERE page_key = '$pageSlug' AND section_key = " . $pdo->quote($sKey))->fetchColumn();
                    $pdo->prepare("DELETE FROM subpage_items WHERE page_key = ? AND section_key = ?")->execute([$pageSlug, $sKey]);

                    $itemStmt = $pdo->prepare("INSERT INTO subpage_items (content_id, page_key, section_key, item_type, title, description, sort_order, is_active) VALUES (?, ?, ?, 'bullet', ?, ?, ?, 1)");
                    $iSort = 1;
                    foreach ($sData['items'] as $it) {
                        if (!empty(trim($it['title'] ?? ''))) {
                            $itemStmt->execute([$secId, $pageSlug, $sKey, trim($it['title']), trim($it['desc'] ?? ''), $iSort++]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
        set_flash('success', 'Subpage content successfully updated!');
        header("Location: subpages.php?page=$pageSlug");
        exit;
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('[Subpages Update Error] ' . $e->getMessage());
        set_flash('error', 'Error updating content: ' . $e->getMessage());
        header("Location: subpages.php?page=$pageSlug");
        exit;
    }
}

// Fetch live contents from database
$contents = [];
$itemsBySec = [];

if ($pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM subpage_contents WHERE page_key = :page ORDER BY sort_order ASC, id ASC");
        $stmt->execute(['page' => $pageSlug]);
        $rawContents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rawContents as $rc) {
            $contents[$rc['section_key']] = $rc;
        }

        $itemsStmt = $pdo->prepare("SELECT * FROM subpage_items WHERE page_key = :page ORDER BY sort_order ASC, id ASC");
        $itemsStmt->execute(['page' => $pageSlug]);
        $rawItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rawItems as $ri) {
            $itemsBySec[$ri['section_key']][] = $ri;
        }
    } catch (Throwable $e) {
        error_log('[Subpages Fetch] ' . $e->getMessage());
    }
}

$hero = $contents['hero'] ?? [];
$heroMetrics = $itemsBySec['hero'] ?? [];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Subpage Navigation Tabs -->
<div class="adm-tabs">
    <a href="subpages.php?page=why_averroes" class="adm-tab-link <?= ($pageSlug === 'why_averroes') ? 'active' : '' ?>">
        🎓 Why Averroes Subpage
    </a>
    <a href="subpages.php?page=life_at_averroes" class="adm-tab-link <?= ($pageSlug === 'life_at_averroes') ? 'active' : '' ?>">
        🌟 Life at Averroes Subpage
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <!-- Top Action Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
                <?= htmlspecialchars($pageTitle) ?>
            </h2>
            <p style="font-size:12.5px; color:#64748B;">All content and media below update directly on the live website.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="<?= $liveUrls[$pageSlug] ?>" target="_blank" class="adm-btn adm-btn-secondary">
                👁️ View Live Page
            </a>
            <button type="submit" class="adm-btn adm-btn-primary">
                💾 Save All Changes
            </button>
        </div>
    </div>

    <!-- 1. Hero Section -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <span>Section 0: Subpage Hero Banner</span>
            </div>
            <span class="adm-pill orange">Hero</span>
        </div>
        <div class="adm-card-body">
            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">Eyebrow Tagline</label>
                    <input type="text" name="hero[eyebrow]" class="adm-input" value="<?= htmlspecialchars($hero['eyebrow'] ?? '') ?>" placeholder="e.g. WHY CHOOSE AVERROES">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Main Heading <span class="req">*</span></label>
                    <input type="text" name="hero[title]" class="adm-input" value="<?= htmlspecialchars($hero['title_base'] ?? '') ?>" required>
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Lead Narrative Paragraph</label>
                <textarea name="hero[lead]" class="adm-textarea" rows="3"><?= htmlspecialchars($hero['lead_text'] ?? '') ?></textarea>
            </div>

            <?= render_image_upload_field('Hero Showcase Photo', 'hero_image_file', 'hero[image]', $hero['image'] ?? '', 'Upload directly from your computer or specify an image asset path.') ?>

            <div class="adm-form-group" style="margin-top:10px;">
                <label class="adm-label">Image Alt Text (Accessibility)</label>
                <input type="text" name="hero[image_alt]" class="adm-input" value="<?= htmlspecialchars($hero['image_alt'] ?? '') ?>">
            </div>

            <!-- Hero Metrics -->
            <div style="margin-top:16px; border-top:1px dashed #E2E8F0; padding-top:16px;">
                <label class="adm-label" style="margin-bottom:10px;">Hero Metric Highlight Boxes (3 Boxes)</label>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
                    <?php for ($m = 0; $m < 3; $m++): 
                        $met = $heroMetrics[$m] ?? ['val' => '', 'lbl' => ''];
                    ?>
                        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:12px;">
                            <div style="font-size:11px; font-weight:700; color:#64748B; margin-bottom:6px;">Metric Box #<?= $m + 1 ?></div>
                            <div style="margin-bottom:8px;">
                                <input type="text" name="hero_metrics[<?= $m ?>][val]" class="adm-input" placeholder="Value (e.g. 100% or 5.5h)" value="<?= htmlspecialchars($met['val'] ?? '') ?>">
                            </div>
                            <div>
                                <input type="text" name="hero_metrics[<?= $m ?>][lbl]" class="adm-input" placeholder="Label (e.g. Pass Rate)" value="<?= htmlspecialchars($met['lbl'] ?? '') ?>">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Content Sections -->
    <?php foreach ($contents as $secKey => $sec): 
        if ($secKey === 'hero') continue;
        $items = $itemsBySec[$secKey] ?? [];
    ?>
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <span>Section: <?= htmlspecialchars($sec['title_base']) ?> <span style="color:#F58220;"><?= htmlspecialchars($sec['title_highlight'] ?? '') ?></span></span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <label style="font-size:12px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" name="sections[<?= $secKey ?>][reverse]" value="1" <?= ($sec['layout_mode'] === 'reverse') ? 'checked' : '' ?>>
                        <span>Reverse (Image Left)</span>
                    </label>
                    <span class="adm-pill blue"><?= htmlspecialchars($sec['type']) ?></span>
                </div>
            </div>

            <div class="adm-card-body">
                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-label">Title Prefix (Normal Text)</label>
                        <input type="text" name="sections[<?= $secKey ?>][title_base]" class="adm-input" value="<?= htmlspecialchars($sec['title_base'] ?? '') ?>">
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">Title Highlight (Orange Gradient)</label>
                        <input type="text" name="sections[<?= $secKey ?>][title_highlight]" class="adm-input" value="<?= htmlspecialchars($sec['title_highlight'] ?? '') ?>">
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Section Narrative / Lead Paragraph</label>
                    <textarea name="sections[<?= $secKey ?>][lead_text]" class="adm-textarea" rows="2"><?= htmlspecialchars($sec['lead_text'] ?? '') ?></textarea>
                </div>

                <?= render_image_upload_field('Section Showcase Photo', 'sec_image_file_' . preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$secKey), "sections[$secKey][image]", $sec['image'] ?? '', 'Upload directly from your computer or specify an image asset path.') ?>

                <div class="adm-form-group" style="margin-top:10px;">
                    <label class="adm-label">Image Description (Alt Text)</label>
                    <input type="text" name="sections[<?= $secKey ?>][image_alt]" class="adm-input" value="<?= htmlspecialchars($sec['image_alt'] ?? '') ?>">
                </div>

                <!-- Section Items Repeater -->
                <div style="margin-top:16px; border-top:1px dashed #E2E8F0; padding-top:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <label class="adm-label" style="margin-bottom:0;">Bullet Points / Feature Cards</label>
                    </div>

                    <div id="items-<?= $secKey ?>">
                        <?php foreach ($items as $idx => $it): ?>
                            <div class="repeater-row" style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:12px; margin-bottom:10px;">
                                <div style="display:flex; gap:10px; margin-bottom:8px;">
                                    <input type="text" name="sections[<?= $secKey ?>][items][<?= $idx ?>][title]" class="adm-input" style="font-weight:600;" placeholder="Item Title / Heading" value="<?= htmlspecialchars($it['title'] ?? '') ?>">
                                    <button type="button" class="adm-btn adm-btn-secondary btn-remove-row" style="color:#EF4444;" title="Remove this point">✕</button>
                                </div>
                                <div>
                                    <textarea name="sections[<?= $secKey ?>][items][<?= $idx ?>][desc]" class="adm-textarea" style="min-height:55px;" placeholder="Detailed description"><?= htmlspecialchars($it['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Bottom Save Button -->
    <div style="display:flex; justify-content:flex-end; margin-top:20px; margin-bottom:40px;">
        <button type="submit" class="adm-btn adm-btn-primary" style="padding:12px 24px; font-size:14px;">
            💾 Save All Subpage Changes
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
