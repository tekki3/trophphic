<?php

namespace Trophphic\Core;

use Trophphic\Core\Request;
use Trophphic\Core\Response;
use Trophphic\Core\Router;
use Trophphic\Core\Environment;
use Trophphic\Core\Session\SessionManager;
use Trophphic\Core\Security\CSRF;

class Bootstrap
{
    public Router $router;
    public Request $request;
    public Response $response;
    private static $instance = null;

    public function __construct()
    {
        self::$instance = $this;
        
        // Load environment variables first
        Environment::load(__DIR__ . '/../../.env');
        
        // Then initialize session
        SessionManager::initialize();
        
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        
        $this->loadRoutes();
        
        // Initialize CSRF protection
        CSRF::init();
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    private function loadRoutes(): void
    {
        require_once __DIR__ . '/../../config/routes.php';
    }

    public function run()
    {
        echo $this->router->resolve();
    }
} 