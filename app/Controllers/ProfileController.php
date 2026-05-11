<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\ForumPost;
use App\Models\ForumThread;

class ProfileController extends Controller {
    public function show(string $username): void {
        $user = User::findByUsername($username);
        if (!$user) { http_response_code(404); return; }

        $postCount   = ForumPost::count('user_id = ?', [$user['id']]);
        $threadCount = ForumThread::count('user_id = ?', [$user['id']]);
        $recentPosts = ForumPost::recent(5); // simplified

        $this->render('profile', compact('user', 'postCount', 'threadCount', 'recentPosts'));
    }

    public function settings(): void {
        $this->requireAuth();
        $this->render('settings', ['user' => auth()->user(), 'success' => flash('success'), 'error' => flash('error')]);
    }

    public function saveSettings(): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('settings'); }

        $user = auth()->user();
        $data = [];

        $email = trim($_POST['email'] ?? '');
        if ($email && $email !== $user['email']) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Ugyldig email.'); redirect('settings');
            }
            $existing = User::findByEmail($email);
            if ($existing && $existing['id'] !== $user['id']) {
                flash('error', 'Email er allerede i brug.'); redirect('settings');
            }
            $data['email'] = strtolower($email);
        }

        $password = $_POST['password'] ?? '';
        if ($password) {
            if (strlen($password) < 8) {
                flash('error', 'Adgangskode skal mindst være 8 tegn.'); redirect('settings');
            }
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if (!empty($data)) {
            User::update($data, $user['id']);
        }

        flash('success', 'Indstillinger gemt!');
        redirect('settings');
    }
}
