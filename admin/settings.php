<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Global Site Settings & Database Backup Tool
 * Manages: settings table & export_db.php trigger
 */

require_once __DIR__ . '/includes/auth.php';

// Single-Click Database Backup Action
if (isset($_GET['action']) && $_GET['action'] === 'backup_db') {
    $script = dirname(__DIR__) . '/scripts/export_db.php';
    if (file_exists($script) && $pdo !== null) {
        try {
            // Re-dump the single database file directly
            $targetFile = dirname(__DIR__) . '/database/averroes-ltd.sql';

            $output = "-- ====================================================================\n";
            $output .= "-- Averroes International School CMS - Complete Unified Database Dump\n";
            $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Target Server: MySQL 5.7+ / MySQL 8.0+ / MariaDB / PlanetScale\n";
            $output .= "-- ====================================================================\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
            $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
            $output .= "SET time_zone = '+00:00';\n";
            $output .= "SET NAMES utf8mb4;\n\n";

            $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);

            foreach ($tables as $tRow) {
                $table = $tRow[0];
                $output .= "-- --------------------------------------------------------------------\n";
                $output .= "-- Table structure for `$table`\n";
                $output .= "-- --------------------------------------------------------------------\n";
                $output .= "DROP TABLE IF EXISTS `$table`;\n";

                $createRow = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
                $output .= ($createRow['Create Table'] ?? '') . ";\n\n";

                $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $output .= "-- Dumping data for table `$table`\n";
                    $columns = array_keys($rows[0]);
                    $colList = implode('`, `', $columns);

                    $chunks = array_chunk($rows, 50);
                    foreach ($chunks as $chunk) {
                        $output .= "INSERT INTO `$table` (`$colList`) VALUES\n";
                        $valRows = [];
                        foreach ($chunk as $row) {
                            $vals = [];
                            foreach ($row as $val) {
                                $vals[] = ($val === null) ? 'NULL' : $pdo->quote((string)$val);
                            }
                            $valRows[] = '(' . implode(', ', $vals) . ')';
                        }
                        $output .= implode(",\n", $valRows) . ";\n";
                    }
                    $output .= "\n";
                }
            }
            $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

            file_put_contents($targetFile, $output);

            // Serve as attachment download
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename=averroes-ltd.sql');
            header('Content-Length: ' . strlen($output));
            echo $output;
            exit;
        } catch (Throwable $e) {
            set_flash('error', 'Backup failed: ' . $e->getMessage());
            header('Location: settings.php');
            exit;
        }
    }
}

$pageTitle = 'Global Site Settings & Database Tools';

// Handle Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: settings.php');
        exit;
    }

    // Handle Quick Delete for Brand Logos
    if (!empty($_POST['action_delete_target'])) {
        $target = trim($_POST['action_delete_target']);
        if (in_array($target, ['logo', 'footer_logo', 'favicon'], true)) {
            try {
                $delStmt = $pdo->prepare("UPDATE settings SET setting_value = '' WHERE setting_key = :k");
                $delStmt->execute(['k' => $target]);
                $names = ['logo' => 'Header Logo', 'footer_logo' => 'Footer Logo Banner', 'favicon' => 'Website Favicon'];
                set_flash('success', $names[$target] . ' was deleted/cleared successfully.');
            } catch (Throwable $e) {
                set_flash('error', 'Error deleting logo: ' . $e->getMessage());
            }
            header('Location: settings.php');
            exit;
        }
    }

    if (isset($_POST['settings']) && is_array($_POST['settings'])) {
        try {
            // Process Direct Media Uploads for Logos & Favicon
            $_POST['settings']['logo'] = resolve_image_path('logo_file', $_POST['settings']['logo'] ?? '', 'assets/img/logo.jpeg');
            $_POST['settings']['footer_logo'] = resolve_image_path('footer_logo_file', $_POST['settings']['footer_logo'] ?? '', 'assets/img/footer_logo.png');
            $_POST['settings']['favicon'] = resolve_image_path('favicon_file', $_POST['settings']['favicon'] ?? '', 'assets/img/logo.jpeg');

            $pdo->beginTransaction();
            $upStmt = $pdo->prepare("UPDATE settings SET setting_value = :v WHERE setting_key = :k");
            foreach ($_POST['settings'] as $k => $v) {
                $upStmt->execute(['v' => trim((string)$v), 'k' => $k]);
            }
            $pdo->commit();
            set_flash('success', 'Global settings updated successfully!');
        } catch (Throwable $e) {
            $pdo->rollBack();
            set_flash('error', 'Update error: ' . $e->getMessage());
        }
        header('Location: settings.php');
        exit;
    }
}

