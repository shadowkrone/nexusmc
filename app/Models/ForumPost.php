<?php
declare(strict_types=1);

namespace App\Models;

class ForumPost extends Model {
    protected static string $table = 'forum_posts';

    public static function forThread(int $threadId, int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        return static::db()->fetchAll(
            'SELECT p.*, u.username, u.role, u.avatar, u.created_at AS user_joined,
                (SELECT COUNT(*) FROM ' . DB_PREFIX . 'forum_posts p2 WHERE p2.user_id = u.id) AS user_post_count
            FROM ' . DB_PREFIX . 'forum_posts p
            JOIN ' . DB_PREFIX . 'users u ON p.user_id = u.id
            WHERE p.thread_id = ?
            ORDER BY p.created_at ASC
            LIMIT ? OFFSET ?',
            [$threadId, $perPage, $offset]
        );
    }

    public static function countForThread(int $threadId): int {
        return static::count('thread_id = ?', [$threadId]);
    }

    public static function recent(int $limit = 5): array {
        return static::db()->fetchAll(
            'SELECT p.*, u.username, t.title AS thread_title, t.id AS thread_id
            FROM ' . DB_PREFIX . 'forum_posts p
            JOIN ' . DB_PREFIX . 'users u ON p.user_id = u.id
            JOIN ' . DB_PREFIX . 'forum_threads t ON p.thread_id = t.id
            ORDER BY p.created_at DESC LIMIT ?',
            [$limit]
        );
    }
}
