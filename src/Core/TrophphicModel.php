<?php

namespace Trophphic\Core;

abstract class TrophphicModel
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find($id)
    {
        return $this->db->query("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id")
            ->bind(':id', $id)
            ->find();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM {$this->table}")
            ->findAll();
    }

    public function create(array $data)
    {
        $data = $this->filterFillable($data);
        $fields = array_keys($data);
        $placeholders = array_map(fn($field) => ":$field", $fields);
        
        $fieldsStr = implode(', ', $fields);
        $placeholdersStr = implode(', ', $placeholders);

        $query = $this->db->query("INSERT INTO {$this->table} ($fieldsStr) VALUES ($placeholdersStr)");

        foreach ($data as $key => $value) {
            $query->bind(":$key", $value);
        }

        return $query->execute();
    }

    public function update($id, array $data)
    {
        $data = $this->filterFillable($data);
        $fields = array_map(fn($field) => "$field = :$field", array_keys($data));
        $fieldsStr = implode(', ', $fields);

        $query = $this->db->query("UPDATE {$this->table} SET $fieldsStr WHERE {$this->primaryKey} = :id");
        
        foreach ($data as $key => $value) {
            $query->bind(":$key", $value);
        }
        $query->bind(':id', $id);

        return $query->execute();
    }

    public function delete($id): bool
    {
        return $this->db->query("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id")
            ->bind(':id', $id)
            ->execute();
    }

    public function where(string $field, $value, string $operator = '=')
    {
        return $this->db->query("SELECT * FROM {$this->table} WHERE $field $operator :value")
            ->bind(':value', $value)
            ->findAll();
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }
} 