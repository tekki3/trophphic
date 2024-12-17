<?php 

namespace Trophphic\Core;

class Logger {
    public static function log($level, $message) {
        $file = $_ENV['LOG_FILE']; // Ensure this path matches your desired log location
        $directory = dirname($file);

        // Create the logs directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $time = date('Y-m-d H:i:s');
        $formattedMessage = "[$time] [$level] $message" . PHP_EOL;

        file_put_contents($file, $formattedMessage, FILE_APPEND);
    }

    public static function error($message) {
        self::log('ERROR', $message);
    }

    public static function info($message) {
        self::log('INFO', $message);
    }
}
