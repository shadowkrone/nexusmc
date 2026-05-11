<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\Setting;

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
        if (!csrf_verify()) { $this->json(['error' => 'Invalid'], 403); }
        $role = $_POST['role'] ?? 'member';
        if (!in_array($role, ['member', 'moderator', 'admin'])) {
            $this->json(['error' => 'Ugyldig rolle'], 400);
        }
        if ($id === auth()->user()['id']) {
            $this->json(['error' => 'Du kan ikke ændre din egen rolle'], 400);
        }
        User::update(['role' => $role], $id);
        $this->json(['ok' => true, 'role' => $role]);
    }

    public function getUser(int $id): void {
        $this->requireAdmin();
        $user = User::find($id);
        if (!$user) { $this->json(['error' => 'Bruger ikke fundet'], 404); }
        // Never expose password hash
        unset($user['password']);
        $this->json(['ok' => true, 'user' => $user]);
    }

    public function updateUser(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { $this->json(['error' => 'Ugyldig forespørgsel.'], 403); }

        $user = User::find($id);
        if (!$user) { $this->json(['error' => 'Bruger ikke fundet.'], 404); }

        $data     = [];
        $errors   = [];

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate username
        if ($username && $username !== $user['username']) {
            if (strlen($username) < 3 || strlen($username) > 30) {
                $errors[] = 'Brugernavn skal være 3–30 tegn.';
            } elseif (preg_match('/[^a-zA-Z0-9_\-]/', $username)) {
                $errors[] = 'Brugernavn må kun indeholde bogstaver, tal, _ og -.';
            } else {
                $taken = db()->fetch('SELECT id FROM ' . DB_PREFIX . 'users WHERE username = ? AND id != ?', [$username, $id]);
                if ($taken) $errors[] = 'Brugernavnet er allerede i brug.';
                else $data['username'] = $username;
            }
        }

        // Validate email
        if ($email && $email !== $user['email']) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Ugyldig email-adresse.';
            } else {
                $taken = db()->fetch('SELECT id FROM ' . DB_PREFIX . 'users WHERE email = ? AND id != ?', [strtolower($email), $id]);
                if ($taken) $errors[] = 'Email er allerede i brug.';
                else $data['email'] = strtolower($email);
            }
        }

        // Password (optional)
        if ($password !== '') {
            if (strlen($password) < 8) {
                $errors[] = 'Adgangskode skal mindst være 8 tegn.';
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
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('admin/forum'); }

        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $icon  = trim($_POST['icon'] ?? '💬');
        $color = trim($_POST['color'] ?? '#10b981');

        if (!$name) { flash('error', 'Navn er påkrævet.'); redirect('admin/forum'); }

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

        flash('success', 'Kategori oprettet!');
        redirect('admin/forum');
    }

    public function deleteCategory(int $id): void {
        $this->requireAdmin();
        ForumCategory::delete($id);
        flash('success', 'Kategori slettet.');
        redirect('admin/forum');
    }

    public function settings(): void {
        $this->requireAdmin();
        $settings = Setting::allMap();
        $this->renderAdmin('settings', ['settings' => $settings, 'success' => flash('success')]);
    }

    public function saveSettings(): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('admin/settings'); }

        $allowed = [
            'site_name', 'site_description', 'site_logo', 'site_banner',
            'server_ip', 'server_port', 'discord_url', 'store_url',
            'maintenance_mode', 'registration_open', 'theme',
        ];
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                Setting::set($key, trim($_POST[$key]));
            }
        }

        flash('success', 'Indstillinger gemt!');
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
            $this->json(['ok' => false, 'message' => 'Kunne ikke hente release-info fra GitHub. Tjek at repo-navnet er korrekt.']);
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
        if (!csrf_verify()) { $this->json(['ok' => false, 'message' => 'Ugyldig forespørgsel.'], 403); }

        $zipUrl = trim($_POST['zip_url'] ?? '');
        if (!$zipUrl || !str_starts_with($zipUrl, 'https://')) {
            $this->json(['ok' => false, 'message' => 'Ugyldig download-URL.']);
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

    public function togglePlugin(string $name): void {
        $this->requireAdmin();
        $enabled = app()->plugins->getEnabled();
        if (in_array($name, $enabled)) {
            $enabled = array_values(array_diff($enabled, [$name]));
        } else {
            $enabled[] = $name;
        }
        Setting::set('enabled_plugins', json_encode($enabled));
        $this->json(['ok' => true, 'enabled' => in_array($name, $enabled)]);
    }
}
