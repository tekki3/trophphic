<?php
namespace Trophphic\Core;

use Exception;

class TrophphicController {
    public function view($view, $data = []) {
        $viewPath = '../src/App/Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            extract($data);
            require_once $viewPath;
        } else {
            Logger::error("View $view not found.");
            throw new Exception("View $view not found.");
        }
    }

    public function model($model) {
        $modelPath = '\\Trophphic\\App\\Models\\' . $model;
        if (class_exists($modelPath)) {
            return new $modelPath;
        } else {
            Logger::error("Model $model not found.");
            throw new Exception("Model $model not found.");
        }
    }

    public function validateCsrfToken($token) {
        if (!Security::validateCsrfToken($token)) {
            Logger::error("Invalid CSRF token.");
            throw new Exception("Invalid CSRF token.");
        }
    }
}