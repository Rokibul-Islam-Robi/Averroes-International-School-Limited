<?php
declare(strict_types=1);

/**
 * Averroes International School
 * Unified Serverless Router for Vercel
 */

// Parse the request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl  = parse_url($requestUri);
$path       = rawurldecode($parsedUrl['path'] ?? '/');

// Helper to serve static files with proper MIME types
function serve_static_if_exists(string $relativePath): void {
    $staticFile = __DIR__ . '/' . ltrim($relativePath, '/');
    if (file_exists($staticFile) && is_file($staticFile)) {
        $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
        $mimes = [
            'css'   => 'text/css; charset=utf-8',
            'js'    => 'application/javascript; charset=utf-8',
            'svg'   => 'image/svg+xml',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'webp'  => 'image/webp',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
        ];
        if (isset($mimes[$ext])) {
            header('Content-Type: ' . $mimes[$ext]);
        }
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($staticFile);
        exit;
    }
}

// Serve assets or admin static files directly if hit through PHP router
if (str_starts_with($path, '/assets/') || str_starts_with($path, '/admin/css/') || str_starts_with($path, '/admin/js/')) {
    serve_static_if_exists($path);
}

// Route: API
if ($path === '/api/inquiry' || $path === '/api/inquiry.php' || str_starts_with($path, '/api/')) {
    require __DIR__ . '/api/inquiry.php';
    exit;
}

// Route: First In Bangladesh / Program rewrite
if (preg_match('#^/first-in-bangladesh/([^/]+)$#', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/program.php';
    exit;
}
if (preg_match('#^/program/([^/]+)$#', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/program.php';
    exit;
}

// Route: Admin
if (str_starts_with($path, '/admin')) {
    $adminSub = trim(substr($path, 6), '/');
    if ($adminSub === '' || $adminSub === 'index.php') {
        require __DIR__ . '/admin/index.php';
        exit;
    }

    if (!str_ends_with($adminSub, '.php')) {
        $adminSub .= '.php';
    }

    $adminTarget = __DIR__ . '/admin/' . $adminSub;
    if (file_exists($adminTarget) && is_file($adminTarget)) {
        require $adminTarget;
        exit;
    }

    // Default admin fallback
    require __DIR__ . '/admin/index.php';
    exit;
}

// Route: Root level PHP pages
$clean = trim($path, '/');
if ($clean === '' || $clean === 'index.php') {
    require __DIR__ . '/index.php';
    exit;
}

$page = $clean;
if (!str_ends_with($page, '.php')) {
    $page .= '.php';
}

$target = __DIR__ . '/' . $page;
if (file_exists($target) && is_file($target)) {
    require $target;
    exit;
}

// Default fallback to index.php
require __DIR__ . '/index.php';
