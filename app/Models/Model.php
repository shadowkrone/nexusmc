<?php
declare(strict_types=1);

namespace App\Models;

abstract class Model {
    protected static string $table = '';

    protected static function db(): \Core\Database {
        return app()->db;
    }

    protected static function table(): string {
        return DB_PREFIX . static::$table;
    }

    public static function find(int $id): ?array {
        $row = static::db()->fetch('SELECT * FROM ' . static::table() . ' WHERE id = ?', [$id]);
        return $row ?: null;
    }

    public static function all(string $order = 'id ASC'): array {
        return static::db()->fetchAll('SELECT * FROM ' . static::table() . " ORDER BY {$order}");
    }

    public static function create(array $data): int {
        return static::db()->insert(static::table(), $data);
    }

    public static function update(array $data, int $id): int {
        return static::db()->update(static::table(), $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int {
        return static::db()->delete(static::table(), 'id = ?', [$id]);
    }

    public static function count(string $where = '1', array $params = []): int {
        return static::db()->count(static::table(), $where, $params);
    }
}
