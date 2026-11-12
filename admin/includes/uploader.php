<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Media & Image Direct Upload Engine
 * Supports direct file upload from admin PC, local validation, and automatic relative path generation.
 */

if (!defined('AV_UPLOADS_DIR')) {
    define('AV_UPLOADS_DIR', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads');
}

/**
 * Handle direct file upload from $_FILES array
 *
 * @param string $inputName Name of the <input type="file" name="...">
 * @param string $destDir Target absolute or relative directory
 * @return string|null Relative web path (e.g. 'assets/img/uploads/aisl_xyz.jpg') or null if no file uploaded
 * @throws RuntimeException If an actual upload error occurred (corrupt, invalid format, too large)
 */
function handle_direct_upload(string $inputName, string $destDir = AV_UPLOADS_DIR): ?string {
    if (!isset($_FILES[$inputName])) {
        return null;
    }

    $file = $_FILES[$inputName];

    // If no file was selected in file input
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // Check PHP upload error codes
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE specified in the form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        $msg = $errorMessages[$file['error']] ?? 'Unknown upload error occurred (Code: ' . $file['error'] . ')';
        throw new RuntimeException($msg);
    }

    // Size validation (Max 15MB)
    $maxSize = 15 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new RuntimeException('File size exceeds the 15MB maximum allowed limit.');
    }

    // Extension & MIME verification
    $originalName = (string)$file['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException("Invalid file format (.$extension). Allowed formats: JPG, PNG, WEBP, SVG, GIF.");
    }

    $tmpPath = (string)$file['tmp_name'];
    if (!is_uploaded_file($tmpPath)) {
        throw new RuntimeException('Security check failed: File was not uploaded via HTTP POST.');
    }

    // Validate MIME type
    $allowedMimes = [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/webp',
        'image/gif',
        'image/svg+xml',
        'text/plain', // some servers detect SVG as text/plain
        'text/xml',
        'application/xml'
    ];

    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($detectedMime, $allowedMimes, true)) {
            throw new RuntimeException("Detected MIME type '$detectedMime' is not an allowed image format.");
        }
    }

    // Ensure target folder exists
    if (!is_dir($destDir)) {
        if (!mkdir($destDir, 0755, true) && !is_dir($destDir)) {
            throw new RuntimeException("Failed to create destination directory: $destDir");
        }
    }

    // Generate safe, clean, unique filename
    $cleanBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    $cleanBase = substr($cleanBase, 0, 30);
    $uniqueSuffix = date('Ymd_His') . '_' . bin2hex(random_bytes(3));
    $finalFilename = "aisl_{$cleanBase}_{$uniqueSuffix}.{$extension}";

    $targetFile = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $finalFilename;

    if (!move_uploaded_file($tmpPath, $targetFile)) {
        throw new RuntimeException("Could not move uploaded file to destination path.");
    }

    // Return uniform relative path for database and public web display
    return 'assets/img/uploads/' . $finalFilename;
}

/**
 * Resolve final image path: prefers newly uploaded file; if none, keeps text input path.
 *
 * @param string $fileInputName
 * @param string|null $textInputValue
 * @param string $fallback
 * @return string
 */
function resolve_image_path(string $fileInputName, ?string $textInputValue, string $fallback = ''): string {
    try {
        $uploaded = handle_direct_upload($fileInputName);
        if ($uploaded !== null) {
            return $uploaded;
        }
    } catch (Throwable $e) {
        if (function_exists('set_flash')) {
            set_flash('error', 'Image Upload Warning: ' . $e->getMessage());
        }
    }

    $textVal = trim((string)$textInputValue);
    if ($textVal !== '') {
        return $textVal;
    }

    return $fallback;
}

/**
 * Render a standardized, professional direct image upload widget with live preview
 */
function render_image_upload_field(
    string $label,
    string $fileInputName,
    string $textInputName,
    ?string $currentValue = '',
    string $hint = '',
    string $accept = 'image/*'
): string {
    $cur = htmlspecialchars((string)$currentValue, ENT_QUOTES, 'UTF-8');
    $uid = 'up_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $fileInputName) . '_' . substr(md5($textInputName), 0, 6);
    $fullPreviewSrc = !empty($currentValue) ? base_url($currentValue) : '';

    $hasImage = !empty($currentValue);
    $previewStyle = $hasImage ? '' : 'display:none;';
    $placeholderStyle = $hasImage ? 'display:none;' : '';

    $html = '<div class="adm-img-widget" id="widget_' . $uid . '">';
    $html .= '<label class="adm-label">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label>';
    
    $html .= '<div class="adm-img-widget-body">';
    // Preview Thumbnail Box
    $html .= '<div class="adm-img-preview-box" id="pbox_' . $uid . '">';
    $html .= '<img src="' . $fullPreviewSrc . '" alt="Preview" class="adm-img-thumb" id="img_' . $uid . '" style="' . $previewStyle . '">';
    $html .= '<div class="adm-img-empty" id="empty_' . $uid . '" style="' . $placeholderStyle . '">';
    $html .= '<span class="adm-empty-icon">🖼️</span>';
    $html .= '<span class="adm-empty-text">No Image</span>';
    $html .= '</div>';
    $html .= '</div>';

    // Controls Column
    $html .= '<div class="adm-img-controls">';
    
    // Choose File Button & Feedback
    $html .= '<div class="adm-file-picker-row">';
    $html .= '<label for="' . $uid . '_file" class="adm-file-pick-btn">';
    $html .= '📁 <span>Choose Local File to Upload</span>';
    $html .= '</label>';
    $html .= '<input type="file" name="' . htmlspecialchars($fileInputName, ENT_QUOTES, 'UTF-8') . '" id="' . $uid . '_file" class="adm-file-hidden" accept="' . htmlspecialchars($accept, ENT_QUOTES, 'UTF-8') . '" data-target-img="img_' . $uid . '" data-target-empty="empty_' . $uid . '" data-target-badge="badge_' . $uid . '" data-target-text="txt_' . $uid . '">';
    $html .= '<span class="adm-file-badge" id="badge_' . $uid . '">Direct upload from computer</span>';
    $html .= '</div>';

    // Text Input for Direct Path or Keeping Current
    $html .= '<div class="adm-path-row">';
    $html .= '<span class="adm-path-lbl">Asset / Web Path:</span>';
    $html .= '<input type="text" name="' . htmlspecialchars($textInputName, ENT_QUOTES, 'UTF-8') . '" id="txt_' . $uid . '" class="adm-input adm-input-path" value="' . $cur . '" placeholder="e.g. assets/img/uploads/photo.jpg or assets/img/subpages/photo.jpg">';
    $html .= '</div>';

    if (!empty($hint)) {
        $html .= '<div class="adm-hint">' . htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    $html .= '</div>'; // adm-img-controls
    $html .= '</div>'; // adm-img-widget-body
    $html .= '</div>'; // adm-img-widget

    return $html;
}