// Fetch all settings
$settings = [];
if ($pdo !== null) {
    try {
        $rows = $pdo->query("SELECT * FROM settings ORDER BY group_name ASC, setting_key ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
    } catch (Throwable $e) {
        error_log('[Settings Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Global Site Settings & Database Architecture
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Manage site branding, SEO metadata, WhatsApp helpline, and database backups.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="settings.php?action=backup_db" class="adm-btn adm-btn-primary">
            💾 Download Single SQL Backup
        </a>
    </div>
</div>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <!-- 1. General & SEO -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title"><span>🏷️ General & Search Engine Optimization (SEO)</span></div>
        </div>
        <div class="adm-card-body">
            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">Global Website Title</label>
                    <input type="text" name="settings[site_title]" class="adm-input" value="<?= htmlspecialchars($settings['site_title'] ?? '') ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Copyright Notice</label>
                    <input type="text" name="settings[copyright_text]" class="adm-input" value="<?= htmlspecialchars($settings['copyright_text'] ?? '') ?>">
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Meta Description (Search Engines)</label>
                <textarea name="settings[meta_description]" class="adm-textarea" rows="2"><?= htmlspecialchars($settings['meta_description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- 2. Branding & Media -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title"><span>🎨 School Branding & Logos</span></div>
            <span class="adm-pill orange">Add • Edit • Delete</span>
        </div>
        <div class="adm-card-body">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                <!-- Header Logo Block -->
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:16px;">
                    <?= render_image_upload_field('Header Logo (JPEG/PNG)', 'logo_file', 'settings[logo]', $settings['logo'] ?? '', 'Upload school crest / main header logo.') ?>
                    <?php if (!empty($settings['logo'])): ?>
                        <div style="text-align:right; margin-top:8px;">
                            <button type="submit" name="action_delete_target" value="logo" class="adm-btn adm-btn-secondary" style="color:#EF4444; font-size:11.5px; padding:4px 10px;" onclick="return confirm('Are you sure you want to delete/clear the Header Logo?');">
                                🗑️ Delete Header Logo
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Footer Logo Block -->
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:16px;">
                    <?= render_image_upload_field('Footer Logo Banner', 'footer_logo_file', 'settings[footer_logo]', $settings['footer_logo'] ?? '', 'Wide footer logo with slogan banner.') ?>
                    <?php if (!empty($settings['footer_logo'])): ?>
                        <div style="text-align:right; margin-top:8px;">
                            <button type="submit" name="action_delete_target" value="footer_logo" class="adm-btn adm-btn-secondary" style="color:#EF4444; font-size:11.5px; padding:4px 10px;" onclick="return confirm('Are you sure you want to delete/clear the Footer Logo Banner?');">
                                🗑️ Delete Footer Logo
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Favicon Block -->
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:16px;">
                    <?= render_image_upload_field('Website Favicon', 'favicon_file', 'settings[favicon]', $settings['favicon'] ?? '', 'Browser tab favicon icon.') ?>
                    <?php if (!empty($settings['favicon'])): ?>
                        <div style="text-align:right; margin-top:8px;">
                            <button type="submit" name="action_delete_target" value="favicon" class="adm-btn adm-btn-secondary" style="color:#EF4444; font-size:11.5px; padding:4px 10px;" onclick="return confirm('Are you sure you want to delete/clear the Website Favicon?');">
                                🗑️ Delete Favicon
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Floating Interaction Hub (WhatsApp & Chat) -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title"><span>💬 Floating Contact Tools</span></div>
        </div>
        <div class="adm-card-body">
            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label class="adm-label">WhatsApp Number / Link</label>
                    <input type="text" name="settings[whatsapp_url]" class="adm-input" value="<?= htmlspecialchars($settings['whatsapp_url'] ?? '') ?>" placeholder="https://wa.me/...">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">AI Chat Assistant Button Label</label>
                    <input type="text" name="settings[chat_text]" class="adm-input" value="<?= htmlspecialchars($settings['chat_text'] ?? 'Chat with Us') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Single Unified Database Information -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title"><span>🗄️ Database Architecture & Deployment</span></div>
            <span class="adm-pill green">1 Single Database Dump</span>
        </div>
        <div class="adm-card-body">
            <p style="font-size:13px; color:#475569; margin-bottom:14px; line-height:1.5;">
                In accordance with project deployment standards, strictly <strong>one unified database file</strong> exists:
                <code style="background:#FFF0E5; color:#F58220; padding:2px 8px; border-radius:4px; font-weight:600;">database/averroes-ltd.sql</code>.
                Clicking the button below instantly exports all 15 active tables and seed data directly into this file and downloads it to your computer.
            </p>
            <a href="settings.php?action=backup_db" class="adm-btn adm-btn-primary">
                💾 Export & Download averroes-ltd.sql
            </a>
        </div>
    </div>

    <div style="text-align:right; margin-bottom:40px;">
        <button type="submit" class="adm-btn adm-btn-primary" style="padding:12px 24px;">
            💾 Save All Global Settings
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
