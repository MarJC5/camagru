<?php

namespace Camagru;

/**
 * Get the absolute path to the application directory.
 */
function app_path($path = '') {
    return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

/**
 * Get the absolute path to the public directory.
 */
function public_path($path = '') {
    return BASE_PATH . '/public' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

/**
 * Get public url
 */

function public_url($path = '') {
    return BASE_URL . ($path ? '/' . $path : $path);
}

/**
 * Get the absolute path to the storage directory.
 */
function storage_path($path = '') {
    return app_path('storage' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get the absolute path to the resources directory.
 */
function resources_path($path = '') {
    return app_path('resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get the absolute path to the components directory.
 */
function components_path($path = '') {
    return resources_path('components' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get the absolute path to the views directory.
 */
function views_path($path = '') {
    return resources_path('views' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get the absolute path to the CSS directory within the public directory.
 */
function css_path($path = '') {
    return public_path('css' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get css url
 */
function css_url($path = '') {
    return BASE_URL . '/css' . ($path ? '/' . $path : $path);
}

/**
 * Get the absolute path to the JS directory within the public directory.
 */
function js_path($path = '') {
    return public_path('js' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get js url
 */
function js_url($path = '') {
    return BASE_URL . '/js' . ($path ? '/' . $path : $path);
}

/**
 * Get the absolute path to the images directory within the public directory.
 */
function images_path($path = '') {
    return public_path('images' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get images url
 */
function images_url($path = '') {
    return BASE_URL . '/images' . ($path ? '/' . $path : $path);
}

/**
 * Load and return the content of a layout file (e.g., header, footer).
 */
function load_template($path) {
    ob_start();
    include $path;
    return ob_get_clean();
}

/**
 * Load and display the header layout.
 */
function get_header() {
    echo load_template(components_path('layouts/header.php'));
}

/**
 * Load and display the footer layout.
 */
function get_footer() {
    echo load_template(components_path('layouts/footer.php'));
}

/**
 * Get the current HTTP response code.
 */
function getHttpStatusMessage($code) {
    $statusCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ];

    return $statusCodes[$code] ?? 'Unknown status';
}