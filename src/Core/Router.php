<?php

namespace Trophphic\Core;

use Trophphic\Core\Logger;


class Router {
    private static $routes = [];

    public static function get($route, $action) {
        self::$routes['GET'][$route] = $action;
    }

    public static function post($route, $action) {
        self::$routes['POST'][$route] = $action;
    }

    public static function resolve($method, $uri) {
        $routes = self::$routes[$method] ?? [];
        $uri = trim($uri, '/'); // Normalize URI
    
        foreach ($routes as $route => $action) {
            if (trim($route, '/') === $uri) { // Normalize route
                return self::dispatch($action);
            }
        }
    
        http_response_code(404);
        Logger::error("404 Not Found: $method $uri");
        exit;
    }
    

    private static function dispatch($action) {
        [$controller, $method] = explode('@', $action);
        $controllerClass = '\\Trophphic\\App\\Controllers\\' . $controller;

        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            if (method_exists($controllerInstance, $method)) {
                return call_user_func([$controllerInstance, $method]);
            }
        }

        http_response_code(500);
        Logger::error("500 Internal Server Error: Controller or method not found.");
        exit;
    }

    public static function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        self::resolve($method, $uri);
    }
}