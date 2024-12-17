<?php

namespace Trophphic\Core;

use PDO;
use PDOException;

class Database {
    private $pdo;

    public function __construct() {
        try {
            $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
            $this->pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Logger::error('Database Connection Error: ' . $e->getMessage());
            die('Database Connection Error: ' . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            Logger::error('Database Query Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getPdo() {
        return $this->pdo;
    }
}