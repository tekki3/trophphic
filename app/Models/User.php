<?php

namespace App\Models;

use Trophphic\Core\TrophphicModel;

class User extends TrophphicModel
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name',
        'email',
        'password'
    ];

    public function update($id, array $data): bool
    {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($fields)) {
            return false;
        }

        $setClause = implode(', ', array_map(fn($field) => "$field = :$field", array_keys($fields)));
        $sql = "UPDATE {$this->table} SET $setClause WHERE id = :id";
        
        try {
            $query = $this->db->query($sql);
            foreach ($fields as $field => $value) {
                $query->bind(":$field", $value);
            }
            $query->bind(':id', $id);
            return $query->execute();
        } catch (\Exception $e) {
            $this->logger->error('Failed to update user', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 