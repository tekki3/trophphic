<?php

namespace Trophphic\Core;

class Controller
{
    public function render(string $view, array $params = []): string
    {
        return $this->renderView($view, $params);
    }

    protected function renderView(string $view, array $params): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        
        ob_start();
        include_once __DIR__ . "/../../app/Views/$view.php";
        return ob_get_clean();
    }
} 