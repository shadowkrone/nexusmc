<?php
declare(strict_types=1);

namespace App\Models;

class Page extends Model {
    protected static string $table = 'pages';

    public static function findBySlug(string $slug): ?array {
        $row = static::db()->fetch('SELECT * FROM ' . static::table() . ' WHERE slug = ?', [$slug]);
        return $row ?: null;
    }

    public static function allForNav(): array {
        return static::db()->fetchAll(
            'SELECT * FROM ' . static::table() . ' WHERE show_in_nav = 1 ORDER BY sort_order ASC, id ASC'
        );
    }
}
