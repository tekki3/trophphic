<?php

namespace Trophphic\Core;

class Request
{
    private array $params = [];

    public function __construct()
    {
        $this->params = array_merge($_GET, $_POST);
    }

    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    public function input(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->params;
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->params, array_flip($keys));
    }
} 