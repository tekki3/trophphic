<?php

namespace Trophphic\Core;

class Security {
    public static function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function getCsrfToken() {
        return $_SESSION['csrf_token'] ?? '';
    }

    public static function validateCsrfToken($token) {
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sanitizeOutput($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}