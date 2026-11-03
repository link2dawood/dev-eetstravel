<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);
if (preg_match('~^/snappymail/(?:data|_data_)(?:/|$)~i', $uri)) {
    http_response_code(404);
    return;
}
if (preg_match('~^/snappymail/.*\.php(?:/|$)~i', $uri)) {
    require_once __DIR__.'/public/index.php';
    return;
}

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
