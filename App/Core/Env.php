<?php

namespace Trophphic\Core;

use Trophphic\Core\Logger;

class Env {
    public static function load(string $filePath): void {
        if (!file_exists($filePath)) {
            throw new \Exception(".env file not found at " . $filePath);
        }

        try {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                Logger::error('Error reading .env file at ' . $filePath);
                throw new \Exception("Error reading .env file at " . $filePath);
            }

            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) {
                    continue;
                }

                if (strpos($line, '=') === false) {
                    Logger::error('Invalid line in .env file:'. $line);
                    throw new \Exception("Invalid line in .env file: " . $line);
                }

                [$name, $value] = explode('=', $line, 2);
                $_ENV[trim($name)] = trim($value);
            }
        } catch (\Exception $e) {
            Logger::error('Error loading .env file: ' . $e->getMessage());
            throw $e;
        }
    }
}