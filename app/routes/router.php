<?php

namespace Camagru\routes;

use Camagru\routes\Web;
use Camagru\routes\Api;
use Camagru\helpers\Session;
use Camagru\core\database\Runner;
use Camagru\helpers\Logger;
use function Camagru\loadView;

class Router
{
    public static function route($requestUri, $requestMethod)
    {
        // Check if the application has been migrated
        if (!Runner::isMigrated() && $requestUri !== '/setup') {
            $requestUri = '/install';
        } else if (!Runner::isMigrated() && $requestUri === '/setup' && $requestMethod === 'GET') {
            // Show the installation page
            echo loadView('page/install.php');
            return;
        }

        $parsedUrl = parse_url($requestUri);
        $path = $parsedUrl['path'] ?? '/';
        $query = $parsedUrl['query'] ?? ''; // Capture the query part

        $method = $requestMethod;
        $routes = array_merge(Web::routes(), Api::routes());
        
        foreach ($routes as $route) {
            if ($method == $route['method'] && self::matchRoute($path, $route['path'])) {
                $params = self::extractParams($path, $route['path'] . $query);
                $params = array_merge($params, self::extractQueryParams($query));
                
                // Capture the output of the controller action
                $output = call_user_func_array($route['action'], [$params]);
                if (strpos($path, '/api/') === 0) {
                    header('Content-Type: application/json');
                    echo json_encode($output);
                } else {
                    echo $output;
                }
                return;
            }
        }

        // If no route is matched, show a 404 page
        Router::redirect('error', ['code' => 404]);
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

    private static function extractQueryParams($queryParams)
    {
        $params = [];
        parse_str($queryParams, $params);
        return $params;
    }

    public static function redirect($name, $params = [])
    {
        $path = self::to($name, $params);
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
        $routes = array_merge(Web::routes(), Api::routes());

        foreach ($routes as $route) {
            if (isset($route['name']) && $route['name'] == $name) {
                $path = $route['path'];
        
                foreach ($params as $key => $value) {
                    if (strpos($path, '{' . $key . '}') === false) {
                        continue;
                    } else if (strpos($path, '?' . $key) !== false) {
                        $path = str_replace('?' . $key, $value, $path);
                    } else {
                        $path = str_replace('{' . $key . '}', $value, $path);
                    }
                }

                return $path;
            }
        }
    }
}
