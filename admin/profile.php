<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Administrator Profile & Password Manager
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Administrator Profile & Security';

$adminId = $_SESSION['admin_id'] ?? 1;

// Handle Form Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security validation failed (CSRF).');
        header('Location: profile.php');
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '') {
        set_flash('error', 'Username and email cannot be empty.');
    } else {
        try {
            // Update username & email
            $upStmt = $pdo->prepare("UPDATE admins SET username = ?, email = ? WHERE id = ?");
            $upStmt->execute([$username, $email, $adminId]);
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_email'] = $email;

            // Password change check
            if ($newPass !== '') {
                if ($newPass !== $confirmPass) {
                    set_flash('error', 'New password and confirmation do not match.');
                } elseif (strlen($newPass) < 6) {
                    set_flash('error', 'New password must be at least 6 characters.');
                } else {
                    $passHash = password_hash($newPass, PASSWORD_BCRYPT);
                    $pStmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
                    $pStmt->execute([$passHash, $adminId]);
                    set_flash('success', 'Profile and password updated successfully!');
                }
            } else {
                set_flash('success', 'Profile details updated successfully!');
            }
        } catch (Throwable $e) {
            set_flash('error', 'Database error: ' . $e->getMessage());
        }
    }

    header('Location: profile.php');
    exit;
}

// Fetch current admin
$admin = ['username' => 'admin', 'email' => 'admin@aisl.edu.bd'];
if ($pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? LIMIT 1");
        $stmt->execute([$adminId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) $admin = $row;
    } catch (Throwable $e) {}
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Poppins',sans-serif; font-size:18px; color:var(--adm-primary); font-weight:700;">
            Administrator Profile & Credentials
        </h2>
        <p style="font-size:12.5px; color:#64748B;">Manage your login credentials, email address, and security password.</p>
    </div>
</div>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="adm-card" style="max-width:600px;">
        <div class="adm-card-header">
            <div class="adm-card-title"><span>👤 Account Credentials</span></div>
        </div>
        <div class="adm-card-body">
            <div class="adm-form-group">
                <label class="adm-label">Username <span class="req">*</span></label>
                <input type="text" name="username" class="adm-input" value="<?= htmlspecialchars($admin['username']) ?>" required>
            </div>

            <div class="adm-form-group">
                <label class="adm-label">Email Address <span class="req">*</span></label>
                <input type="email" name="email" class="adm-input" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
            </div>

            <div style="margin-top:24px; border-top:1px dashed #E2E8F0; padding-top:20px;">
                <h4 style="font-size:14px; font-weight:700; color:var(--adm-primary); margin-bottom:12px;">Change Password (Optional)</h4>

                <div class="adm-form-group">
                    <label class="adm-label">New Password</label>
                    <input type="password" name="new_password" class="adm-input" placeholder="Leave blank to keep current password">
                </div>

                <div class="adm-form-group">
                    <label class="adm-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="adm-input" placeholder="Re-enter new password">
                </div>
            </div>

            <div style="margin-top:20px; text-align:right;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    💾 Update Profile & Security
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
