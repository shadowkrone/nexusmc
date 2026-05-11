<?php
declare(strict_types=1);

namespace App\Models;

class ForumThread extends Model {
    protected static string $table = 'forum_threads';

    public static function forCategory(int $categoryId, int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        return static::db()->fetchAll(
            'SELECT t.*, u.username, u.role,
                (SELECT COUNT(*) FROM ' . DB_PREFIX . 'forum_posts p WHERE p.thread_id = t.id) AS reply_count,
                (SELECT p2.created_at FROM ' . DB_PREFIX . 'forum_posts p2 WHERE p2.thread_id = t.id ORDER BY p2.created_at DESC LIMIT 1) AS last_reply_at,
                (SELECT u2.username FROM ' . DB_PREFIX . 'forum_posts p2
                    JOIN ' . DB_PREFIX . 'users u2 ON p2.user_id = u2.id
                    WHERE p2.thread_id = t.id ORDER BY p2.created_at DESC LIMIT 1) AS last_reply_user
            FROM ' . DB_PREFIX . 'forum_threads t
            JOIN ' . DB_PREFIX . 'users u ON t.user_id = u.id
            WHERE t.category_id = ?
            ORDER BY t.pinned DESC, t.updated_at DESC
            LIMIT ? OFFSET ?',
            [$categoryId, $perPage, $offset]
        );
    }

    public static function recent(int $limit = 5): array {
        return static::db()->fetchAll(
            'SELECT t.*, u.username, c.name AS category_name, c.slug AS category_slug
            FROM ' . DB_PREFIX . 'forum_threads t
            JOIN ' . DB_PREFIX . 'users u ON t.user_id = u.id
            JOIN ' . DB_PREFIX . 'forum_categories c ON t.category_id = c.id
            ORDER BY t.updated_at DESC LIMIT ?',
            [$limit]
        );
    }

    public static function countForCategory(int $categoryId): int {
        return static::count('category_id = ?', [$categoryId]);
    }

    public static function withAuthor(int $id): ?array {
        $row = static::db()->fetch(
            'SELECT t.*, u.username, u.role, u.created_at AS user_joined, u.avatar
            FROM ' . DB_PREFIX . 'forum_threads t
            JOIN ' . DB_PREFIX . 'users u ON t.user_id = u.id
            WHERE t.id = ?',
            [$id]
        );
        return $row ?: null;
    }

    public static function incrementViews(int $id): void {
        static::db()->query('UPDATE ' . static::table() . ' SET views = views + 1 WHERE id = ?', [$id]);
    }
}
