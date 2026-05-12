<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\Setting;
use App\Models\Page;

class AdminController extends Controller {
    public function dashboard(): void {
        $this->requireAdmin();
        $stats = [
            'users'   => User::count(),
            'threads' => ForumThread::count(),
            'posts'   => ForumPost::count(),
        ];
        $recentUsers = User::getRecent(8);
        $this->renderAdmin('dashboard', compact('stats', 'recentUsers'));
    }

    public function users(): void {
        $this->requireAdmin();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = trim($_GET['search'] ?? '');
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $where  = $search ? "WHERE u.username LIKE ? OR u.email LIKE ?" : '';
        $params = $search ? ["%{$search}%", "%{$search}%"] : [];

        $users = db()->fetchAll(
            "SELECT u.*,
                (SELECT COUNT(*) FROM " . DB_PREFIX . "forum_posts p WHERE p.user_id = u.id) AS post_count,
                (SELECT COUNT(*) FROM " . DB_PREFIX . "forum_threads t WHERE t.user_id = u.id) AS thread_count
             FROM " . DB_PREFIX . "users u
             {$where}
             ORDER BY u.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $total = $search
            ? db()->fetch("SELECT COUNT(*) AS c FROM " . DB_PREFIX . "users u {$where}", $params)['c']
            : User::count();
        $pages = (int)ceil($total / $perPage);

        $this->renderAdmin('users', compact('users', 'page', 'pages', 'total', 'search'));
    }

    public function banUser(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { $this->json(['error' => 'Invalid'], 403); }
        $banned = (int)($_POST['banned'] ?? 1);
        User::update(['banned' => $banned], $id);
        $this->json(['ok' => true, 'banned' => $banned]);
    }

    public function changeRole(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { $this->json(['error' => t('flash.invalid_request')], 403); }
        $role = $_POST['role'] ?? 'member';
        if (!in_array($role, ['member', 'moderator', 'admin'])) {
            $this->json(['error' => t('admin.users.invalid_role')], 400);
        }
        if ($id === auth()->user()['id']) {
            $this->json(['error' => t('admin.users.cannot_change_own_role')], 400);
        }
        User::update(['role' => $role], $id);
        $this->json(['ok' => true, 'role' => $role]);
    }

    public function getUser(int $id): void {
        $this->requireAdmin();
        $user = User::find($id);
        if (!$user) { $this->json(['error' => t('admin.users.not_found')], 404); }
        unset($user['password']);
        $this->json(['ok' => true, 'user' => $user]);
    }

    public function updateUser(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { $this->json(['error' => t('flash.invalid_request')], 403); }

        $user = User::find($id);
        if (!$user) { $this->json(['error' => t('admin.users.not_found')], 404); }

        $data     = [];
        $errors   = [];

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username && $username !== $user['username']) {
            if (strlen($username) < 3 || strlen($username) > 30) {
                $errors[] = t('flash.username_length');
            } elseif (preg_match('/[^a-zA-Z0-9_\-]/', $username)) {
                $errors[] = t('flash.username_chars');
            } else {
                $taken = db()->fetch('SELECT id FROM ' . DB_PREFIX . 'users WHERE username = ? AND id != ?', [$username, $id]);
                if ($taken) $errors[] = t('flash.username_taken');
                else $data['username'] = $username;
            }
        }

        if ($email && $email !== $user['email']) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = t('flash.invalid_email');
            } else {
                $taken = db()->fetch('SELECT id FROM ' . DB_PREFIX . 'users WHERE email = ? AND id != ?', [strtolower($email), $id]);
                if ($taken) $errors[] = t('flash.email_taken');
                else $data['email'] = strtolower($email);
            }
        }

        if ($password !== '') {
            if (strlen($password) < 8) {
                $errors[] = t('flash.new_password_min');
            } else {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
        }

        if (!empty($errors)) {
            $this->json(['ok' => false, 'errors' => $errors]);
        }

        if (!empty($data)) {
            User::update($data, $id);
        }

        $updated = User::find($id);
        unset($updated['password']);
        $this->json(['ok' => true, 'user' => $updated]);
    }

    public function forum(): void {
        $this->requireAdmin();
        $categories = ForumCategory::all();
        $this->renderAdmin('forum', ['categories' => $categories, 'success' => flash('success'), 'error' => flash('error')]);
    }

    public function createCategory(): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('admin/forum'); }

        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $icon  = trim($_POST['icon'] ?? '💬');
        $color = trim($_POST['color'] ?? '#10b981');

        if (!$name) { flash('error', t('flash.name_required')); redirect('admin/forum'); }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        ForumCategory::create([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $desc,
            'icon'        => $icon,
            'color'       => $color,
            'sort_order'  => 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        flash('success', t('flash.category_created'));
        redirect('admin/forum');
    }

    public function deleteCategory(int $id): void {
        $this->requireAdmin();
        ForumCategory::delete($id);
        flash('success', t('flash.category_deleted'));
        redirect('admin/forum');
    }

    public function settings(): void {
        $this->requireAdmin();
        $settings = Setting::allMap();
        $this->renderAdmin('settings', ['settings' => $settings, 'success' => flash('success')]);
    }

    public function saveSettings(): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('admin/settings'); }

        $allowed = [
            'site_name', 'site_description', 'site_logo', 'site_favicon',
            'server_ip', 'server_port', 'discord_url', 'store_url',
            'maintenance_mode', 'registration_open', 'theme', 'language',
            'home_hero_title', 'home_hero_subtitle',
            'home_show_server', 'home_show_threads', 'home_show_activity', 'home_show_join_cta',
            'social_youtube', 'social_twitter', 'social_tiktok', 'social_instagram',
        ];
        $booleans = ['maintenance_mode', 'registration_open', 'home_show_server', 'home_show_threads', 'home_show_activity', 'home_show_join_cta'];
        foreach ($allowed as $key) {
            if (in_array($key, $booleans)) {
                Setting::set($key, isset($_POST[$key]) ? '1' : '0');
            } elseif (isset($_POST[$key])) {
                Setting::set($key, trim($_POST[$key]));
            }
        }

        flash('success', t('flash.settings_saved'));
        redirect('admin/settings');
    }

    public function updates(): void {
        $this->requireAdmin();
        $updater  = new \Core\Updater();
        $canWrite = $updater->canWriteFiles();
        $this->renderAdmin('updates', compact('canWrite'));
    }

    public function checkUpdate(): void {
        $this->requireAdmin();
        $updater = new \Core\Updater();
        $release = $updater->getLatestRelease(\Core\Updater::REPO);
        if (!$release) {
            $this->json(['ok' => false, 'message' => t('admin.updates.fetch_error')]);
        }
        $this->json([
            'ok'         => true,
            'current'    => APP_VERSION,
            'latest'     => $release['version'],
            'has_update' => $updater->hasUpdate($release['version']),
            'name'       => $release['name'],
            'body'       => $release['body'],
            'html_url'   => $release['html_url'],
            'zip_url'    => $release['zip_url'],
            'published'  => $release['published_at'],
        ]);
    }

    public function applyUpdate(): void {
        $this->requireAdmin();
        if (!csrf_verify()) { $this->json(['ok' => false, 'message' => t('flash.invalid_request')], 403); }

        $zipUrl = trim($_POST['zip_url'] ?? '');
        if (!$zipUrl || !str_starts_with($zipUrl, 'https://')) {
            $this->json(['ok' => false, 'message' => t('admin.updates.invalid_url')]);
        }

        $updater = new \Core\Updater();
        $result  = $updater->apply($zipUrl);
        $this->json($result);
    }

    public function plugins(): void {
        $this->requireAdmin();
        $plugins = app()->plugins->getAll();
        $this->renderAdmin('plugins', compact('plugins'));
    }

    public function pages(): void {
        $this->requireAdmin();
        $pages = Page::all('sort_order ASC, id ASC');
        $this->renderAdmin('pages', ['pages' => $pages, 'editing' => null, 'success' => flash('success'), 'error' => flash('error')]);
    }

    public function createPage(): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('admin/pages'); }

        $title   = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $nav     = isset($_POST['show_in_nav']) ? 1 : 0;
        $order   = (int)($_POST['sort_order'] ?? 0);

        if (!$title) { flash('error', t('flash.name_required')); redirect('admin/pages'); }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        if (Page::findBySlug($slug)) $slug .= '-' . time();

        Page::create([
            'title'       => $title,
            'slug'        => $slug,
            'content'     => $content,
            'show_in_nav' => $nav,
            'sort_order'  => $order,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        flash('success', t('flash.page_created'));
        redirect('admin/pages');
    }

    public function editPage(int $id): void {
        $this->requireAdmin();
        $page = Page::find($id);
        if (!$page) { flash('error', t('flash.not_found')); redirect('admin/pages'); }
        $pages = Page::all('sort_order ASC, id ASC');
        $this->renderAdmin('pages', ['pages' => $pages, 'editing' => $page, 'success' => flash('success'), 'error' => flash('error')]);
    }

    public function savePage(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('admin/pages'); }

        $title   = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $nav     = isset($_POST['show_in_nav']) ? 1 : 0;
        $order   = (int)($_POST['sort_order'] ?? 0);

        if (!$title) { flash('error', t('flash.name_required')); redirect('admin/pages/' . $id . '/edit'); }

        Page::update([
            'title'       => $title,
            'content'     => $content,
            'show_in_nav' => $nav,
            'sort_order'  => $order,
            'updated_at'  => date('Y-m-d H:i:s'),
        ], $id);

        flash('success', t('flash.page_saved'));
        redirect('admin/pages');
    }

    public function deletePage(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('admin/pages'); }
        Page::delete($id);
        flash('success', t('flash.page_deleted'));
        redirect('admin/pages');
    }

    public function togglePlugin(string $name): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('admin/plugins'); }

        $enabled = app()->plugins->getEnabled();
        if (in_array($name, $enabled)) {
            $enabled = array_values(array_diff($enabled, [$name]));
            $msg = t('flash.plugin_disabled', ['name' => $name]);
        } else {
            $enabled[] = $name;
            $msg = t('flash.plugin_enabled', ['name' => $name]);
        }
        Setting::set('enabled_plugins', json_encode($enabled));
        flash('success', $msg);
        redirect('admin/plugins');
    }
}
