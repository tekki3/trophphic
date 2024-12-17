<?php

namespace Trophphic\Core;

use PDO;

class Model {
    protected static $db;

    public function __construct() {
        if (!self::$db) {
            $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
            try {
                self::$db = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                Logger::error('Database Connection Error: ' . $e->getMessage());
                die('Database Connection Error: ' . $e->getMessage());
            }
        }
    }

    public static function query($sql, $params = []) {
        try {
            $stmt = self::$db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            Logger::error('Database Query Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function fetch($sql, $params = []) {
        try {
            $stmt = self::query($sql, $params);
            if ($stmt === false) {
                return false;
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            Logger::error('Database Fetch Error: ' . $e->getMessage());
            return false;
        }
    }
}