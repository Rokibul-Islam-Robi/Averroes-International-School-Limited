<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Accreditation Partners Manager
 * Manages: partners table (Cambridge, Edexcel, British Council, MOE, etc.)
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Accreditation Partners & Affiliations';

// Handle Form Posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: partners.php');
        exit;
    }

    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'add_partner') {
        $name = trim($_POST['name'] ?? '');
        $cssClass = trim($_POST['css_class'] ?? 'p-gen');
        $labelHtml = trim($_POST['label_html'] ?? '');

        if ($name !== '' && $labelHtml !== '') {
            try {
                $maxSort = (int)$pdo->query("SELECT MAX(sort_order) FROM partners")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO partners (name, css_class, label_html, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$name, $cssClass, $labelHtml, $maxSort + 1]);
                set_flash('success', "Partner '$name' added!");
            } catch (Throwable $e) {
                set_flash('error', 'Error: ' . $e->getMessage());
            }
        }
        header('Location: partners.php');
        exit;
    }

    if ($formAction === 'save_partners' && isset($_POST['partners']) && is_array($_POST['partners'])) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE partners SET name = :n, label_html = :l, sort_order = :s, is_active = :a WHERE id = :id");
            foreach ($_POST['partners'] as $id => $p) {
                $stmt->execute([
                    'n'  => trim($p['name'] ?? ''),
                    'l'  => trim($p['label_html'] ?? ''),
                    's'  => (int)($p['sort_order'] ?? 0),
                    'a'  => !empty($p['is_active']) ? 1 : 0,
                    'id' => (int)$id,
                ]);
            }
            $pdo->commit();
            set_flash('success', 'Partner affiliations updated!');
        } catch (Throwable $e) {
            $pdo->rollBack();
            set_flash('error', 'Error: ' . $e->getMessage());
        }
        header('Location: partners.php');
        exit;
    }

    if ($formAction === 'delete_partner') {
        $id = (int)($_POST['partner_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare("DELETE FROM partners WHERE id = ?")->execute([$id]);
                set_flash('success', 'Partner removed.');
            } catch (Throwable $e) {
                set_flash('error', 'Error: ' . $e->getMessage());
            }
        }
        header('Location: partners.php');
        exit;
    }
}

// Fetch partners
$partners = [];
if ($pdo !== null) {
    try {
        $partners = $pdo->query("SELECT * FROM partners ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Partners Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Accreditation & International Partner Logos
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Manages the institutional partner ribbon displayed above the homepage footer.</p>
    </div>
</div>

<!-- Add Partner Card -->
<div class="adm-card" style="background:#FFF0E5; border-color:#FCD5C0;">
    <div class="adm-card-body" style="padding:16px 20px;">
        <h4 style="font-size:14px; font-weight:700; color:var(--adm-primary); margin-bottom:10px;">+ Add New Partner Logo</h4>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="add_partner">

            <div class="adm-form-grid" style="grid-template-columns: 2fr 1fr 3fr auto; align-items:flex-end; gap:12px;">
                <div>
                    <label class="adm-label">Partner Name <span class="req">*</span></label>
                    <input type="text" name="name" class="adm-input" placeholder="e.g. Edexcel" required>
                </div>
                <div>
                    <label class="adm-label">CSS Identifier</label>
                    <input type="text" name="css_class" class="adm-input" placeholder="p-edx" value="p-brand">
                </div>
                <div>
                    <label class="adm-label">Image HTML / SVG Tag <span class="req">*</span></label>
                    <input type="text" name="label_html" class="adm-input" placeholder='<img src="assets/img/partners/..." alt="..." class="partner-img">' required>
                </div>
                <div>
                    <button type="submit" class="adm-btn adm-btn-primary" style="height:38px;">+ Add Partner</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Existing Partners -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title"><span>Accredited Partners (<?= count($partners) ?>)</span></div>
    </div>
    <div class="adm-card-body" style="padding:0;">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="save_partners">

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">Sort</th>
                            <th>Partner Name</th>
                            <th>Logo HTML / Image Tag</th>
                            <th style="width:90px;">Active</th>
                            <th style="width:80px; text-align:right;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partners as $p): ?>
                            <tr>
                                <td>
                                    <input type="number" name="partners[<?= $p['id'] ?>][sort_order]" class="adm-input" style="width:60px; padding:4px 6px;" value="<?= (int)$p['sort_order'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="partners[<?= $p['id'] ?>][name]" class="adm-input" value="<?= htmlspecialchars($p['name']) ?>">
                                </td>
                                <td>
                                    <input type="text" name="partners[<?= $p['id'] ?>][label_html]" class="adm-input" value="<?= htmlspecialchars($p['label_html']) ?>">
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" name="partners[<?= $p['id'] ?>][is_active]" value="1" <?= (!empty($p['is_active'])) ? 'checked' : '' ?>>
                                </td>
                                <td style="text-align:right;">
                                    <button type="submit" form="del-p-<?= $p['id'] ?>" class="adm-btn adm-btn-secondary adm-btn-sm btn-confirm-delete" data-title="<?= htmlspecialchars($p['name']) ?>" style="color:#EF4444;">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px; text-align:right; background:#F8FAFC; border-top:1px solid #E2E8F0;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    💾 Save Partner Logos
                </button>
            </div>
        </form>

        <!-- Hidden delete forms -->
        <?php foreach ($partners as $p): ?>
            <form id="del-p-<?= $p['id'] ?>" method="POST" action="" style="display:none;">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="form_action" value="delete_partner">
                <input type="hidden" name="partner_id" value="<?= $p['id'] ?>">
            </form>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
