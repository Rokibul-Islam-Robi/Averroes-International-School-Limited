<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Layout Header
 */

require_once __DIR__ . '/auth.php';
require_admin_auth();

$pageTitle = $pageTitle ?? 'Dashboard Overview';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Averroes Ltd CMS</title>
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo.jpeg') ?>">
    <link rel="stylesheet" href="<?= base_url('admin/css/admin.css') ?>">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="adm-wrapper">
    <header class="adm-topbar">
        <div class="adm-topbar-title">
            <span><?= htmlspecialchars($pageTitle) ?></span>
        </div>
        <div class="adm-topbar-actions">
            <a href="<?= base_url() ?>" target="_blank" class="adm-btn adm-btn-secondary adm-btn-sm" title="View Public Website">
                <span>🌐</span> View Website
            </a>
            <a href="<?= base_url('admin/settings.php?action=backup_db') ?>" class="adm-btn adm-btn-primary adm-btn-sm" title="Single-Click Database Backup">
                <span>💾</span> Backup Database
            </a>
            <a href="<?= base_url('admin/logout.php') ?>" class="adm-btn adm-btn-secondary adm-btn-sm" style="color:#EF4444;" title="Sign Out">
                <span>🚪</span> Logout
            </a>
        </div>
    </header>

    <main class="adm-main">
