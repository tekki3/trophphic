<?php

namespace Trophphic\Core\Session;

use Trophphic\Core\Database;
use Trophphic\Core\Logger;

class DatabaseSessionHandler implements \SessionHandlerInterface, SessionInterface
{
    private static ?DatabaseSessionHandler $instance = null;
    private Database $db;
    private Logger $logger;
    private array $data = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->logger = Logger::getInstance();
        $this->logger->info('Database session handler initialized');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function open(string $path, string $name): bool
    {
        $this->logger->info('DatabaseSessionHandler: Starting session');
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $this->logger->info('DatabaseSessionHandler: Using existing session ID', ['id' => $id]);
        
        $result = $this->db->query('SELECT data FROM sessions WHERE id = :id')
            ->bind(':id', $id)
            ->find();

        if ($result) {
            $this->data = json_decode($result['data'], true) ?? [];
            return $result['data'];
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        $sql = "INSERT INTO sessions (id, data, last_activity) 
                   VALUES (:id, :data, :time) 
                   ON DUPLICATE KEY UPDATE 
                   data = VALUES(data),
                   last_activity = VALUES(last_activity)";

        try {
            $this->db->query($sql)
                ->bind(':id', $id)
                ->bind(':data', $data)
                ->bind(':time', time())
                ->execute();
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Session write failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function destroy(?string $id = null): bool
    {
        try {
            if ($id !== null) {
                $this->db->query('DELETE FROM sessions WHERE id = :id')
                    ->bind(':id', $id)
                    ->execute();
            }
            $this->clear();
            session_destroy();
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Session destroy failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {
        try {
            $old = time() - $max_lifetime;
            $result = $this->db->query('DELETE FROM sessions WHERE last_activity < :old')
                ->bind(':old', $old)
                ->execute();
            return $result ? 1 : 0;
        } catch (\Exception $e) {
            $this->logger->error('Session garbage collection failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $_SESSION[$key] = $value;
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->data = $_SESSION;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function clear(): void
    {
        $this->data = [];
        session_unset();
    }

    public function flash(string $key, $value): void
    {
        $flash = $this->get('_flash', []);
        $flash[$key] = $value;
        $this->set('_flash', $flash);
    }

    public function getFlash(string $key, $default = null)
    {
        $flash = $this->get('_flash', []);
        $value = $flash[$key] ?? $default;
        unset($flash[$key]);
        $this->set('_flash', $flash);
        return $value;
    }

    public function regenerate(): bool
    {
        return session_regenerate_id(true);
    }

    public function remove(string $key): void
    {
        unset($this->data[$key], $_SESSION[$key]);
    }
} 