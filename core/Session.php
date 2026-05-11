<?php
declare(strict_types=1);

namespace Core;

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_strict_mode', '1');
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public function destroy(): void {
        session_destroy();
        $_SESSION = [];
    }

    public function regenerate(): void {
        session_regenerate_id(true);
    }
}
