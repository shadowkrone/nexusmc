-- NexusMC Database Schema
-- Run this during installation

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `{PREFIX}users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(30) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('member','moderator','admin') NOT NULL DEFAULT 'member',
  `avatar`     VARCHAR(255) DEFAULT NULL,
  `banned`     TINYINT(1) NOT NULL DEFAULT 0,
  `last_seen`  DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}settings` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`   VARCHAR(100) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `slug`        VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `icon`        VARCHAR(10) NOT NULL DEFAULT '💬',
  `color`       VARCHAR(7) NOT NULL DEFAULT '#10b981',
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_threads` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED NOT NULL,
  `title`       VARCHAR(150) NOT NULL,
  `body`        TEXT NOT NULL,
  `views`       INT UNSIGNED NOT NULL DEFAULT 0,
  `pinned`      TINYINT(1) NOT NULL DEFAULT 0,
  `locked`      TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL,
  `updated_at`  DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `updated_at` (`updated_at`),
  CONSTRAINT `fk_thread_cat`  FOREIGN KEY (`category_id`) REFERENCES `{PREFIX}forum_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_thread_user` FOREIGN KEY (`user_id`)     REFERENCES `{PREFIX}users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_posts` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thread_id`  INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `body`       TEXT NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_post_thread` FOREIGN KEY (`thread_id`) REFERENCES `{PREFIX}forum_threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_post_user`   FOREIGN KEY (`user_id`)   REFERENCES `{PREFIX}users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}pages` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(150) NOT NULL,
  `slug`        VARCHAR(150) NOT NULL,
  `content`     LONGTEXT NOT NULL,
  `show_in_nav` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL,
  `updated_at`  DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings
INSERT IGNORE INTO `{PREFIX}settings` (`key`, `value`) VALUES
  ('site_name', 'NexusMC'),
  ('site_description', 'Minecraft Community'),
  ('registration_open', '1'),
  ('maintenance_mode', '0'),
  ('server_ip', ''),
  ('server_port', '25565'),
  ('discord_url', ''),
  ('store_url', ''),
  ('enabled_plugins', '[]'),
  ('home_hero_title', ''),
  ('home_hero_subtitle', ''),
  ('home_show_server', '1'),
  ('home_show_threads', '1'),
  ('home_show_activity', '1'),
  ('home_show_join_cta', '1'),
  ('social_youtube', ''),
  ('social_twitter', ''),
  ('social_tiktok', ''),
  ('social_instagram', ''),
  ('site_favicon', '');

SET FOREIGN_KEY_CHECKS = 1;
