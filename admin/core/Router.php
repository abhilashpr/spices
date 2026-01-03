<?php
/**
 * Simple Router
 */

class Router
{
    private $routes = [];
    private $currentRoute = null;

    public function get(string $path, string $controller, string $method = 'index'): void
    {
        $this->addRoute('GET', $path, $controller, $method);
    }

    public function post(string $path, string $controller, string $method = 'index'): void
    {
        $this->addRoute('POST', $path, $controller, $method);
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = '/online-sp/admin';
        $path = str_replace($basePath, '', $requestUri);
        $path = $path ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchRoute($route['path'], $path)) {
                $this->currentRoute = $route;
                $this->callController($route['controller'], $route['action']);
                return;
            }
        }

        // 404
        http_response_code(404);
        echo "404 - Page not found";
    }

    private function matchRoute(string $route, string $path): bool
    {
        $route = rtrim($route, '/');
        $path = rtrim($path, '/');
        
        if ($route === $path) {
            return true;
        }

        // Simple pattern matching
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        return preg_match("#^{$pattern}$#", $path);
    }

    private function callController(string $controllerClass, string $method): void
    {
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller not found: {$controllerClass}");
        }

        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            throw new Exception("Method not found: {$controllerClass}::{$method}");
        }

        $controller->$method();
    }
}

