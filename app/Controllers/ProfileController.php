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

        $recentThreads = db()->fetchAll(
            'SELECT t.id, t.title, t.created_at, c.name AS category_name, c.slug AS category_slug
             FROM ' . DB_PREFIX . 'forum_threads t
             JOIN ' . DB_PREFIX . 'forum_categories c ON t.category_id = c.id
             WHERE t.user_id = ?
             ORDER BY t.created_at DESC LIMIT 5',
            [$user['id']]
        );

        $this->render('profile', compact('user', 'postCount', 'threadCount', 'recentThreads'));
    }

    public function settings(): void {
        $this->requireAuth();
        $this->render('settings', [
            'user'    => auth()->user(),
            'success' => flash('success'),
            'error'   => flash('error'),
            'tab'     => $_GET['tab'] ?? 'profile',
        ]);
    }

    public function saveUsername(): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('settings?tab=profile'); }

        $user     = auth()->user();
        $username = trim($_POST['username'] ?? '');

        if (!$username) {
            flash('error', t('flash.username_empty')); redirect('settings?tab=profile');
        }
        if (strlen($username) < 3 || strlen($username) > 30) {
            flash('error', t('flash.username_length')); redirect('settings?tab=profile');
        }
        if (preg_match('/[^a-zA-Z0-9_\-]/', $username)) {
            flash('error', t('flash.username_chars')); redirect('settings?tab=profile');
        }
        if (strtolower($username) !== strtolower($user['username'])) {
            $taken = User::findByUsername($username);
            if ($taken) { flash('error', t('flash.username_taken')); redirect('settings?tab=profile'); }
        }

        User::update(['username' => $username], $user['id']);
        flash('success', t('flash.username_updated', ['name' => $username]));
        redirect('settings?tab=profile');
    }

    public function saveEmail(): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('settings?tab=security'); }

        $user     = auth()->user();
        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['current_password'] ?? '';

        if (!$email) {
            flash('error', t('flash.email_empty')); redirect('settings?tab=security');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', t('flash.invalid_email')); redirect('settings?tab=security');
        }
        if (!password_verify($password, $user['password'])) {
            flash('error', t('flash.wrong_password_confirm')); redirect('settings?tab=security');
        }
        if ($email !== $user['email']) {
            $taken = User::findByEmail($email);
            if ($taken) { flash('error', t('flash.email_taken')); redirect('settings?tab=security'); }
        }

        User::update(['email' => $email], $user['id']);
        flash('success', t('flash.email_updated'));
        redirect('settings?tab=security');
    }

    public function savePassword(): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('settings?tab=security'); }

        $user        = auth()->user();
        $current     = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) {
            flash('error', t('flash.current_wrong')); redirect('settings?tab=security');
        }
        if (strlen($newPassword) < 8) {
            flash('error', t('flash.new_password_min')); redirect('settings?tab=security');
        }
        if ($newPassword !== $confirm) {
            flash('error', t('flash.passwords_match')); redirect('settings?tab=security');
        }

        User::update(['password' => password_hash($newPassword, PASSWORD_BCRYPT)], $user['id']);
        flash('success', t('flash.password_changed'));
        redirect('settings?tab=security');
    }

    public function deleteAccount(): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('settings?tab=danger'); }

        $user     = auth()->user();
        $password = $_POST['confirm_password'] ?? '';
        $confirm  = trim($_POST['confirm_text'] ?? '');

        if (strtolower($confirm) !== t('settings.danger_confirm_value')) {
            flash('error', t('flash.type_confirm_text')); redirect('settings?tab=danger');
        }
        if (!password_verify($password, $user['password'])) {
            flash('error', t('flash.wrong_password')); redirect('settings?tab=danger');
        }

        db()->delete(DB_PREFIX . 'forum_posts',   'user_id = ?', [$user['id']]);
        db()->delete(DB_PREFIX . 'forum_threads', 'user_id = ?', [$user['id']]);
        User::delete($user['id']);

        auth()->logout();
        flash('success', t('flash.account_deleted'));
        redirect('');
    }
}
