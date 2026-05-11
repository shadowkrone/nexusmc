<?php
declare(strict_types=1);

// ── Auto-install tables ──────────────────────────────────────────────────────
(function (): void {
    if (db()->fetch("SHOW TABLES LIKE '" . DB_PREFIX . "app_forms'")) return;

    $p = DB_PREFIX;
    $stmts = [
        "CREATE TABLE `{$p}app_forms` (
          `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `name`         VARCHAR(100) NOT NULL,
          `slug`         VARCHAR(100) NOT NULL UNIQUE,
          `description`  TEXT,
          `active`       TINYINT(1) NOT NULL DEFAULT 1,
          `one_per_user` TINYINT(1) NOT NULL DEFAULT 1,
          `created_at`   DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE `{$p}app_questions` (
          `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `form_id`    INT UNSIGNED NOT NULL,
          `label`      VARCHAR(200) NOT NULL,
          `type`       ENUM('text','textarea','select','radio') NOT NULL DEFAULT 'textarea',
          `options`    TEXT,
          `required`   TINYINT(1) NOT NULL DEFAULT 1,
          `sort_order` INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE `{$p}applications` (
          `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `form_id`    INT UNSIGNED NOT NULL,
          `user_id`    INT UNSIGNED NOT NULL,
          `status`     ENUM('pending','accepted','denied') NOT NULL DEFAULT 'pending',
          `created_at` DATETIME NOT NULL,
          `updated_at` DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE `{$p}app_answers` (
          `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `application_id` INT UNSIGNED NOT NULL,
          `question_id`    INT UNSIGNED NOT NULL,
          `answer`         TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE `{$p}app_notes` (
          `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `application_id` INT UNSIGNED NOT NULL,
          `admin_id`       INT UNSIGNED NOT NULL,
          `body`           TEXT NOT NULL,
          `created_at`     DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];

    foreach ($stmts as $sql) {
        db()->getPdo()->exec($sql);
    }
})();

// ── Autoload plugin classes ──────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $prefix = 'Plugin\\Applications\\';
    if (!str_starts_with($class, $prefix)) return;
    $file = __DIR__ . '/' . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';
    if (file_exists($file)) require_once $file;
});

// ── Register routes ──────────────────────────────────────────────────────────
$r = app()->router;
$ns = 'Plugin\\Applications\\Controllers\\';

// User routes
$r->get('/applications',    $ns . 'ApplicationController@list');
$r->get('/my-applications', $ns . 'ApplicationController@mine');
$r->get('/apply/{slug}',    $ns . 'ApplicationController@apply');
$r->post('/apply/{slug}',   $ns . 'ApplicationController@submit');

// Admin — specific paths before parameterised ones
$r->get('/admin/applications',                       $ns . 'AdminApplicationController@index');
$r->get('/admin/applications/forms',                 $ns . 'AdminApplicationController@forms');
$r->get('/admin/applications/forms/new',             $ns . 'AdminApplicationController@newForm');
$r->post('/admin/applications/forms/create',         $ns . 'AdminApplicationController@createForm');
$r->get('/admin/applications/forms/{id}/edit',       $ns . 'AdminApplicationController@editForm');
$r->post('/admin/applications/forms/{id}/update',    $ns . 'AdminApplicationController@updateForm');
$r->post('/admin/applications/forms/{id}/delete',    $ns . 'AdminApplicationController@deleteForm');
$r->get('/admin/applications/{id}',                  $ns . 'AdminApplicationController@review');
$r->post('/admin/applications/{id}/status',          $ns . 'AdminApplicationController@setStatus');
$r->post('/admin/applications/{id}/note',            $ns . 'AdminApplicationController@addNote');
$r->post('/admin/applications/{id}/delete',          $ns . 'AdminApplicationController@delete');

// ── Admin sidebar hook ───────────────────────────────────────────────────────
app()->plugins->on('admin_nav_html', function (): string {
    $active = str_contains(
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
        '/admin/applications'
    );
    $cls = $active
        ? 'bg-brand/15 text-brand'
        : 'text-slate-400 hover:text-white hover:bg-slate-800';
    return '<a href="' . url('admin/applications') . '"
       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors ' . $cls . '">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      Ans&#248;gninger
    </a>';
});
