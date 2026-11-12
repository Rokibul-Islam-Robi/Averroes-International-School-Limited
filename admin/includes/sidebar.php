<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Layout Sidebar
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentQuery = $_GET['page'] ?? '';
$adminUsername = $_SESSION['admin_username'] ?? 'Administrator';

// Query counts if DB is active
$countInquiries = 0;
if ($pdo !== null) {
    try {
        $countInquiries = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'new'")->fetchColumn();
    } catch (Throwable $e) {}
}
?>
<aside class="adm-sidebar">
    <div class="adm-brand">
        <img src="<?= base_url('assets/img/logo.jpeg') ?>" alt="Averroes Ltd Logo" class="adm-brand-logo">
        <div class="adm-brand-info">
            <h1>Averroes Ltd CMS</h1>
            <span>School of Life</span>
        </div>
    </div>

    <div class="adm-nav-container">
        <div class="adm-nav-group-title">MAIN</div>
        <a href="<?= base_url('admin/index.php') ?>" class="adm-nav-link <?= ($currentPage === 'index') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                </span>
                <span>Dashboard</span>
            </div>
        </a>

        <div class="adm-nav-group-title">ACADEMIC & CONTENT CMS</div>
        <a href="<?= base_url('admin/home.php') ?>" class="adm-nav-link <?= ($currentPage === 'home') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </span>
                <span>Homepage</span>
            </div>
        </a>
        <a href="<?= base_url('admin/subpages.php?page=why_averroes') ?>" class="adm-nav-link <?= ($currentPage === 'subpages' && $currentQuery === 'why_averroes') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </span>
                <span>Why Averroes</span>
            </div>
        </a>
        <a href="<?= base_url('admin/subpages.php?page=life_at_averroes') ?>" class="adm-nav-link <?= ($currentPage === 'subpages' && $currentQuery === 'life_at_averroes') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </span>
                <span>Life at Averroes</span>
            </div>
        </a>
        <a href="<?= base_url('admin/admission.php') ?>" class="adm-nav-link <?= ($currentPage === 'admission') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </span>
                <span>Admissions Portal</span>
            </div>
        </a>
        <a href="<?= base_url('admin/programs.php') ?>" class="adm-nav-link <?= ($currentPage === 'programs') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </span>
                <span>Programs (10+)</span>
            </div>
        </a>
        <a href="<?= base_url('admin/campuses.php') ?>" class="adm-nav-link <?= ($currentPage === 'campuses') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22V7l8-4 8 4v15"/><path d="M8 22v-6a2 2 0 0 1 4 0v6"/><line x1="4" y1="11" x2="20" y2="11"/><line x1="4" y1="16" x2="20" y2="16"/></svg>
                </span>
                <span>Campus Locations</span>
            </div>
        </a>
        <a href="<?= base_url('admin/contact.php') ?>" class="adm-nav-link <?= ($currentPage === 'contact') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <span>Contact Us & Map</span>
            </div>
        </a>

        <div class="adm-nav-group-title">NAVIGATION & LEADS</div>
        <a href="<?= base_url('admin/navigation.php') ?>" class="adm-nav-link <?= ($currentPage === 'navigation') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                </span>
                <span>Header & Footer Nav</span>
            </div>
        </a>
        <a href="<?= base_url('admin/partners.php') ?>" class="adm-nav-link <?= ($currentPage === 'partners') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                </span>
                <span>Accreditation Partners</span>
            </div>
        </a>
        <a href="<?= base_url('admin/inquiries.php') ?>" class="adm-nav-link <?= ($currentPage === 'inquiries') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <span>Inquiries & Leads</span>
            </div>
            <?php if ($countInquiries > 0): ?>
                <span class="adm-badge" style="background:#EF4444;"><?= $countInquiries ?></span>
            <?php endif; ?>
        </a>

        <div class="adm-nav-group-title">SYSTEM</div>
        <a href="<?= base_url('admin/settings.php') ?>" class="adm-nav-link <?= ($currentPage === 'settings') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </span>
                <span>Site Settings & Logos</span>
            </div>
        </a>
        <a href="<?= base_url('admin/profile.php') ?>" class="adm-nav-link <?= ($currentPage === 'profile') ? 'active' : '' ?>">
            <div class="adm-nav-link-left">
                <span class="adm-nav-link-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span>Admin Profile</span>
            </div>
        </a>
    </div>

    <!-- Cognova IT Developer Attribution -->
    <div style="padding:10px 18px; margin:0 12px 8px 12px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:8px; text-align:center;">
        <div style="font-size:10px; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:0.8px;">System Architecture</div>
        <div style="font-size:11.5px; font-weight:700; color:#FFFFFF; margin-top:2px; display:flex; align-items:center; justify-content:center; gap:5px;">
            <span style="color:#F58220;">⚡</span> Developed by Cognova IT
        </div>
    </div>

    <div class="adm-sidebar-footer">
        <div class="adm-user-brief">
            <div class="adm-user-avatar"><?= strtoupper(substr($adminUsername, 0, 1)) ?></div>
            <div class="adm-user-meta">
                <div class="adm-user-name"><?= htmlspecialchars($adminUsername) ?></div>
                <div class="adm-user-role">Super Admin</div>
            </div>
        </div>
        <a href="<?= base_url('admin/logout.php') ?>" title="Logout" style="color:#94A3B8; text-decoration:none; display:inline-flex; align-items:center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </a>
    </div>
</aside>
