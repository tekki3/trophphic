<?php

namespace Trophphic\Console;

abstract class Command
{
    protected function success(string $message): void
    {
        echo "\033[32m✓ $message\033[0m\n";
    }

    protected function error(string $message): void
    {
        echo "\033[31m✗ $message\033[0m\n";
    }

    protected function info(string $message): void
    {
        echo "\033[34mℹ $message\033[0m\n";
    }

    protected function createDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    protected function getStub(string $type): string
    {
        $stubPath = __DIR__ . "/stubs/$type.stub";
        if (!file_exists($stubPath)) {
            throw new \RuntimeException("Stub not found: $type");
        }
        return file_get_contents($stubPath);
    }
} 