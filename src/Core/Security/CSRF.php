<?php

namespace Trophphic\Core\Security;

use Trophphic\Core\Session\DatabaseSessionHandler;
use Trophphic\Core\Logger;

class CSRF
{
    private static string $tokenKey = '_csrf_token';
    private static Logger $logger;
    private static DatabaseSessionHandler $session;

    public static function init(): void
    {
        self::$logger = Logger::getInstance();
        self::$session = DatabaseSessionHandler::getInstance();
        self::$session->start();
    }

    public static function generateToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::$session->start();
        }
        
        $token = bin2hex(random_bytes(32));
        self::$logger->debug('Setting CSRF token', ['token' => substr($token, 0, 8) . '...']);
        self::$session->set(self::$tokenKey, $token);
        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::$session->start();
        }
        
        $storedToken = self::getStoredToken();
        self::$logger->debug('CSRF validation', [
            'provided_token' => $token ? substr($token, 0, 8) . '...' : null,
            'stored_token' => $storedToken ? substr($storedToken, 0, 8) . '...' : null
        ]);
        return $token && $storedToken && hash_equals($storedToken, $token);
    }

    public static function getStoredToken(): ?string
    {
        return self::$session->get(self::$tokenKey);
    }

    public static function getTokenField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }
} 