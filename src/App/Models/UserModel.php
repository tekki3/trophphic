<?php


namespace Trophphic\App\Models;

use Trophphic\Core\Model;

class UserModel extends Model {
    protected $table = 'users';

    public function getAll() {
        return self::fetch("SELECT * FROM {$this->table}");
    }

    public function find($id) {
        return self::fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }
}