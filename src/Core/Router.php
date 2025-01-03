<?php

namespace Trophphic\Core;

class Router
{
    private array $routes = [];
    private Request $request;
    private Response $response;
    private Logger $logger;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->logger = Logger::getInstance();
    }

    public function get(string $path, array $callback): void
    {
        $this->logger->debug('Registering GET route', ['path' => $path]);
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, array $callback): void
    {
        $this->logger->debug('Registering POST route', ['path' => $path]);
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        
        $this->logger->info('Processing request', [
            'path' => $path,
            'method' => $method
        ]);

        $callback = $this->routes[$method][$path] ?? false;

        if (!$callback) {
            $this->logger->warning('Route not found', [
                'path' => $path,
                'method' => $method
            ]);
            $this->response->setStatusCode(404);
            return "Not Found";
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $callback[0] = $controller;
            $this->logger->debug('Controller instantiated', [
                'controller' => get_class($controller)
            ]);
        }

        return call_user_func($callback, $this->request, $this->response);
    }
} 