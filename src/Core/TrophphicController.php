<?php

namespace Trophphic\Core;

class TrophphicController
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    public function render(string $view, array $params = []): string
    {
        $this->logger->debug('Rendering view', ['view' => $view]);
        return $this->renderView($view, $params);
    }

    protected function renderView(string $view, array $params): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        
        $viewPath = __DIR__ . "/../../app/Views/$view.php";
        
        if (!file_exists($viewPath)) {
            $this->logger->error('View file not found', ['path' => $viewPath]);
            throw new \RuntimeException("View file not found: $view");
        }

        ob_start();
        include_once $viewPath;
        return ob_get_clean();
    }
} 