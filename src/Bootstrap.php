<?php

namespace Trophphic;

use Trophphic\Core\Request;
use Trophphic\Core\Response;
use Trophphic\Core\Router;

class Bootstrap
{
    public Router $router;
    public Request $request;
    public Response $response;
    private static $instance = null;

    public function __construct()
    {
        self::$instance = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        
        $this->loadRoutes();
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    private function loadRoutes(): void
    {
        require_once __DIR__ . '/../config/routes.php';
    }

    public function run()
    {
        echo $this->router->resolve();
    }
} 