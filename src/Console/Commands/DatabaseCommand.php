<?php

namespace Trophphic\Console\Commands;

use Trophphic\Console\Command;
use Trophphic\Core\Environment;
use PDO;

class DatabaseCommand extends Command
{
    private function getConnection(): PDO
    {
        $host = Environment::get('DB_HOST', '127.0.0.1');
        $port = Environment::get('DB_PORT', '3306');
        $username = Environment::get('DB_USERNAME', 'root');
        $password = Environment::get('DB_PASSWORD', '');

        return new PDO(
            "mysql:host=$host;port=$port",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function create(): void
    {
        $database = Environment::get('DB_DATABASE');
        
        try {
            $pdo = $this->getConnection();
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
            $this->success("Database created successfully: $database");
        } catch (\PDOException $e) {
            $this->error("Failed to create database: " . $e->getMessage());
        }
    }

    public function migrate(): void
    {
        $migrationsPath = __DIR__ . '/../../../database/migrations';
        $this->createDirectory($migrationsPath);

        $files = glob("$migrationsPath/*.php");
        sort($files);

        foreach ($files as $file) {
            require_once $file;
            $className = 'Migration_' . basename($file, '.php');
            $migration = new $className();
            
            try {
                $migration->up();
                $this->success("Migrated: " . basename($file));
            } catch (\Exception $e) {
                $this->error("Failed to migrate " . basename($file) . ": " . $e->getMessage());
            }
        }
    }

    public function seed(): void
    {
        $seedersPath = __DIR__ . '/../../../database/seeders';
        $this->createDirectory($seedersPath);

        $files = glob("$seedersPath/*.php");
        foreach ($files as $file) {
            require_once $file;
            $className = basename($file, '.php');
            $seeder = new $className();
            
            try {
                $seeder->run();
                $this->success("Seeded: " . basename($file));
            } catch (\Exception $e) {
                $this->error("Failed to seed " . basename($file) . ": " . $e->getMessage());
            }
        }
    }

    public function fresh(): void
    {
        $database = Environment::get('DB_DATABASE');
        
        try {
            $pdo = $this->getConnection();
            $pdo->exec("DROP DATABASE IF EXISTS `$database`");
            $pdo->exec("CREATE DATABASE `$database`");
            $this->success("Database refreshed successfully: $database");
            $this->migrate();
            $this->seed();
        } catch (\PDOException $e) {
            $this->error("Failed to refresh database: " . $e->getMessage());
        }
    }
} 