<?php

namespace Camagru\routes;

use Camagru\routes\Web;
use Camagru\routes\Api;
use Camagru\core\controllers\PageController;

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

                if (strpos($path, '/api/') === 0) {
                    header('Content-Type: application/json');
                    echo json_encode($output);
                } else {
                    echo $output; // For regular web routes
                }
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

    public static function redirect($path)
    {
        header('Location: ' . $path);
        exit();
    }

    public static function back()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public static function refresh()
    {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    public static function current()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function refreshWith($params)
    {
        $query = http_build_query($params);
        header('Location: ' . $_SERVER['REQUEST_URI'] . '?' . $query);
        exit();
    }

    public static function to($name, $params = [])
    {
        $routes = Web::routes();
        $routes = array_merge($routes, Api::routes());

        foreach ($routes as $route) {
            if (isset($route['name']) && $route['name'] == $name) {
                $path = $route['path'];
                foreach ($params as $key => $value) {
                    $path = str_replace('{' . $key . '}', $value, $path);
                }
                return $path;
            }
        }
    }
}
