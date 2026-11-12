<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Homepage Manager
 * Manages: index.php (Hero video, Why summary, Counter Stats)
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Homepage Content & Stats Manager';

// Handle Form Posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: home.php');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Hero Video URL in settings
        if (isset($_POST['hero_video_url'])) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = :val WHERE setting_key = 'hero_video_url'");
            $stmt->execute(['val' => trim($_POST['hero_video_url'])]);
        }

        // 2. Sections (Why Averroes summary)
        if (isset($_POST['sections']['why'])) {
            $why = $_POST['sections']['why'];
            $whyImg = resolve_image_path('why_image_file', $why['image'] ?? '', 'assets/img/rocket.svg');

            $stmt = $pdo->prepare("
                UPDATE sections 
                SET heading_plain = :hp, heading_em = :he, body = :body, button_text = :btn, button_url = :url, image = :img
                WHERE section_key = 'why'
            ");
            $stmt->execute([
                'hp'   => trim($why['heading_plain'] ?? 'Why '),
                'he'   => trim($why['heading_em'] ?? 'Averroes?'),
                'body' => trim($why['body'] ?? ''),
                'btn'  => trim($why['button_text'] ?? 'Read More'),
                'url'  => trim($why['button_url'] ?? 'why-averroes.php'),
                'img'  => $whyImg,
            ]);
        }

        // 3. Counter Statistics
        if (isset($_POST['stats']) && is_array($_POST['stats'])) {
            $statStmt = $pdo->prepare("UPDATE stats SET display_value = :val, count_to = :cnt, suffix = :sfx, label = :lbl WHERE id = :id");
            foreach ($_POST['stats'] as $sId => $st) {
                $statStmt->execute([
                    'val' => trim($st['val'] ?? ''),
                    'cnt' => trim($st['cnt'] ?? ''),
                    'sfx' => trim($st['sfx'] ?? ''),
                    'lbl' => trim($st['lbl'] ?? ''),
                    'id'  => (int)$sId,
                ]);
            }
        }

        $pdo->commit();
        set_flash('success', 'Homepage content & counters updated successfully!');
    } catch (Throwable $e) {
        $pdo->rollBack();
        set_flash('error', 'Update error: ' . $e->getMessage());
    }

    header('Location: home.php');
    exit;
}

// Fetch live homepage elements
$heroVideo = '';
$whySec = [];
$stats = [];

if ($pdo !== null) {
    try {
        $heroVideo = (string)$pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'hero_video_url'")->fetchColumn();
        $whySec = $pdo->query("SELECT * FROM sections WHERE section_key = 'why'")->fetch(PDO::FETCH_ASSOC) ?: [];
        $stats = $pdo->query("SELECT * FROM stats ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Home Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Homepage Content & Counter Statistics
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Manage the hero video embed, Why Averroes summary card, and live animated counters.</p>
    </div>
    <a href="<?= base_url() ?>" target="_blank" class="adm-btn adm-btn-secondary">
        👁️ View Public Homepage
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <!-- 1. Hero Video Embed -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <span>🎥 Hero Section Video Embed</span>
            </div>
            <span class="adm-pill orange">Hero Background</span>
        </div>
        <div class="adm-card-body">
            <div class="adm-form-group">
                <label class="adm-label">YouTube / Vimeo Embed URL</label>
                <input type="text" name="hero_video_url" class="adm-input" value="<?= htmlspecialchars($heroVideo) ?>" placeholder="https://youtu.be/...">
                <div class="adm-hint">This video plays inside the hero media presentation container on index.php.</div>
            </div>
        </div>
    </div>

    <!-- 2. Animated Counter Statistics (4 Counters) -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <span>🔢 Animated Counter Metrics (4 Counters)</span>
            </div>
            <span class="adm-pill green">Live Stats</span>
        </div>
        <div class="adm-card-body">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
                <?php foreach ($stats as $st): ?>
                    <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:14px;">
                        <div style="font-size:11px; font-weight:700; color:#64748B; margin-bottom:6px;">Counter #<?= $st['id'] ?></div>
                        <div class="adm-form-group" style="margin-bottom:8px;">
                            <label class="adm-label">Display Value</label>
                            <input type="text" name="stats[<?= $st['id'] ?>][val]" class="adm-input" value="<?= htmlspecialchars($st['display_value']) ?>" placeholder="e.g. 275+">
                        </div>
                        <div class="adm-form-grid" style="grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                            <div>
                                <label class="adm-label">Count Target</label>
                                <input type="text" name="stats[<?= $st['id'] ?>][cnt]" class="adm-input" value="<?= htmlspecialchars($st['count_to'] ?? '') ?>" placeholder="275">
                            </div>
                            <div>
                                <label class="adm-label">Suffix</label>
                                <input type="text" name="stats[<?= $st['id'] ?>][sfx]" class="adm-input" value="<?= htmlspecialchars($st['suffix'] ?? '') ?>" placeholder="+ or %">
                            </div>
                        </div>
                        <div>
                            <label class="adm-label">Metric Label</label>
                            <input type="text" name="stats[<?= $st['id'] ?>][lbl]" class="adm-input" value="<?= htmlspecialchars($st['label']) ?>" placeholder="e.g. Teachers">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 3. Why Averroes Summary Block -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <span>📖 Homepage "Why Averroes?" Summary Block</span>
            </div>
        </div>
        <div class="adm-card-body">
            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">Heading Plain Text</label>
                    <input type="text" name="sections[why][heading_plain]" class="adm-input" value="<?= htmlspecialchars($whySec['heading_plain'] ?? 'Why ') ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Heading Emphasized (Orange)</label>
                    <input type="text" name="sections[why][heading_em]" class="adm-input" value="<?= htmlspecialchars($whySec['heading_em'] ?? 'Averroes?') ?>">
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Summary Narrative Copy</label>
                <textarea name="sections[why][body]" class="adm-textarea" rows="4"><?= htmlspecialchars($whySec['body'] ?? '') ?></textarea>
            </div>

            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">CTA Button Text</label>
                    <input type="text" name="sections[why][button_text]" class="adm-input" value="<?= htmlspecialchars($whySec['button_text'] ?? 'Read More') ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">CTA Button Link</label>
                    <input type="text" name="sections[why][button_url]" class="adm-input" value="<?= htmlspecialchars($whySec['button_url'] ?? 'why-averroes.php') ?>">
                </div>
            </div>

            <div style="margin-top:14px; border-top:1px dashed #E2E8F0; padding-top:14px;">
                <?= render_image_upload_field('Section Illustration / Graphic', 'why_image_file', 'sections[why][image]', $whySec['image'] ?? 'assets/img/rocket.svg', 'Upload an illustration (e.g. rocket.svg) or photo directly from your PC.') ?>
            </div>
        </div>
    </div>

    <div style="text-align:right; margin-bottom:40px;">
        <button type="submit" class="adm-btn adm-btn-primary" style="padding:12px 24px;">
            💾 Save Homepage Settings
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
