<?php

namespace Trophphic\Core\Session;

use Trophphic\Core\Database;
use Trophphic\Core\Environment;
use Trophphic\Core\Logger;
use PDOException;

class DatabaseSessionHandler implements SessionInterface
{
    private Database $db;
    private string $sessionId;
    private array $data = [];
    private array $flash = [];
    private Logger $logger;

    public function __construct()
    {
        try {
            $this->logger = Logger::getInstance();
            $this->logger->info('DatabaseSessionHandler: Initializing');
            
            $this->db = Database::getInstance();
            
            // Verify database connection
            $this->db->query("SELECT 1")->execute();
            $this->logger->info('DatabaseSessionHandler: Database connection verified');
            
            $this->ensureSessionTable();
        } catch (PDOException $e) {
            $this->logger->error('DatabaseSessionHandler: Database error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function ensureSessionTable(): void
    {
        try {
            $this->logger->info('DatabaseSessionHandler: Creating sessions table');
            
            $sql = "CREATE TABLE IF NOT EXISTS `sessions` (
                `id` VARCHAR(128) NOT NULL,
                `data` TEXT,
                `last_activity` INT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            
            $result = $this->db->query($sql)->execute();
            $this->logger->info('DatabaseSessionHandler: Table creation attempt completed', ['result' => $result]);
            
        } catch (PDOException $e) {
            $this->logger->error('DatabaseSessionHandler: Failed to create table: ' . $e->getMessage());
            throw $e;
        }
    }

    public function start(): void
    {
        try {
            $this->logger->info('DatabaseSessionHandler: Starting session');
            
            if (isset($_COOKIE[session_name()])) {
                $this->sessionId = $_COOKIE[session_name()];
                $this->logger->info('DatabaseSessionHandler: Using existing session ID', ['id' => $this->sessionId]);
            } else {
                $this->sessionId = bin2hex(random_bytes(32));
                $this->logger->info('DatabaseSessionHandler: Created new session ID', ['id' => $this->sessionId]);
            }

            // Set cookie
            setcookie(session_name(), $this->sessionId, [
                'expires' => time() + ((int)Environment::get('SESSION_LIFETIME', 120) * 60),
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            // Insert initial session data
            $sql = "INSERT INTO sessions (id, data, last_activity) 
                   VALUES (:id, :data, :time) 
                   ON DUPLICATE KEY UPDATE 
                   last_activity = VALUES(last_activity)";
            
            $this->db->query($sql)
                ->bind(':id', $this->sessionId)
                ->bind(':data', json_encode([]))
                ->bind(':time', time())
                ->execute();
                
            $this->logger->info('DatabaseSessionHandler: Session initialized in database');
            
        } catch (\Exception $e) {
            $this->logger->error('DatabaseSessionHandler: Failed to start session: ' . $e->getMessage());
            throw $e;
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->updateData();
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function remove(string $key): void
    {
        unset($this->data[$key]);
        $this->updateData();
    }

    public function clear(): void
    {
        $this->data = [];
        $this->updateData();
    }

    public function flash(string $key, $value): void
    {
        $data = $this->data;
        $data['_flash'][$key] = $value;
        
        $this->db->query("UPDATE sessions SET data = :data WHERE id = :id")
            ->bind(':id', $this->sessionId)
            ->bind(':data', json_encode($data))
            ->execute();
    }

    public function getFlash(string $key, $default = null)
    {
        return $this->flash[$key] ?? $default;
    }

    public function regenerate(): bool
    {
        $newId = bin2hex(random_bytes(32));
        
        $this->db->query("UPDATE sessions SET id = :new_id WHERE id = :old_id")
            ->bind(':new_id', $newId)
            ->bind(':old_id', $this->sessionId)
            ->execute();

        $this->sessionId = $newId;
        setcookie(session_name(), $this->sessionId, [
            'expires' => time() + ((int)Environment::get('SESSION_LIFETIME', 120) * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        return true;
    }

    public function destroy(): void
    {
        $this->db->query("DELETE FROM sessions WHERE id = :id")
            ->bind(':id', $this->sessionId)
            ->execute();
        
        $this->data = [];
        $this->flash = [];
        
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    private function updateData(): void
    {
        $this->db->query("UPDATE sessions SET data = :data, last_activity = :time WHERE id = :id")
            ->bind(':id', $this->sessionId)
            ->bind(':data', json_encode($this->data))
            ->bind(':time', time())
            ->execute();
    }
} 