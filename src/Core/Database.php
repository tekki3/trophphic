<?php

namespace Trophphic\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private ?PDOStatement $statement = null;
    private Logger $logger;

    private function __construct()
    {
        $this->logger = Logger::getInstance();
        
        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            Environment::get('DB_CONNECTION', 'mysql'),
            Environment::get('DB_HOST', '127.0.0.1'),
            Environment::get('DB_PORT', '3306'),
            Environment::get('DB_DATABASE', 'trophphic')
        );

        try {
            $this->pdo = new PDO($dsn, 
                Environment::get('DB_USERNAME', 'root'),
                Environment::get('DB_PASSWORD', ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            $this->logger->info('Database connection established successfully');
        } catch (PDOException $e) {
            $this->logger->error('Database connection failed: ' . $e->getMessage());
            throw new PDOException($e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query(string $sql): self
    {
        $this->logger->debug('Preparing SQL query', ['query' => $sql]);
        $this->statement = $this->pdo->prepare($sql);
        return $this;
    }

    public function bind($param, $value, $type = null): self
    {
        if (is_null($type)) {
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
        }
        
        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    public function execute(): bool
    {
        try {
            $result = $this->statement->execute();
            $this->logger->debug('Query executed successfully');
            return $result;
        } catch (PDOException $e) {
            $this->logger->error('Query execution failed', [
                'error' => $e->getMessage(),
                'query' => $this->statement->queryString
            ]);
            throw $e;
        }
    }

    public function findAll(): array
    {
        $this->execute();
        return $this->statement->fetchAll();
    }

    public function find()
    {
        $this->execute();
        return $this->statement->fetch();
    }

    public function findColumn()
    {
        $this->execute();
        return $this->statement->fetchColumn();
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
} 