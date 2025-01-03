<?php

namespace Trophphic\Core\Session;

use Trophphic\Core\Environment;
use Trophphic\Core\Logger;

class SessionManager
{
    private static ?SessionInterface $handler = null;
    private static Logger $logger;

    public static function initialize(): void
    {
        self::$logger = Logger::getInstance();
        
        $driver = trim(Environment::get('SESSION_DRIVER', 'file'));
        self::$logger->info('Session driver from environment', ['driver' => $driver]);
        
        try {
            if ($driver === 'database') {
                self::$handler = new DatabaseSessionHandler();
                self::$logger->info('Database session handler initialized');
            } else {
                self::$handler = new FileSessionHandler();
                self::$logger->info('File session handler initialized');
            }

            self::$handler->start();
            self::$logger->info('Session started successfully');
            
        } catch (\Exception $e) {
            self::$logger->error('Failed to initialize session: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance(): SessionInterface
    {
        if (self::$handler === null) {
            self::initialize();
        }
        return self::$handler;
    }
} 
