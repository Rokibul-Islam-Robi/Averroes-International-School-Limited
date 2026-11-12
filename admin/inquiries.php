<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Admissions & Contact Inquiries Inbox
 * Manages: contact_inquiries table & CSV Export
 */

require_once __DIR__ . '/includes/auth.php';

// Export CSV handler
if (isset($_GET['action']) && $_GET['action'] === 'export_csv' && $pdo !== null) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=averroes_inquiries_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Parent / Student Name', 'Email', 'Phone', 'Target Campus', 'Grade Level', 'Subject / Type', 'Message', 'Reply Message', 'Replied At', 'Replied By', 'Status', 'Date Received']);

    $rows = $pdo->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        fputcsv($output, [
            $r['id'],
            $r['name'],
            $r['email'],
            $r['phone'],
            ucfirst($r['campus']),
            $r['grade'] ?? 'Not specified',
            $r['subject'] ?? 'Website Inquiry',
            $r['message'],
            $r['reply_message'] ?? '',
            $r['replied_at'] ?? '',
            $r['replied_by'] ?? '',
            ucfirst($r['status']),
            $r['created_at'],
        ]);
    }
    fclose($output);
    exit;
}

$pageTitle = 'Admissions & Inquiries Inbox';

// Handle Status Updates or Deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: inquiries.php');
        exit;
    }

    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'reply_inquiry') {
        $id           = (int)($_POST['inquiry_id'] ?? 0);
        $replySubject = trim($_POST['reply_subject'] ?? '');
        $replyMessage = trim($_POST['reply_message'] ?? '');
        $sendEmail    = !empty($_POST['send_email']);

        if ($id > 0 && $replyMessage !== '') {
            $stmt = $pdo->prepare("SELECT * FROM contact_inquiries WHERE id = ?");
            $stmt->execute([$id]);
            $inq = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($inq) {
                $repliedBy = $_SESSION['admin_username'] ?? 'Admissions Desk';

                $updateStmt = $pdo->prepare("
                    UPDATE contact_inquiries 
                    SET reply_message = :reply, replied_at = NOW(), replied_by = :replied_by, status = 'contacted'
                    WHERE id = :id
                ");
                $updateStmt->execute([
                    ':reply'      => $replyMessage,
                    ':replied_by' => $repliedBy,
                    ':id'         => $id,
                ]);

                $emailSent = false;
                if ($sendEmail && !empty($inq['email']) && filter_var($inq['email'], FILTER_VALIDATE_EMAIL)) {
                    $to = $inq['email'];
                    $subject = !empty($replySubject) ? $replySubject : ('Response to your inquiry: ' . ($inq['subject'] ?? 'Averroes International School'));
                    
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: Averroes International School <admissions@aisl.edu.bd>\r\n";
                    $headers .= "Reply-To: info@aisl.edu.bd\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();

                    $emailHtml = '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:Arial, sans-serif; background:#f8fafc; margin:0; padding:20px; color:#1e293b;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0;">
        <tr style="background:#25093b;">
            <td style="padding:24px 30px; text-align:center;">
                <h1 style="color:#ffffff; margin:0; font-size:22px; letter-spacing:0.5px;">AVERROES INTERNATIONAL SCHOOL LTD</h1>
                <p style="color:#f58220; margin:6px 0 0; font-size:13px; font-weight:600;">One School Serving The Purposes of Here & Hereafter</p>
            </td>
        </tr>
        <tr>
            <td style="padding:30px;">
                <p style="font-size:15px; margin-top:0;">Dear <strong>' . htmlspecialchars($inq['name']) . '</strong>,</p>
                <p style="font-size:14px; line-height:1.6; color:#334155;">Thank you for getting in touch with Averroes International School (' . htmlspecialchars(ucfirst($inq['campus'])) . ' Campus).</p>
                
                <div style="background:#f1f5f9; border-left:4px solid #7c3aed; padding:16px 20px; margin:20px 0; border-radius:0 8px 8px 0;">
                    <div style="font-size:11px; text-transform:uppercase; font-weight:bold; color:#64748b; margin-bottom:6px;">Official Response:</div>
                    <div style="font-size:14px; line-height:1.7; color:#0f172a; white-space:pre-wrap;">' . htmlspecialchars($replyMessage) . '</div>
                </div>

                <div style="background:#fff7ed; border:1px dashed #fdba74; padding:12px 16px; margin:20px 0; border-radius:8px; font-size:12.5px; color:#9a3412;">
                    <strong>Your Original Inquiry:</strong><br>
                    <em>"' . htmlspecialchars($inq['message']) . '"</em>
                </div>

                <p style="font-size:13.5px; line-height:1.6; color:#475569;">
                    Should you require further information, feel free to visit our campus or contact our central admissions counselor at:
                </p>
                <ul style="font-size:13px; line-height:1.8; color:#334155; padding-left:20px;">
                    <li><strong>Lalmatia Campus Helpline:</strong> +880 1954-123 123</li>
                    <li><strong>Banasree Campus Helpline:</strong> +88 01901-048844</li>
                    <li><strong>Email:</strong> admin@aisl.edu.bd</li>
                </ul>
                <p style="font-size:14px; margin-bottom:0;">Warm regards,<br><strong>' . htmlspecialchars($repliedBy) . '</strong><br>Averroes Admissions & Academic Directorate</p>
            </td>
        </tr>
        <tr style="background:#f8fafc; border-top:1px solid #e2e8f0;">
            <td style="padding:16px 30px; text-align:center; font-size:11.5px; color:#94a3b8;">
                © ' . date('Y') . ' Averroes International School Ltd. All rights reserved.<br>
                Lalmatia & Banasree Campuses, Dhaka, Bangladesh.
            </td>
        </tr>
    </table>
</body>
</html>';

                    try {
                        $emailSent = @mail($to, $subject, $emailHtml, $headers);
                    } catch (Throwable $e) {
                        $emailSent = false;
                    }
                }

                if ($emailSent) {
                    set_flash('success', 'Reply recorded and email successfully sent to ' . htmlspecialchars($inq['email']) . '!');
                } else {
                    set_flash('success', 'Reply recorded in system and inquiry marked as Contacted.');
                }
            } else {
                set_flash('error', 'Inquiry record not found.');
            }
        } else {
            set_flash('error', 'Reply message cannot be empty.');
        }
        header('Location: inquiries.php');
        exit;
    }

    if ($formAction === 'update_status') {
        $id = (int)($_POST['inquiry_id'] ?? 0);
        $status = $_POST['status'] ?? 'new';
        $validStatuses = ['new', 'contacted', 'enrolled', 'archived'];
        if ($id > 0 && in_array($status, $validStatuses, true)) {
            $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            set_flash('success', 'Inquiry status updated to ' . ucfirst($status) . '.');
        }
    }

    if ($formAction === 'delete_inquiry') {
        $id = (int)($_POST['inquiry_id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM contact_inquiries WHERE id = ?")->execute([$id]);
            set_flash('success', 'Inquiry deleted.');
        }
    }

    header('Location: inquiries.php');
    exit;
}

// Filters
$campusFilter = $_GET['campus'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$searchQuery = trim($_GET['q'] ?? '');

$where = [];
$params = [];

if ($campusFilter !== '') {
    $where[] = "campus = :campus";
    $params['campus'] = $campusFilter;
}
if ($statusFilter !== '') {
    $where[] = "status = :status";
    $params['status'] = $statusFilter;
}
if ($searchQuery !== '') {
    $where[] = "(name LIKE :q OR phone LIKE :q OR email LIKE :q)";
    $params['q'] = "%$searchQuery%";
}

$sql = "SELECT * FROM contact_inquiries";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY created_at DESC";

$inquiries = [];
if ($pdo !== null) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('[Inquiries Fetch] ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Admissions & Contact Inquiries Inbox
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Manage incoming parent admission inquiries and contact requests.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="inquiries.php?action=export_csv" class="adm-btn adm-btn-primary">
            📥 Export to Excel / CSV
        </a>
    </div>
</div>

<!-- Filters Bar -->
<div class="adm-card" style="margin-bottom:20px;">
    <div class="adm-card-body" style="padding:14px 20px;">
        <form method="GET" action="" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
            <div style="flex:1; min-width:200px;">
                <input type="text" name="q" class="adm-input" placeholder="Search by parent name, phone, or email..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <div>
                <select name="campus" class="adm-select" style="width:170px;">
                    <option value="">All Campuses</option>
                    <option value="lalmatia" <?= ($campusFilter === 'lalmatia') ? 'selected' : '' ?>>Lalmatia</option>
                    <option value="banasree" <?= ($campusFilter === 'banasree') ? 'selected' : '' ?>>Banasree</option>
                    <option value="central" <?= ($campusFilter === 'central') ? 'selected' : '' ?>>Central / Chat</option>
                    <option value="general" <?= ($campusFilter === 'general') ? 'selected' : '' ?>>General</option>
                </select>
            </div>
            <div>
                <select name="status" class="adm-select" style="width:150px;">
                    <option value="">All Statuses</option>
                    <option value="new" <?= ($statusFilter === 'new') ? 'selected' : '' ?>>New</option>
                    <option value="contacted" <?= ($statusFilter === 'contacted') ? 'selected' : '' ?>>Contacted</option>
                    <option value="enrolled" <?= ($statusFilter === 'enrolled') ? 'selected' : '' ?>>Enrolled</option>
                    <option value="archived" <?= ($statusFilter === 'archived') ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div>
                <button type="submit" class="adm-btn adm-btn-secondary">Filter</button>
                <?php if ($campusFilter || $statusFilter || $searchQuery): ?>
                    <a href="inquiries.php" class="adm-btn adm-btn-secondary" style="color:#EF4444;">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Inquiries Table -->
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">
            <span>Inquiry Records (<?= count($inquiries) ?>)</span>
        </div>
    </div>
    <div class="adm-card-body" style="padding:0;">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Parent / Student</th>
                        <th>Contact Numbers</th>
                        <th>Campus & Grade</th>
                        <th>Inquiry Message</th>
                        <th>Date Received</th>
                        <th>Status</th>
                        <th style="width:100px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inquiries)): ?>
                        <tr><td colspan="7" style="text-align:center; padding:28px; color:#94A3B8;">No inquiry records found matching criteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr>
                                <td>
                                    <strong style="color:var(--adm-primary); font-size:13.5px;"><?= htmlspecialchars($inq['name']) ?></strong><br>
                                    <span style="font-size:12px; color:#64748B;"><?= htmlspecialchars($inq['email']) ?></span>
                                </td>
                                <td>
                                    <span style="font-size:13px; font-weight:600; color:#0F172A;"><?= htmlspecialchars($inq['phone']) ?></span>
                                </td>
                                <td>
                                    <span class="adm-pill orange"><?= htmlspecialchars(ucfirst($inq['campus'])) ?></span>
                                    <?php if (!empty($inq['grade'])): ?>
                                        <div style="font-size:11.5px; color:#64748B; margin-top:2px;"><?= htmlspecialchars($inq['grade']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($inq['subject'])): ?>
                                        <div style="font-size:11px; font-weight:700; color:#7C3AED; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:3px;"><?= htmlspecialchars($inq['subject']) ?></div>
                                    <?php endif; ?>
                                    <p style="font-size:12.5px; color:#334155; line-height:1.4; max-width:320px; word-break:break-word; margin:0;"><?= nl2br(htmlspecialchars($inq['message'])) ?></p>
                                    
                                    <?php if (!empty($inq['reply_message'])): ?>
                                        <div style="margin-top:8px; padding:7px 10px; background:#F5F3FF; border-left:3px solid #7C3AED; border-radius:4px; max-width:320px;">
                                            <div style="font-size:10.5px; font-weight:700; color:#6D28D9; display:flex; justify-content:space-between; align-items:center;">
                                                <span>✓ Replied by <?= htmlspecialchars($inq['replied_by'] ?? 'Staff') ?></span>
                                                <span style="color:#8B5CF6; font-weight:normal; font-size:10px;"><?= !empty($inq['replied_at']) ? date('M d, h:i A', strtotime($inq['replied_at'])) : '' ?></span>
                                            </div>
                                            <p style="font-size:11.5px; color:#4C1D95; margin:3px 0 0; line-height:1.4; word-break:break-word;"><?= nl2br(htmlspecialchars($inq['reply_message'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size:12px; color:#64748B;"><?= date('M d, Y', strtotime($inq['created_at'])) ?><br><?= date('h:i A', strtotime($inq['created_at'])) ?></span>
                                </td>
                                <td>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="form_action" value="update_status">
                                        <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                        <select name="status" class="adm-select" style="padding:3px 6px; font-size:12px;" onchange="this.form.submit()">
                                            <option value="new" <?= ($inq['status'] === 'new') ? 'selected' : '' ?>>🔴 New</option>
                                            <option value="contacted" <?= ($inq['status'] === 'contacted') ? 'selected' : '' ?>>🔵 Contacted</option>
                                            <option value="enrolled" <?= ($inq['status'] === 'enrolled') ? 'selected' : '' ?>>🟢 Enrolled</option>
                                            <option value="archived" <?= ($inq['status'] === 'archived') ? 'selected' : '' ?>>⚪ Archived</option>
                                        </select>
                                    </form>
                                </td>
                                <td style="text-align:right; white-space:nowrap;">
                                    <div style="display:flex; gap:5px; justify-content:flex-end; align-items:center;">
                                        <button type="button" 
                                                class="adm-btn adm-btn-primary adm-btn-sm btn-open-reply" 
                                                data-id="<?= $inq['id'] ?>" 
                                                data-name="<?= htmlspecialchars($inq['name']) ?>" 
                                                data-email="<?= htmlspecialchars($inq['email']) ?>" 
                                                data-phone="<?= htmlspecialchars($inq['phone']) ?>" 
                                                data-campus="<?= htmlspecialchars(ucfirst($inq['campus'])) ?>"
                                                data-subject="<?= htmlspecialchars($inq['subject'] ?? '') ?>" 
                                                data-message="<?= htmlspecialchars($inq['message']) ?>" 
                                                data-reply="<?= htmlspecialchars($inq['reply_message'] ?? '') ?>" 
                                                style="background:#7C3AED; color:#fff; border-color:#7C3AED; padding:4px 8px; font-size:11.5px;" 
                                                title="Reply to inquiry">
                                            ✉️ Reply
                                        </button>

                                        <?php 
                                        $cleanPhone = preg_replace('/[^\d]/', '', $inq['phone']); 
                                        if (strlen($cleanPhone) >= 10): 
                                        ?>
                                            <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= urlencode('Assalamu Alaikum ' . $inq['name'] . ', thank you for contacting Averroes International School regarding: ' . ($inq['subject'] ?? 'your inquiry')) ?>" 
                                               target="_blank" 
                                               rel="noopener" 
                                               class="adm-btn adm-btn-secondary adm-btn-sm" 
                                               style="color:#10B981; border-color:#10B981; padding:4px 7px; font-size:11.5px;" 
                                               title="Chat via WhatsApp">
                                                💬
                                            </a>
                                        <?php endif; ?>

                                        <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="form_action" value="delete_inquiry">
                                            <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                            <button type="submit" class="adm-btn adm-btn-secondary adm-btn-sm btn-confirm-delete" data-title="inquiry from <?= htmlspecialchars($inq['name']) ?>" style="color:#EF4444; padding:4px 7px; font-size:11.5px;">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.65); z-index:99999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:14px; max-width:620px; width:100%; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); overflow:hidden; display:flex; flex-direction:column; max-height:90vh;">
        <div style="padding:16px 22px; background:#25093B; color:#fff; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="margin:0; font-size:16px; font-weight:700; color:#fff; font-family:'Poppins',sans-serif;">Send Inquiry Response</h3>
                <p style="margin:2px 0 0; font-size:11.5px; color:#F58220; font-weight:600;">Averroes Ltd CMS Admissions & Academic Desk</p>
            </div>
            <button type="button" id="closeReplyModal" style="background:none; border:none; color:#fff; font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        
        <form method="POST" action="inquiries.php" style="padding:20px 22px; overflow-y:auto;">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="reply_inquiry">
            <input type="hidden" name="inquiry_id" id="replyInquiryId" value="">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px; font-size:12px; background:#F8FAFC; padding:10px 14px; border-radius:8px; border:1px solid #E2E8F0;">
                <div>
                    <span style="color:#64748B; font-size:10.5px; text-transform:uppercase; font-weight:700;">Guardian / Student:</span>
                    <div style="font-weight:700; color:#0F172A; font-size:13px;" id="replyModalName"></div>
                </div>
                <div>
                    <span style="color:#64748B; font-size:10.5px; text-transform:uppercase; font-weight:700;">Email Address:</span>
                    <div style="font-weight:600; color:#2563EB; font-size:12.5px;" id="replyModalEmail"></div>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:11px; font-weight:700; text-transform:uppercase; color:#64748B; margin-bottom:4px;">Original Inquirer Query</label>
                <div id="replyModalOriginal" style="background:#FFF7ED; border-left:3px solid #F58220; padding:10px 12px; border-radius:0 6px 6px 0; font-size:12px; color:#9A3412; max-height:80px; overflow-y:auto; line-height:1.45;"></div>
            </div>

            <div class="adm-form-group" style="margin-bottom:14px;">
                <label class="adm-label" for="replySubject">Email Subject</label>
                <input type="text" id="replySubject" name="reply_subject" class="adm-input" placeholder="Response regarding your inquiry at Averroes" required>
            </div>

            <div class="adm-form-group" style="margin-bottom:14px;">
                <label class="adm-label" for="replyMessage">Reply Message (Text only — formatted into official Averroes email template)</label>
                <textarea id="replyMessage" name="reply_message" class="adm-textarea" rows="6" placeholder="Dear Guardian, thank you for contacting Averroes International School..." required></textarea>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:#334155; cursor:pointer;">
                    <input type="checkbox" name="send_email" id="replySendEmail" value="1" checked style="accent-color:#7C3AED; width:16px; height:16px;">
                    <span>Send response email to parent's email address</span>
                </label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #E2E8F0; padding-top:14px;">
                <button type="button" id="cancelReplyModal" class="adm-btn adm-btn-secondary">Cancel</button>
                <button type="submit" class="adm-btn adm-btn-primary" style="background:#25093B;">
                    <span>Save & Send Reply</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyModal = document.getElementById('replyModal');
    const closeBtn = document.getElementById('closeReplyModal');
    const cancelBtn = document.getElementById('cancelReplyModal');
    const replyInquiryId = document.getElementById('replyInquiryId');
    const replyModalName = document.getElementById('replyModalName');
    const replyModalEmail = document.getElementById('replyModalEmail');
    const replyModalOriginal = document.getElementById('replyModalOriginal');
    const replySubject = document.getElementById('replySubject');
    const replyMessage = document.getElementById('replyMessage');
    const replySendEmail = document.getElementById('replySendEmail');

    document.querySelectorAll('.btn-open-reply').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name') || 'Guardian';
            const email = this.getAttribute('data-email') || '';
            const campus = this.getAttribute('data-campus') || 'Lalmatia';
            const subject = this.getAttribute('data-subject') || 'Inquiry';
            const message = this.getAttribute('data-message') || '';
            const prevReply = this.getAttribute('data-reply') || '';

            replyInquiryId.value = id;
            replyModalName.textContent = name;
            replyModalEmail.textContent = email ? email : 'No email provided (chat/phone)';
            replyModalOriginal.textContent = message;

            replySubject.value = 'Response regarding your inquiry: ' + subject + ' — Averroes International School';
            
            if (prevReply) {
                replyMessage.value = prevReply;
            } else {
                replyMessage.value = `Dear ${name},\n\nThank you for reaching out to Averroes International School (${campus} Campus).\n\nIn response to your query regarding "${subject}", our admissions counselor is pleased to assist you. \n\nPlease let us know if you would like to schedule a campus tour or consultation.\n\nWarm regards,\nAdmissions Desk\nAverroes International School Ltd`;
            }

            if (!email || email.includes('@averroes.edu.bd') || email === 'chat-inquiry@averroes.edu.bd') {
                replySendEmail.checked = false;
                replySendEmail.disabled = true;
            } else {
                replySendEmail.checked = true;
                replySendEmail.disabled = false;
            }

            replyModal.style.display = 'flex';
        });
    });

    function closeReply() {
        replyModal.style.display = 'none';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeReply);
    if (cancelBtn) cancelBtn.addEventListener('click', closeReply);
    if (replyModal) {
        replyModal.addEventListener('click', function(e) {
            if (e.target === replyModal) closeReply();
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
