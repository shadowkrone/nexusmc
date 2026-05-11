<?php
declare(strict_types=1);

namespace App\Models;

class ForumCategory extends Model {
    protected static string $table = 'forum_categories';

    public static function withStats(): array {
        return static::db()->fetchAll(
            'SELECT c.*,
                (SELECT COUNT(*) FROM ' . DB_PREFIX . 'forum_threads t WHERE t.category_id = c.id) AS thread_count,
                (SELECT COUNT(*) FROM ' . DB_PREFIX . 'forum_posts p
                    JOIN ' . DB_PREFIX . 'forum_threads t2 ON p.thread_id = t2.id
                    WHERE t2.category_id = c.id) AS post_count,
                (SELECT t3.title FROM ' . DB_PREFIX . 'forum_threads t3
                    WHERE t3.category_id = c.id ORDER BY t3.updated_at DESC LIMIT 1) AS last_thread_title,
                (SELECT t3.id FROM ' . DB_PREFIX . 'forum_threads t3
                    WHERE t3.category_id = c.id ORDER BY t3.updated_at DESC LIMIT 1) AS last_thread_id,
                (SELECT t3.updated_at FROM ' . DB_PREFIX . 'forum_threads t3
                    WHERE t3.category_id = c.id ORDER BY t3.updated_at DESC LIMIT 1) AS last_activity
            FROM ' . DB_PREFIX . 'forum_categories c
            ORDER BY c.sort_order ASC, c.id ASC'
        );
    }

    public static function findBySlug(string $slug): ?array {
        $row = static::db()->fetch('SELECT * FROM ' . static::table() . ' WHERE slug = ?', [$slug]);
        return $row ?: null;
    }
}
