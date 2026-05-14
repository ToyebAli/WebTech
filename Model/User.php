<?php


class User {
    private PDO $db;

    public function __construct() {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = new PDO(
                'mysql:host=127.0.0.1;dbname=ecommerce_store;charset=utf8mb4',
                'root', '',
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }
        $this->db = $pdo;
    }

    public function findByEmail(string $email): array|false {
        $s = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $s->execute([$email]);
        return $s->fetch();
    }

    public function findById(int $id): array|false {
        $s = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $s->execute([$id]);
        return $s->fetch();
    }

    public function findByToken(string $hashedToken): array|false {
        $s = $this->db->prepare('SELECT * FROM users WHERE remember_token = ? LIMIT 1');
        $s->execute([$hashedToken]);
        return $s->fetch();
    }

    public function create(string $name, string $email, string $password, string $phone = ''): int {
        $s = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, phone, role)
             VALUES (?, ?, ?, ?, ?)'
        );
        $s->execute([
            $name, $email,
            password_hash($password, PASSWORD_BCRYPT),
            $phone ?: null,
            'customer',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $name, string $email,
                                   string $phone, string $addrJson): void {
        $s = $this->db->prepare(
            'UPDATE users SET name=?, email=?, phone=?, shipping_addresses=? WHERE id=?'
        );
        $s->execute([$name, $email, $phone ?: null, $addrJson, $id]);
    }

    public function updatePassword(int $id, string $newPassword): void {
        $s = $this->db->prepare('UPDATE users SET password_hash=? WHERE id=?');
        $s->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    public function saveToken(int $id, string $hashedToken): void {
        $s = $this->db->prepare('UPDATE users SET remember_token=? WHERE id=?');
        $s->execute([$hashedToken, $id]);
    }

    public function clearToken(int $id): void {
        $s = $this->db->prepare('UPDATE users SET remember_token=NULL WHERE id=?');
        $s->execute([$id]);
    }
}