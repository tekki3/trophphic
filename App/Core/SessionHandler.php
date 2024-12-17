<?php 

namespace Trophphic\Core;

use PDOException;
use SessionHandlerInterface;
use PDO;
use Trophphic\Core\Logger;

class SessionHandler implements SessionHandlerInterface {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getPdo();
    }

    public function open(string $savePath, string $sessionName): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read(string $id): string|false {
        try {
            $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['data'] ?? '';
        } catch (PDOException $e) {
            Logger::error("Error reading session: " . $e->getMessage());
            return false;
        }
    }
    
    public function write(string $id, string $data): bool {
        try {
            $stmt = $this->db->prepare("REPLACE INTO sessions (id, data, last_activity) VALUES (:id, :data, :last_activity)");
            return $stmt->execute([
                'id' => $id,
                'data' => $data,
                'last_activity' => time()
            ]);
        } catch (PDOException $e) {
            Logger::error("Error writing session: " . $e->getMessage());
            return false;
        }
    }
    
    public function destroy(string $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            Logger::error("Error destroying session: " . $e->getMessage());
            return false;
        }
    }
    
    public function gc(int $max_lifetime): int|false {
        try {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE last_activity < :time");
            return $stmt->execute(['time' => time() - $max_lifetime]);
        } catch (PDOException $e) {
            Logger::error("Error in garbage collection: " . $e->getMessage());
            return false;
        }
    }
}
