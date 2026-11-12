<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Secure File & Media Upload Helper
 */

if (!function_exists('handle_image_upload')) {
    /**
     * Handle secure image uploads
     *
     * @param array $file $_FILES['input_name']
     * @param string $subDir Subdirectory inside assets (defaults to 'uploads')
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    function handle_image_upload(array $file, string $subDir = 'uploads'): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'path' => null, 'error' => 'Invalid file parameter.'];
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'path' => null, 'error' => null];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'error' => 'Upload error code: ' . $file['error']];
        }

        // Limit size to 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'path' => null, 'error' => 'File size exceeds maximum limit of 5MB.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (!in_array($ext, $allowedExts, true)) {
            return ['success' => false, 'path' => null, 'error' => 'Invalid file type. Allowed types: JPG, PNG, WEBP, SVG, GIF.'];
        }

        // MIME type check
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'text/plain', // some systems report SVG as text/plain
            'text/xml',
            'image/svg',
        ];
        if (!in_array($mime, $allowedMimes, true)) {
            return ['success' => false, 'path' => null, 'error' => 'Invalid MIME type (' . $mime . ').'];
        }

        // Generate safe storage path
        $targetDir = APP_ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $subDir;
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                return ['success' => false, 'path' => null, 'error' => 'Failed to create upload destination directory.'];
            }
        }

        $safeName = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $targetDir . DIRECTORY_SEPARATOR . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => false, 'path' => null, 'error' => 'Failed to move uploaded file. Check directory permissions.'];
        }

        $relativePath = 'assets/' . trim($subDir, '/\\') . '/' . $safeName;
        return ['success' => true, 'path' => $relativePath, 'error' => null];
    }
}
