<?php

// Redirect all requests to the public directory
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// If accessing root, redirect to public
if ($uri === '/' || $uri === '') {
    require_once __DIR__.'/public/index.php';
    exit;
}

// For all other requests, check if it's a Laravel route
if (file_exists(__DIR__.'/public'.$uri)) {
    // If it's a real file in public, serve it
    return false;
} else {
    // Otherwise, let Laravel handle it
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    require_once __DIR__.'/public/index.php';
}