<?php

namespace Trophphic\Core\Session;

use Trophphic\Core\Environment;

class FileSessionHandler implements SessionInterface
{
    private array $flash = [];

    public function __construct()
    {
        $lifetime = (int)Environment::get('SESSION_LIFETIME', 120);
        ini_set('session.gc_maxlifetime', $lifetime * 60);
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        session_unset();
    }

    public function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, $default = null)
    {
        return $this->flash[$key] ?? $default;
    }

    public function regenerate(): bool
    {
        return session_regenerate_id(true);
    }

    public function destroy(?string $id = null): bool
    {
        session_destroy();
        return true;
    }
} 