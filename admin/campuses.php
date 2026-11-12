<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Campus Locations Manager
 * Manages: campuses table & Homepage "Discover Our Campuses" section
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Campus Locations & Branches Manager';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: campuses.php');
        exit;
    }

    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'save_campus') {
        $id = (int)($_POST['campus_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $footerHeading = trim($_POST['footer_heading'] ?? $name);
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $mapUrl = trim($_POST['map_url'] ?? '#');
        $image = resolve_image_path('campus_image_file', $_POST['image'] ?? '', 'assets/img/Campus-1.jpg');
        $showGrid = !empty($_POST['show_in_grid']) ? 1 : 0;
        $isActive = !empty($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if ($name === '') {
            set_flash('error', 'Campus Name is required.');
        } else {
            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE campuses 
                        SET name = :n, footer_heading = :fh, phone = :p, email = :e, address = :a, 
                            map_url = :m, image = :img, show_in_grid = :sg, is_active = :act, sort_order = :sort
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        'n' => $name, 'fh' => $footerHeading, 'p' => $phone, 'e' => $email, 'a' => $address,
                        'm' => $mapUrl, 'img' => $image, 'sg' => $showGrid, 'act' => $isActive, 'sort' => $sortOrder, 'id' => $id
                    ]);
                    set_flash('success', "Campus '$name' updated successfully!");
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO campuses (name, footer_heading, phone, email, address, map_url, image, show_in_grid, is_active, sort_order)
                        VALUES (:n, :fh, :p, :e, :a, :m, :img, :sg, :act, :sort)
                    ");
                    $stmt->execute([
                        'n' => $name, 'fh' => $footerHeading, 'p' => $phone, 'e' => $email, 'a' => $address,
                        'm' => $mapUrl, 'img' => $image, 'sg' => $showGrid, 'act' => $isActive, 'sort' => $sortOrder
                    ]);
                    set_flash('success', "New campus branch '$name' created!");
                }
                header('Location: campuses.php');
                exit;
            } catch (Throwable $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }

    if ($formAction === 'delete_campus') {
        $id = (int)($_POST['campus_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare("DELETE FROM campuses WHERE id = ?")->execute([$id]);
                set_flash('success', 'Campus location removed.');
            } catch (Throwable $e) {
                set_flash('error', 'Error removing campus: ' . $e->getMessage());
            }
        }
        header('Location: campuses.php');
        exit;
    }
}

// Fetch single campus for edit
$editItem = null;
if ($action === 'edit' && $editId > 0 && $pdo !== null) {
    $stmt = $pdo->prepare("SELECT * FROM campuses WHERE id = ? LIMIT 1");
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all campuses
$campuses = [];
if ($pdo !== null) {
    try {
        $campuses = $pdo->query("SELECT * FROM campuses ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Campuses Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'edit' || $action === 'new'): 
    $c = $editItem ?? [
        'id' => 0,
        'name' => '',
        'footer_heading' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
        'map_url' => 'https://maps.google.com/...',
        'image' => 'assets/img/Campus-1.jpg',
        'show_in_grid' => 1,
        'is_active' => 1,
        'sort_order' => count($campuses) + 1,
    ];
?>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
                <?= ($c['id'] > 0) ? 'Edit Campus: ' . htmlspecialchars($c['name']) : '+ Add New Campus Location' ?>
            </h2>
            <p style="font-size:12.5px; color:#64748B;">Updates the campus across the homepage, contact page, and footer directory.</p>
        </div>
        <a href="campuses.php" class="adm-btn adm-btn-secondary">&larr; Back to Campuses</a>
    </div>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="form_action" value="save_campus">
        <input type="hidden" name="campus_id" value="<?= $c['id'] ?>">

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title"><span>Campus Details</span></div>
                <div style="display:flex; gap:12px;">
                    <label style="font-size:12.5px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" name="show_in_grid" value="1" <?= (!empty($c['show_in_grid'])) ? 'checked' : '' ?>>
                        <span>Show in Homepage Grid</span>
                    </label>
                    <label style="font-size:12.5px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" <?= (!empty($c['is_active'])) ? 'checked' : '' ?>>
                        <span>Active</span>
                    </label>
                </div>
            </div>
            <div class="adm-card-body">
                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-label">Campus Name <span class="req">*</span></label>
                        <input type="text" name="name" class="adm-input" value="<?= htmlspecialchars($c['name']) ?>" required>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">Footer Heading</label>
                        <input type="text" name="footer_heading" class="adm-input" value="<?= htmlspecialchars($c['footer_heading']) ?>">
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">Sort Order</label>
                        <input type="number" name="sort_order" class="adm-input" value="<?= (int)$c['sort_order'] ?>">
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Address</label>
                    <textarea name="address" class="adm-textarea" rows="2"><?= htmlspecialchars($c['address']) ?></textarea>
                </div>

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-label">Phone Numbers (1 per line or comma-separated)</label>
                        <textarea name="phone" class="adm-textarea" rows="2"><?= htmlspecialchars($c['phone']) ?></textarea>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-label">Email</label>
                        <input type="email" name="email" class="adm-input" value="<?= htmlspecialchars($c['email']) ?>">
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Google Maps Link</label>
                    <input type="text" name="map_url" class="adm-input" value="<?= htmlspecialchars($c['map_url']) ?>">
                </div>

                <div style="margin-top:14px; border-top:1px dashed #E2E8F0; padding-top:14px;">
                    <?= render_image_upload_field('Campus Photo / Facade', 'campus_image_file', 'image', $c['image'] ?? 'assets/img/Campus-1.jpg', 'Directly upload campus building photo from your computer.') ?>
                </div>
            </div>
        </div>

        <div style="text-align:right; margin-bottom:40px;">
            <button type="submit" class="adm-btn adm-btn-primary" style="padding:12px 24px;">
                💾 Save Campus Location
            </button>
        </div>
    </form>

<?php else: ?>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
                Campus Locations & Branches
            </h2>
            <p style="font-size:12.5px; color:#64748B;">Currently managing <?= count($campuses) ?> active branches.</p>
        </div>
        <a href="campuses.php?action=new" class="adm-btn adm-btn-primary">+ Add New Campus</a>
    </div>

    <div class="adm-card">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:50px;">Sort</th>
                        <th>Campus Name</th>
                        <th>Address</th>
                        <th>Phones & Email</th>
                        <th>Status</th>
                        <th style="width:120px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campuses as $cmp): ?>
                        <tr>
                            <td><strong><?= (int)$cmp['sort_order'] ?></strong></td>
                            <td>
                                <strong style="font-size:13.5px; color:var(--adm-primary);"><?= htmlspecialchars($cmp['name']) ?></strong>
                            </td>
                            <td><span style="font-size:12px; color:#475569;"><?= htmlspecialchars($cmp['address']) ?></span></td>
                            <td>
                                <div style="font-size:12px; color:#0F172A; white-space:pre-line;"><?= htmlspecialchars($cmp['phone']) ?></div>
                                <span style="font-size:11.5px; color:#64748B;"><?= htmlspecialchars($cmp['email']) ?></span>
                            </td>
                            <td>
                                <?php if ($cmp['is_active']): ?>
                                    <span class="adm-pill green">Active</span>
                                <?php else: ?>
                                    <span class="adm-pill red">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <a href="campuses.php?action=edit&id=<?= $cmp['id'] ?>" class="adm-btn adm-btn-secondary adm-btn-sm">
                                    ✏️ Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
