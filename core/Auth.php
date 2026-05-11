<?php
declare(strict_types=1);

namespace Core;

class Auth {
    private Database $db;
    private Session $session;
    private ?array $user = null;

    public function __construct(Database $db, Session $session) {
        $this->db      = $db;
        $this->session = $session;
    }

    public function check(): bool {
        return $this->user() !== null;
    }

    public function user(): ?array {
        if ($this->user !== null) return $this->user;
        $id = $this->session->get('user_id');
        if (!$id) return null;
        $this->user = $this->db->fetch(
            'SELECT * FROM ' . DB_PREFIX . 'users WHERE id = ? AND banned = 0',
            [$id]
        ) ?: null;
        return $this->user;
    }

    public function isAdmin(): bool {
        return ($this->user()['role'] ?? '') === 'admin';
    }

    public function isModerator(): bool {
        $role = $this->user()['role'] ?? '';
        return in_array($role, ['admin', 'moderator']);
    }

    public function attempt(string $email, string $password): bool {
        $user = $this->db->fetch(
            'SELECT * FROM ' . DB_PREFIX . 'users WHERE email = ? AND banned = 0',
            [strtolower(trim($email))]
        );
        if (!$user || !password_verify($password, $user['password'])) return false;
        $this->login($user);
        return true;
    }

    public function login(array $user): void {
        $this->session->regenerate();
        $this->session->set('user_id', $user['id']);
        $this->user = $user;
        $this->db->update(
            DB_PREFIX . 'users',
            ['last_seen' => date('Y-m-d H:i:s')],
            'id = ?', [$user['id']]
        );
    }

    public function logout(): void {
        $this->session->remove('user_id');
        $this->session->regenerate();
        $this->user = null;
    }

    public function register(string $username, string $email, string $password): int|false {
        $exists = $this->db->fetch(
            'SELECT id FROM ' . DB_PREFIX . 'users WHERE email = ? OR username = ?',
            [strtolower(trim($email)), $username]
        );
        if ($exists) return false;

        return $this->db->insert(DB_PREFIX . 'users', [
            'username'   => $username,
            'email'      => strtolower(trim($email)),
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => 'member',
            'created_at' => date('Y-m-d H:i:s'),
            'last_seen'  => date('Y-m-d H:i:s'),
        ]);
    }
}
