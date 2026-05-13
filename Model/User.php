<?php
require_once __DIR__ . '/../config/database.php';

class User {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByRememberToken(string $hashedToken): array|false {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE remember_token = ? LIMIT 1'
        );
        $stmt->execute([$hashedToken]);
        return $stmt->fetch();
    }

    public function create(
        string $name, string $email,
        string $password, string $phone = ''
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, phone, role)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $name, $email,
            password_hash($password, PASSWORD_BCRYPT),
            $phone, 'customer',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $allowed = ['name','email','phone','shipping_addresses','remember_token'];
        $sets = []; $values = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $sets[]   = "$col = ?";
                $values[] = $data[$col];
            }
        }
        if (empty($sets)) return;
        $values[] = $id;
        $this->db->prepare(
            'UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?'
        )->execute($values);
    }

    public function updatePassword(int $id, string $newPassword): void {
        $this->db->prepare(
            'UPDATE users SET password_hash = ? WHERE id = ?'
        )->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    public function clearRememberToken(int $id): void {
        $this->db->prepare(
            'UPDATE users SET remember_token = NULL WHERE id = ?'
        )->execute([$id]);
    }
}