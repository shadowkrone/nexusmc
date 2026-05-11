<?php
declare(strict_types=1);

namespace App\Models;

class Setting extends Model {
    protected static string $table = 'settings';

    public static function get(string $key): ?string {
        $row = static::db()->fetch(
            'SELECT value FROM ' . static::table() . ' WHERE `key` = ?',
            [$key]
        );
        return $row ? $row['value'] : null;
    }

    public static function set(string $key, string $value): void {
        $exists = static::db()->fetch(
            'SELECT id FROM ' . static::table() . ' WHERE `key` = ?',
            [$key]
        );
        if ($exists) {
            static::db()->update(static::table(), ['value' => $value], '`key` = ?', [$key]);
        } else {
            static::db()->insert(static::table(), ['key' => $key, 'value' => $value]);
        }
    }

    public static function allMap(): array {
        $rows = static::db()->fetchAll('SELECT `key`, `value` FROM ' . static::table());
        $map  = [];
        foreach ($rows as $row) $map[$row['key']] = $row['value'];
        return $map;
    }

    public static function setMany(array $data): void {
        foreach ($data as $key => $value) {
            static::set($key, (string) $value);
        }
    }
}
