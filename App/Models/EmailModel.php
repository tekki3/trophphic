<?php


namespace Trophphic\App\Models;

use Trophphic\Core\Model;


class EmailModel extends Model {
    protected $table = 'emails';


    public function getAll() {
        return self::fetch("SELECT * FROM {$this->table}");
    }

    public function find($id) {
        return self::fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function insert($recipient, $subject, $body, $sender) {
        $sql = "INSERT INTO {$this->table} (recipient, subject, body, sender) VALUES (?, ?, ?, ?)";
        return self::query($sql, [$recipient, $subject, $body, $sender]);
    }

    public function update($id, $recipient, $subject, $body, $sender) {
        $sql = "UPDATE {$this->table} SET recipient = ?, subject = ?, body = ?, sender = ? WHERE id = ?";
        return self::query($sql, [$recipient, $subject, $body, $sender, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return self::query($sql, [$id]);
    }
}