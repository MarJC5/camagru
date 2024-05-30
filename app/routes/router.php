<?php

namespace Camagru\routes;

use Camagru\routes\Web;
use Camagru\routes\Api;
use Camagru\controllers\PageController;

class Router
{
    public static function route($requestUri, $requestMethod)
    {
        $parsedUrl = parse_url($requestUri);
        $path = $parsedUrl['path'] ?? '/';
        $method = $requestMethod;

        $routes = Web::routes();
        $routes = array_merge($routes, Api::routes());

        foreach ($routes as $route) {
            if ($method == $route['method'] && self::matchRoute($path, $route['path'])) {
                $params = self::extractParams($path, $route['path']);
                // Capture the output of the controller action
                $output = call_user_func_array($route['action'], $params);

                echo $output; // Output the view content
                return;
            }
        }

        // If no route is matched, show a 404 page
        PageController::error(404);
    }

    private static function matchRoute($path, $routePath)
    {
        // Replace placeholders with regex patterns
        $pattern = preg_replace('#\{[\w]+\}#', '([^/]+)', $routePath);
        return preg_match('#^' . $pattern . '$#', $path);
    }

    private static function extractParams($path, $routePath)
    {
        $params = [];
        // Replace placeholders with regex patterns
        $pattern = preg_replace('#\{[\w]+\}#', '([^/]+)', $routePath);
        if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
            array_shift($matches);  // Remove the full match from the beginning
            $params = array_values($matches);
        }
        return $params;
    }
}
