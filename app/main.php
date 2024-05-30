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