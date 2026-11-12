<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Async Direct Media Upload API
 * Returns JSON { success: bool, url?: string, error?: string }
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/auth.php';

if (!is_admin_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
if (!verify_csrf($csrf)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF security token.']);
    exit;
}

$fileKey = isset($_FILES['file']) ? 'file' : (isset($_FILES['upload']) ? 'upload' : null);
if (!$fileKey) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file payload received.']);
    exit;
}

try {
    $uploadedPath = handle_direct_upload($fileKey);
    if (!$uploadedPath) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No file was uploaded or file was empty.']);
        exit;
    }

    echo json_encode([
        'success'  => true,
        'url'      => $uploadedPath,
        'full_url' => base_url($uploadedPath),
        'filename' => basename($uploadedPath),
    ]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}
