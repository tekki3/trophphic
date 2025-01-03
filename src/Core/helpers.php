<?php

use Trophphic\Core\Session\SessionManager;
use Trophphic\Core\Form\Errors;

if (!function_exists('errors')) {
    function errors(): Errors
    {
        $errors = SessionManager::getInstance()->getFlash('errors', []);
        return new Errors($errors);
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        return SessionManager::getInstance()->getFlash("old_$key") ?? $default;
    }
}