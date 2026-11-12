<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Signature Programs Builder (10+ Programs)
 * Manages: program.php?slug=... & Homepage "First Time in Bangladesh" Carousel
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Signature Programs Builder (10+ Offerings)';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// Handle Form Posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: programs.php');
        exit;
    }

    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'save_program') {
        $id = (int)($_POST['program_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $summary = trim($_POST['summary'] ?? '');
        $body = trim($_POST['body_html'] ?? '');
        $icon = resolve_image_path('icon_file', $_POST['icon'] ?? '', 'assets/img/cap.svg');
        $banner = resolve_image_path('banner_image_file', $_POST['banner_image'] ?? '', 'assets/img/Campus-1.jpg');
        $imageRow1 = resolve_image_path('image_row1_file', $_POST['image_row1'] ?? '', '');
        $imageRow2 = resolve_image_path('image_row2_file', $_POST['image_row2'] ?? '', '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = !empty($_POST['is_active']) ? 1 : 0;

        // Auto-slug if empty
        if ($slug === '' && $title !== '') {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        }

        // Process highlights as JSON array
        $highlightsRaw = trim($_POST['highlights_raw'] ?? '');
        $highlightsArr = [];
        if ($highlightsRaw !== '') {
            $lines = explode("\n", $highlightsRaw);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line !== '') $highlightsArr[] = $line;
            }
        }
        $highlightsJson = json_encode($highlightsArr, JSON_UNESCAPED_UNICODE);

        if ($title === '') {
            set_flash('error', 'Program Title is required.');
        } else {
            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE programs 
                        SET title = :title, slug = :slug, subtitle = :subtitle, summary = :summary, 
                            body_html = :body, highlights_json = :hl, icon = :icon, banner_image = :banner, 
                            image_row1 = :r1, image_row2 = :r2,
                            sort_order = :sort, is_active = :act
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        'title'    => $title,
                        'slug'     => $slug,
                        'subtitle' => $subtitle,
                        'summary'  => $summary,
                        'body'     => $body,
                        'hl'       => $highlightsJson,
                        'icon'     => $icon,
                        'banner'   => $banner,
                        'r1'       => $imageRow1,
                        'r2'       => $imageRow2,
                        'sort'     => $sortOrder,
                        'act'      => $isActive,
                        'id'       => $id,
                    ]);
                    set_flash('success', "Program '$title' updated successfully!");
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO programs (title, slug, subtitle, summary, body_html, highlights_json, icon, banner_image, image_row1, image_row2, link_url, sort_order, is_active)
                        VALUES (:title, :slug, :subtitle, :summary, :body, :hl, :icon, :banner, :r1, :r2, :link, :sort, :act)
                    ");
                    $linkUrl = "program.php?slug=$slug";
                    $stmt->execute([
                        'title'    => $title,
                        'slug'     => $slug,
                        'subtitle' => $subtitle,
                        'summary'  => $summary,
                        'body'     => $body,
                        'hl'       => $highlightsJson,
                        'icon'     => $icon,
                        'banner'   => $banner,
                        'r1'       => $imageRow1,
                        'r2'       => $imageRow2,
                        'link'     => $linkUrl,
                        'sort'     => $sortOrder,
                        'act'      => $isActive,
                    ]);
                    set_flash('success', "New Program '$title' created successfully!");
                }
                header('Location: programs.php');
                exit;
            } catch (Throwable $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }

    if ($formAction === 'delete_program') {
        $id = (int)($_POST['program_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare("DELETE FROM programs WHERE id = ?")->execute([$id]);
                set_flash('success', 'Program successfully deleted.');
            } catch (Throwable $e) {
                set_flash('error', 'Error deleting program: ' . $e->getMessage());
            }
        }
        header('Location: programs.php');
        exit;
    }
}

