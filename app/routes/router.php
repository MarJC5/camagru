<?php

namespace Camagru\routes;

use Camagru\routes\Web;
use Camagru\routes\Api;
use Camagru\helpers\Logger;
use Camagru\core\middlewares\Auth;
use Camagru\core\middlewares\Migration;
use function Camagru\loadView;

/**
 * Class Router
 * Handles routing for the application.
 */
class Router
{
    /**
     * Route the incoming request to the appropriate controller action.
     *
     * @param string $requestUri The requested URI.
     * @param string $method The HTTP method of the request.
     */
    public static function route($requestUri, $method)
    {
        // Check if the application has been migrated
        if (!Migration::ready($requestUri, $method)) {
            $requestUri = '/install';
        }
        
        $parsedUrl = parse_url($requestUri);
        $path = $parsedUrl['path'] ?? '/';
        $query = $parsedUrl['query'] ?? ''; // Capture the query part
        $routes = array_merge(Web::routes(), Api::routes());

        foreach ($routes as $route) {
            if ($method == $route['method'] && self::matchRoute($path, $route['path'])) {
                $routeInfo = ['method' => $method, 'path' => $path, 'uri' => $requestUri, 'query' => $query, 'route' => $route];
                $params = self::extractParams($path, $route['path'] . $query);
                $params = array_merge($params, self::extractQueryParams($query), $routeInfo);

                // Check for secure option and call middleware if present
                if (isset($route['secure'])) {
                    if (!Auth::handle($route['secure'], $params)) {
                        return;
                    }
                }

                // Logger::log($route['method'] . ' ' .  $route['path']);
                // Logger::log('PARAMS' . ' ' .  print_r($params, true));
                
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

    /**
     * Match the given path to a route pattern.
     *
     * @param string $path The requested path.
     * @param string $routePath The route pattern to match against.
     * @return bool True if the path matches the route pattern, false otherwise.
     */
    private static function matchRoute($path, $routePath)
    {
        // Replace placeholders with regex patterns
        $pattern = preg_replace('#\{[\w]+\}#', '([^/]+)', $routePath);
        return preg_match('#^' . $pattern . '$#', $path);
    }

    /**
     * Extract parameters from the path based on the route pattern.
     *
     * @param string $path The requested path.
     * @param string $routePath The route pattern to extract parameters from.
     * @return array An associative array of parameters.
     */
    private static function extractParams($path, $routePath)
    {
        $params = [];
        $paramNames = [];
        preg_match_all('#\{([\w]+)\}#', $routePath, $paramNames);
        $paramNames = $paramNames[1];
        
        // Replace placeholders with regex patterns
        $pattern = preg_replace('#\{[\w]+\}#', '([^/]+)', $routePath);
        
        if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
            array_shift($matches);  // Remove the full match from the beginning
            foreach ($paramNames as $index => $name) {
                $params[$name] = $matches[$index];
            }
        }
        
        return $params;
    }

    /**
     * Extract query parameters from the URL.
     *
     * @param string $queryParams The query string from the URL.
     * @return array An associative array of query parameters.
     */
    private static function extractQueryParams($queryParams)
    {
        $params = [];
        parse_str($queryParams, $parsedParams);

        foreach ($parsedParams as $key => $value) {
            $params[$key] = $value;
        }

        return $params;
    }

    /**
     * Redirect to a named route with optional parameters.
     *
     * @param string $name The name of the route to redirect to.
     * @param array $params An associative array of parameters for the route.
     */
    public static function redirect($name, $params = [])
    {
        $path = self::to($name, $params);
        header('Location: ' . $path);
        exit();
    }

    /**
     * Redirect to the previous page.
     */
    public static function back()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    /**
     * Refresh the current page.
     */
    public static function refresh()
    {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    /**
     * Get the current URL.
     *
     * @return string The current URL.
     */
    public static function current()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Refresh the page with new query parameters.
     *
     * @param array $params An associative array of query parameters.
     */
    public static function refreshWith($params)
    {
        $query = http_build_query($params);
        header('Location: ' . $_SERVER['REQUEST_URI'] . '?' . $query);
        exit();
    }

    /**
     * Clear all query parameters from the URL.
     */
    public static function clearParams()
    {
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }

    /**
     * Generate a URL for a named route with optional parameters.
     *
     * @param string $name The name of the route.
     * @param array $params An associative array of parameters for the route.
     * @return string The generated URL.
     */
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
