<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Dashboard Overview
 */

$pageTitle = 'Dashboard Overview';
require_once __DIR__ . '/includes/header.php';

// Fetch aggregate counts
$countPrograms = 0;
$countCampuses = 0;
$countSubscribers = 0;
$countInquiries = 0;
$recentInquiries = [];
$recentSubscribers = [];

if ($pdo !== null) {
    try {
        $countPrograms = (int)$pdo->query("SELECT COUNT(*) FROM programs")->fetchColumn();
        $countCampuses = (int)$pdo->query("SELECT COUNT(*) FROM campuses")->fetchColumn();
        $countSubscribers = (int)$pdo->query("SELECT COUNT(*) FROM newsletter_subscribers")->fetchColumn();
        $countInquiries = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries")->fetchColumn();

        $recentInquiries = $pdo->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $recentSubscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Dashboard Stats] ' . $e->getMessage());
    }
}
?>

<!-- Metric Counters -->
<div class="adm-stat-grid">
    <div class="adm-stat-card">
        <div>
            <div class="adm-stat-num"><?= $countPrograms ?></div>
            <div class="adm-stat-label">Active Programs / Slider Cards</div>
        </div>
        <div class="adm-stat-icon orange">🚀</div>
    </div>

    <div class="adm-stat-card">
        <div>
            <div class="adm-stat-num"><?= $countCampuses ?></div>
            <div class="adm-stat-label">Campuses & Branches</div>
        </div>
        <div class="adm-stat-icon purple">🏫</div>
    </div>

    <div class="adm-stat-card">
        <div>
            <div class="adm-stat-num"><?= $countInquiries ?></div>
            <div class="adm-stat-label">Parent & Student Inquiries</div>
        </div>
        <div class="adm-stat-icon blue">📬</div>
    </div>

    <div class="adm-stat-card">
        <div>
            <div class="adm-stat-num"><?= $countSubscribers ?></div>
            <div class="adm-stat-label">Newsletter Subscribers</div>
        </div>
        <div class="adm-stat-icon green">📰</div>
    </div>
</div>

<!-- Quick Subpage & Content Management Hub -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>⚡ Quick Content & Page Editors</span>
        </div>
        <span style="font-size:12px; color:#64748B;">All website pages editable in real-time</span>
    </div>
    <div class="adm-card-body">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
            <a href="<?= base_url('admin/subpages.php?page=why_averroes') ?>" class="adm-card" style="margin-bottom:0; padding:18px; text-decoration:none; color:inherit; display:block; border-color:#E2E8F0; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#F58220'" onmouseout="this.style.borderColor='#E2E8F0'">
                <div style="font-size:24px; margin-bottom:8px;">🎓</div>
                <div style="font-weight:700; color:var(--adm-primary); font-size:15px;">Why Averroes Subpage</div>
                <div style="font-size:12px; color:#64748B; margin-top:4px;">Edit Cambridge & Tarbiyah, pedagogy blocks, metrics & imagery.</div>
            </a>

            <a href="<?= base_url('admin/subpages.php?page=life_at_averroes') ?>" class="adm-card" style="margin-bottom:0; padding:18px; text-decoration:none; color:inherit; display:block; border-color:#E2E8F0; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#F58220'" onmouseout="this.style.borderColor='#E2E8F0'">
                <div style="font-size:24px; margin-bottom:8px;">🌟</div>
                <div style="font-weight:700; color:var(--adm-primary); font-size:15px;">Life at Averroes Subpage</div>
                <div style="font-size:12px; color:#64748B; margin-top:4px;">Edit student life, active clubs, daily Salah & facilities.</div>
            </a>

            <a href="<?= base_url('admin/admission.php') ?>" class="adm-card" style="margin-bottom:0; padding:18px; text-decoration:none; color:inherit; display:block; border-color:#E2E8F0; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#F58220'" onmouseout="this.style.borderColor='#E2E8F0'">
                <div style="font-size:24px; margin-bottom:8px;">📝</div>
                <div style="font-weight:700; color:var(--adm-primary); font-size:15px;">Admissions Portal</div>
                <div style="font-size:12px; color:#64748B; margin-top:4px;">Edit 7-step timeline, age matrix, fee structure & FAQ accordion.</div>
            </a>

            <a href="<?= base_url('admin/contact.php') ?>" class="adm-card" style="margin-bottom:0; padding:18px; text-decoration:none; color:inherit; display:block; border-color:#E2E8F0; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#F58220'" onmouseout="this.style.borderColor='#E2E8F0'">
                <div style="font-size:24px; margin-bottom:8px;">📍</div>
                <div style="font-weight:700; color:var(--adm-primary); font-size:15px;">Contact Us & Maps</div>
                <div style="font-size:12px; color:#64748B; margin-top:4px;">Edit emergency hotlines, Lalmatia & Banasree branches & Google Map.</div>
            </a>

            <a href="<?= base_url('admin/programs.php') ?>" class="adm-card" style="margin-bottom:0; padding:18px; text-decoration:none; color:inherit; display:block; border-color:#E2E8F0; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#F58220'" onmouseout="this.style.borderColor='#E2E8F0'">
                <div style="font-size:24px; margin-bottom:8px;">🚀</div>
                <div style="font-weight:700; color:var(--adm-primary); font-size:15px;">Signature Programs (10+)</div>
                <div style="font-size:12px; color:#64748B; margin-top:4px;">Manage Qira'at, Seerah, Swimming, Turf, Book Shop, Hifz, etc.</div>
            </a>

            <a href="<?= base_url('admin/home.php') ?>" class="adm-card" style="margin-bottom:0; padding:18px; text-decoration:none; color:inherit; display:block; border-color:#E2E8F0; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#F58220'" onmouseout="this.style.borderColor='#E2E8F0'">
                <div style="font-size:24px; margin-bottom:8px;">🏠</div>
                <div style="font-weight:700; color:var(--adm-primary); font-size:15px;">Homepage Manager</div>
                <div style="font-size:12px; color:#64748B; margin-top:4px;">Edit hero Vimeo video, counter stats, and section intros.</div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Inquiries (Full Width Overview) -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>📬 Recent Inquiries</span>
        </div>
        <a href="<?= base_url('admin/inquiries.php') ?>" class="adm-btn adm-btn-secondary adm-btn-sm">View All</a>
    </div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Parent / Student</th>
                    <th>Phone</th>
                    <th>Campus</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentInquiries)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:24px; color:#94A3B8;">
                            No inquiries received yet. Test by submitting a form on Contact or Admissions!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentInquiries as $inq): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($inq['name']) ?></strong><br>
                                <span style="font-size:11.5px; color:#64748B;"><?= htmlspecialchars($inq['email']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($inq['phone']) ?></td>
                            <td><span class="adm-pill orange"><?= htmlspecialchars(ucfirst($inq['campus'])) ?></span></td>
                            <td><span style="font-size:12px; color:#64748B;"><?= date('M d, Y', strtotime($inq['created_at'])) ?></span></td>
                            <td>
                                <?php if ($inq['status'] === 'new'): ?>
                                    <span class="adm-pill red">New</span>
                                <?php elseif ($inq['status'] === 'contacted'): ?>
                                    <span class="adm-pill blue">Contacted</span>
                                <?php else: ?>
                                    <span class="adm-pill green"><?= htmlspecialchars(ucfirst($inq['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
