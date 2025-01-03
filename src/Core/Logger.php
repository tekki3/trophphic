<?php

namespace Trophphic\Core;

class Logger
{
    private static ?Logger $instance = null;
    private string $logPath;
    
    private const LEVEL_INFO = 'INFO';
    private const LEVEL_ERROR = 'ERROR';
    private const LEVEL_WARNING = 'WARNING';
    private const LEVEL_DEBUG = 'DEBUG';

    private function __construct()
    {
        $this->logPath = __DIR__ . '/../../logs';
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_INFO, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        
        $logMessage = "[$date $time] [$level] $message$contextStr" . PHP_EOL;
        $filename = $this->logPath . "/$date.log";
        
        file_put_contents($filename, $logMessage, FILE_APPEND);
    }
} 