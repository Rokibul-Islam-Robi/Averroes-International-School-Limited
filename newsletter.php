<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Newsletter Subscription Handler
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db(false);

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit;
    }
    header('Location: ' . base_url());
    exit;
}

// Read email from either $_POST or JSON raw body
$email = $_POST['email'] ?? '';
if (empty($email)) {
    $rawInput = file_get_contents('php://input');
    if ($rawInput) {
        $data = json_decode($rawInput, true);
        if (is_array($data) && isset($data['email'])) {
            $email = (string)$data['email'];
        }
    }
}

$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$result = subscribe_newsletter($pdo, (string)$email, $ip);

if ($isAjax) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($result);
    exit;
}

if ($result['success']) {
    set_flash('success', $result['message']);
} else {
    set_flash('danger', $result['message']);
}

header('Location: ' . base_url() . '#nl');
exit;
