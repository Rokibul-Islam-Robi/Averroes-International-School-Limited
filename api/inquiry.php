<?php
declare(strict_types=1);

/**
 * Averroes International School
 * Public Inquiry API Endpoint (api/inquiry.php)
 * Handles AJAX submissions from Contact Form, Admission Form, and AI Chat Widget
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = get_db(false);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Support JSON payload or standard POST
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?? [];
} else {
    $input = $_POST;
}

$source  = trim((string)($input['source'] ?? 'general'));
$name    = trim((string)($input['name'] ?? $input['parent_name'] ?? ''));
$email   = trim((string)($input['email'] ?? ''));
$phone   = trim((string)($input['phone'] ?? ''));
$campus  = trim((string)($input['campus'] ?? 'central'));
$grade   = trim((string)($input['grade'] ?? $input['grade_applying'] ?? ''));
$message = trim((string)($input['message'] ?? $input['query'] ?? ''));
$subject = trim((string)($input['subject'] ?? ''));

// If source is AI Chat Widget
if ($source === 'ai_chat_widget') {
    if ($message === '') {
        echo json_encode(['success' => false, 'message' => 'Query cannot be empty.']);
        exit;
    }
    if ($name === '') {
        $name = 'Website Visitor (Chat Widget)';
    }
    if ($email === '') {
        $email = 'chat-inquiry@averroes.edu.bd';
    }
    if ($phone === '') {
        $phone = 'Website Live Chat';
    }
    if ($subject === '') {
        $subject = 'Chat Query: ' . mb_substr($message, 0, 60) . (mb_strlen($message) > 60 ? '...' : '');
    }
}

// Fallbacks for campus
if (!in_array($campus, ['lalmatia', 'banasree', 'uttara', 'central'], true)) {
    $campus = 'central';
}

if ($name === '' || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Name and message are required.']);
    exit;
}

if ($pdo !== null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO contact_inquiries 
            (name, email, phone, campus, grade, subject, message, status, ip_address, created_at)
            VALUES (:name, :email, :phone, :campus, :grade, :subject, :message, 'new', :ip_address, NOW())
        ");
        $stmt->execute([
            ':name'       => $name,
            ':email'      => $email,
            ':phone'      => $phone,
            ':campus'     => $campus,
            ':grade'      => $grade !== '' ? $grade : null,
            ':subject'    => $subject !== '' ? $subject : 'Website Inquiry',
            ':message'    => $message,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Your inquiry has been successfully submitted and forwarded to our admissions desk.',
            'id'      => $pdo->lastInsertId(),
        ]);
        exit;
    } catch (Throwable $e) {
        error_log('[API Inquiry Error] ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error while saving inquiry. Please try again or call our helpline directly.',
        ]);
        exit;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection unavailable.',
    ]);
    exit;
}
