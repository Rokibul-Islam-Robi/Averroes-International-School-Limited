<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Global Navigation & Links Manager
 * Manages: nav_items table (Header Nav, Header CTAs, Footer Links, Legal Links)
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Header & Footer Navigation Manager';

$locationFilter = $_GET['loc'] ?? 'header';
$validLocations = ['header', 'header_btn', 'footer', 'legal'];
if (!in_array($locationFilter, $validLocations, true)) {
    $locationFilter = 'header';
}

// Handle Form Posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header("Location: navigation.php?loc=$locationFilter");
        exit;
    }

    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'add_item') {
        $label = trim($_POST['label'] ?? '');
        $url = trim($_POST['url'] ?? '#');
        $hasDropdown = !empty($_POST['has_dropdown']) ? 1 : 0;
        $cssClass = trim($_POST['css_class'] ?? '');

        if ($label !== '') {
            try {
                $maxSort = (int)$pdo->query("SELECT MAX(sort_order) FROM nav_items WHERE location = " . $pdo->quote($locationFilter))->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO nav_items (location, label, url, css_class, has_dropdown, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$locationFilter, $label, $url, $cssClass !== '' ? $cssClass : null, $hasDropdown, $maxSort + 1]);
                set_flash('success', "Nav link '$label' added!");
            } catch (Throwable $e) {
                set_flash('error', 'Error: ' . $e->getMessage());
            }
        }
        header("Location: navigation.php?loc=$locationFilter");
        exit;
    }

    if ($formAction === 'save_items' && isset($_POST['items']) && is_array($_POST['items'])) {
        try {
            $pdo->beginTransaction();
            $upStmt = $pdo->prepare("UPDATE nav_items SET label = :l, url = :u, sort_order = :s, is_active = :a WHERE id = :id");
            foreach ($_POST['items'] as $id => $it) {
                $upStmt->execute([
                    'l'  => trim($it['label'] ?? ''),
                    'u'  => trim($it['url'] ?? '#'),
                    's'  => (int)($it['sort_order'] ?? 0),
                    'a'  => !empty($it['is_active']) ? 1 : 0,
                    'id' => (int)$id,
                ]);
            }
            $pdo->commit();
            set_flash('success', 'Navigation links updated successfully!');
        } catch (Throwable $e) {
            $pdo->rollBack();
            set_flash('error', 'Error updating navigation: ' . $e->getMessage());
        }
        header("Location: navigation.php?loc=$locationFilter");
        exit;
    }

    if ($formAction === 'delete_item') {
        $id = (int)($_POST['item_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare("DELETE FROM nav_items WHERE id = ?")->execute([$id]);
                set_flash('success', 'Nav link removed.');
            } catch (Throwable $e) {
                set_flash('error', 'Error deleting item: ' . $e->getMessage());
            }
        }
        header("Location: navigation.php?loc=$locationFilter");
        exit;
    }
}

// Fetch items
$items = [];
if ($pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM nav_items WHERE location = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$locationFilter]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Nav Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Global Navigation Links & Menus
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Manage header menus, action buttons, and footer link columns.</p>
    </div>
</div>

<!-- Tabs -->
<div class="adm-tabs">
    <a href="navigation.php?loc=header" class="adm-tab-link <?= ($locationFilter === 'header') ? 'active' : '' ?>">
        🧭 Header Main Menu
    </a>
    <a href="navigation.php?loc=header_btn" class="adm-tab-link <?= ($locationFilter === 'header_btn') ? 'active' : '' ?>">
        🔘 Header Action Buttons
    </a>
    <a href="navigation.php?loc=footer" class="adm-tab-link <?= ($locationFilter === 'footer') ? 'active' : '' ?>">
        👣 Footer Important Links
    </a>
    <a href="navigation.php?loc=legal" class="adm-tab-link <?= ($locationFilter === 'legal') ? 'active' : '' ?>">
        ⚖️ Legal Policy Links
    </a>
</div>

<!-- Add New Link -->
<div class="adm-card" style="background:#FFF0E5; border-color:#FCD5C0;">
    <div class="adm-card-body" style="padding:16px 20px;">
        <h4 style="font-size:14px; font-weight:700; color:var(--adm-primary); margin-bottom:10px;">+ Add New Nav Item</h4>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="add_item">

            <div class="adm-form-grid" style="grid-template-columns: 2fr 2fr 1fr auto; align-items:flex-end; gap:12px;">
                <div>
                    <label class="adm-label">Link Label <span class="req">*</span></label>
                    <input type="text" name="label" class="adm-input" placeholder="e.g. Campus Life" required>
                </div>
                <div>
                    <label class="adm-label">Target URL / Anchor</label>
                    <input type="text" name="url" class="adm-input" placeholder="e.g. life-at-averroes.php" value="#">
                </div>
                <div>
                    <label class="adm-label">Dropdown?</label>
                    <label style="display:flex; align-items:center; gap:6px; height:38px; cursor:pointer;">
                        <input type="checkbox" name="has_dropdown" value="1">
                        <span style="font-size:12.5px;">Has Submenu</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="adm-btn adm-btn-primary" style="height:38px;">+ Add Item</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Existing Links Table -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>Links for '<?= htmlspecialchars(strtoupper($locationFilter)) ?>'</span>
        </div>
        <span class="adm-pill purple"><?= count($items) ?> Items</span>
    </div>
    <div class="adm-card-body" style="padding:0;">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="save_items">

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">Sort</th>
                            <th>Link Label</th>
                            <th>URL Destination</th>
                            <th style="width:90px;">Active</th>
                            <th style="width:80px; text-align:right;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:20px; color:#94A3B8;">No links configured.</td></tr>
                        <?php else: ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td>
                                        <input type="number" name="items[<?= $it['id'] ?>][sort_order]" class="adm-input" style="width:60px; padding:4px 6px;" value="<?= (int)$it['sort_order'] ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="items[<?= $it['id'] ?>][label]" class="adm-input" value="<?= htmlspecialchars($it['label']) ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="items[<?= $it['id'] ?>][url]" class="adm-input" value="<?= htmlspecialchars($it['url']) ?>">
                                    </td>
                                    <td style="text-align:center;">
                                        <input type="checkbox" name="items[<?= $it['id'] ?>][is_active]" value="1" <?= (!empty($it['is_active'])) ? 'checked' : '' ?>>
                                    </td>
                                    <td style="text-align:right;">
                                        <button type="submit" form="del-form-<?= $it['id'] ?>" class="adm-btn adm-btn-secondary adm-btn-sm btn-confirm-delete" data-title="<?= htmlspecialchars($it['label']) ?>" style="color:#EF4444;">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px; text-align:right; background:#F8FAFC; border-top:1px solid #E2E8F0;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    💾 Save Navigation Changes
                </button>
            </div>
        </form>

        <!-- Hidden delete forms -->
        <?php foreach ($items as $it): ?>
            <form id="del-form-<?= $it['id'] ?>" method="POST" action="" style="display:none;">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="form_action" value="delete_item">
                <input type="hidden" name="item_id" value="<?= $it['id'] ?>">
            </form>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
