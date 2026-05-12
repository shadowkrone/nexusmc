<?php
declare(strict_types=1);

namespace App\Models;

class User extends Model {
    protected static string $table = 'users';

    public static function findByUsername(string $username): ?array {
        $row = static::db()->fetch('SELECT * FROM ' . static::table() . ' WHERE username = ?', [$username]);
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $row = static::db()->fetch('SELECT * FROM ' . static::table() . ' WHERE email = ?', [strtolower($email)]);
        return $row ?: null;
    }

    public static function getRecent(int $limit = 10): array {
        return static::db()->fetchAll(
            'SELECT * FROM ' . static::table() . ' ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public static function paginate(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        return static::db()->fetchAll(
            'SELECT * FROM ' . static::table() . ' ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$perPage, $offset]
        );
    }

    public static function roleLabel(string $role): string {
        return match($role) {
            'admin'     => t('role.admin'),
            'moderator' => t('role.moderator'),
            default     => t('role.member'),
        };
    }

    public static function roleBadgeClass(string $role): string {
        return match($role) {
            'admin'     => 'bg-red-500/20 text-red-400 border border-red-500/30',
            'moderator' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
            default     => 'bg-slate-700 text-slate-300 border border-slate-600',
        };
    }
}
