<?php
/**
 * Router for PHP's built-in web server
 *
 * Usage: php -S localhost:8000 router.php
 *
 * This router serves static files directly and routes all other requests
 * to the main application.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// List of static file extensions to serve directly
$staticExtensions = [
    'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
    'woff', 'woff2', 'ttf', 'eot', 'otf', // Fonts
    'map', 'json', 'html', 'htm' // HTML files
];

// Get file extension
$extension = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

// Check if it's a static file request
if (in_array($extension, $staticExtensions)) {
    $filePath = __DIR__ . $uri;

    if (file_exists($filePath) && is_file($filePath)) {
        // Set correct MIME type
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'otf' => 'font/otf',
            'map' => 'application/json',
            'html' => 'text/html',
            'htm' => 'text/html',
        ];

        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        header('Content-Type: ' . $contentType);
        header('Cache-Control: public, max-age=31536000');

        readfile($filePath);
        return true;
    }
}

// Route everything else to index.php
require __DIR__ . '/index.php';