// Fetch program for editing
$editItem = null;
if ($action === 'edit' && $editId > 0 && $pdo !== null) {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ? LIMIT 1");
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all programs
$programs = [];
if ($pdo !== null) {
    try {
        $programs = $pdo->query("SELECT * FROM programs ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Programs Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'edit' || $action === 'new'): 
    $p = $editItem ?? [
        'id' => 0,
        'title' => '',
        'slug' => '',
        'subtitle' => '',
        'summary' => '',
        'body_html' => '',
        'highlights_json' => '[]',
        'icon' => 'assets/img/cap.svg',
        'banner_image' => 'assets/img/Campus-1.jpg',
        'image_row1' => '',
        'image_row2' => '',
        'sort_order' => count($programs) + 1,
        'is_active' => 1,
    ];
    $hlLines = '';
    if (!empty($p['highlights_json'])) {
        $decoded = json_decode($p['highlights_json'], true);
        if (is_array($decoded)) {
            $hlLines = implode("\n", $decoded);
        }
    }
?>
    <!-- Edit or Create Form -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
                <?= ($p['id'] > 0) ? 'Edit Program: ' . htmlspecialchars($p['title']) : '+ Add New Signature Program' ?>
            </h2>
            <p style="font-size:12.5px; color:#64748B;">Updates the program's dedicated page (program.php?slug=...) and navbar dropdown.</p>
        </div>
        <a href="programs.php" class="adm-btn adm-btn-secondary">
            &larr; Back to Program List
        </a>
    </div>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="form_action" value="save_program">
        <input type="hidden" name="program_id" value="<?= $p['id'] ?>">

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <span>Program Identity & Content</span>
                </div>
                <label style="font-size:12.5px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" <?= (!empty($p['is_active'])) ? 'checked' : '' ?>>
                    <span style="font-weight:600;">Active on Live Site</span>
                </label>
            </div>
            <div class="adm-card-body">
                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-label">Program Title <span class="req">*</span></label>
                        <input type="text" name="title" class="adm-input" value="<?= htmlspecialchars($p['title']) ?>" placeholder="e.g. Qira'at Competition" required>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">URL Slug (e.g. qiraat-competition)</label>
                        <input type="text" name="slug" class="adm-input" value="<?= htmlspecialchars($p['slug']) ?>" placeholder="auto-generated from title">
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">Display Sort Order</label>
                        <input type="number" name="sort_order" class="adm-input" value="<?= (int)$p['sort_order'] ?>">
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Subtitle / Key Descriptor</label>
                    <input type="text" name="subtitle" class="adm-input" value="<?= htmlspecialchars($p['subtitle'] ?? '') ?>" placeholder="e.g. Excellence in Recitation, Tajweed & Stage Presentation">
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Short Summary (Appears in Card / Sliders)</label>
                    <textarea name="summary" class="adm-textarea" rows="2"><?= htmlspecialchars($p['summary'] ?? '') ?></textarea>
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Detailed Body Content (HTML allowed)</label>
                    <textarea name="body_html" class="adm-textarea" rows="7" style="font-family:monospace;"><?= htmlspecialchars($p['body_html'] ?? '') ?></textarea>
                    <div class="adm-hint">You can use standard HTML tags like &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;.</div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Key Highlights (1 item per line)</label>
                    <textarea name="highlights_raw" class="adm-textarea" rows="4"><?= htmlspecialchars($hlLines) ?></textarea>
                    <div class="adm-hint">Each line will become a bullet point in the program highlights box.</div>
                </div>
            </div>
        </div>

        <!-- Program Images & Visual Assets -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <span>🖼️ Program Visual Media & Subpage Showcase Images</span>
                </div>
                <span class="adm-pill orange">Direct Uploads Supported</span>
            </div>
            <div class="adm-card-body">
                <div class="adm-form-grid">
                    <?= render_image_upload_field('Program Icon (SVG / PNG)', 'icon_file', 'icon', $p['icon'] ?? 'assets/img/cap.svg', 'Upload an SVG or high-res transparent PNG icon.') ?>
                    <?= render_image_upload_field('Program Banner Image', 'banner_image_file', 'banner_image', $p['banner_image'] ?? 'assets/img/Campus-1.jpg', 'Hero banner or campus landscape.') ?>
                </div>

                <div class="adm-form-grid" style="margin-top:10px;">
                    <?= render_image_upload_field('Subpage Section 1 Showcase Photo (Row 1)', 'image_row1_file', 'image_row1', $p['image_row1'] ?? '', 'Primary action photo displayed in the first section of the subpage.') ?>
                    <?= render_image_upload_field('Subpage Section 2 Showcase Photo (Row 2)', 'image_row2_file', 'image_row2', $p['image_row2'] ?? '', 'Secondary feature or community photo displayed in the second section.') ?>
                </div>
            </div>
        </div>

        <div style="text-align:right; margin-bottom:40px;">
            <button type="submit" class="adm-btn adm-btn-primary" style="padding:12px 24px;">
                💾 Save Signature Program
            </button>
        </div>
    </form>

<?php else: ?>
    <!-- Program List View -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
                All Signature Programs (<?= count($programs) ?> Offerings)
            </h2>
            <p style="font-size:12.5px; color:#64748B;">Manage the 10 core offerings. You can edit existing ones or add new programs dynamically.</p>
        </div>
        <a href="programs.php?action=new" class="adm-btn adm-btn-primary">
            + Add New Program
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:50px;">Sort</th>
                        <th>Program Title & Subtitle</th>
                        <th>Slug & URL</th>
                        <th>Status</th>
                        <th style="width:140px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $prog): ?>
                        <tr>
                            <td><strong><?= (int)$prog['sort_order'] ?></strong></td>
                            <td>
                                <strong style="font-size:14px; color:var(--adm-primary);"><?= htmlspecialchars($prog['title']) ?></strong>
                                <?php if (!empty($prog['subtitle'])): ?>
                                    <div style="font-size:12px; color:#64748B; margin-top:2px;"><?= htmlspecialchars($prog['subtitle']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code style="background:#F1F5F9; padding:2px 6px; border-radius:4px; font-size:12px; color:#0F172A;">
                                    <?= htmlspecialchars($prog['slug']) ?>
                                </code>
                            </td>
                            <td>
                                <?php if ($prog['is_active']): ?>
                                    <span class="adm-pill green">Active</span>
                                <?php else: ?>
                                    <span class="adm-pill red">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <a href="<?= base_url('program.php?slug=' . urlencode($prog['slug'])) ?>" target="_blank" class="adm-btn adm-btn-secondary adm-btn-sm" title="Preview live page">
                                        👁️
                                    </a>
                                    <a href="programs.php?action=edit&id=<?= $prog['id'] ?>" class="adm-btn adm-btn-secondary adm-btn-sm" title="Edit program">
                                        ✏️ Edit
                                    </a>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="form_action" value="delete_program">
                                        <input type="hidden" name="program_id" value="<?= $prog['id'] ?>">
                                        <button type="submit" class="adm-btn adm-btn-secondary adm-btn-sm btn-confirm-delete" data-title="<?= htmlspecialchars($prog['title']) ?>" style="color:#EF4444;" title="Delete program">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
