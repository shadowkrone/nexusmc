<?php
declare(strict_types=1);

namespace App\Controllers;

class AuthController extends Controller {
    public function showLogin(): void {
        if (auth()->check()) redirect('');
        $this->render('auth/login', ['error' => flash('error'), 'success' => flash('success')]);
    }

    public function login(): void {
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('login'); }
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', t('flash.fill_all_fields'));
            redirect('login');
        }

        if (auth()->attempt($email, $password)) {
            $next = $_POST['next'] ?? '';
            redirect($next ?: '');
        }

        flash('error', t('flash.wrong_credentials'));
        redirect('login');
    }

    public function showRegister(): void {
        if (auth()->check()) redirect('');
        $this->render('auth/register', ['error' => flash('error')]);
    }

    public function register(): void {
        if (!csrf_verify()) { flash('error', t('flash.invalid_request')); redirect('register'); }

        $username  = trim($_POST['username'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (!$username || !$email || !$password) {
            flash('error', t('flash.fill_all_fields')); redirect('register');
        }
        if (strlen($username) < 3 || strlen($username) > 20) {
            flash('error', t('flash.username_length_20')); redirect('register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', t('flash.invalid_email')); redirect('register');
        }
        if (strlen($password) < 8) {
            flash('error', t('flash.password_min_8')); redirect('register');
        }
        if ($password !== $password2) {
            flash('error', t('flash.passwords_no_match')); redirect('register');
        }

        $id = auth()->register($username, $email, $password);
        if (!$id) {
            flash('error', t('flash.email_or_username_taken')); redirect('register');
        }

        $user = \App\Models\User::find($id);
        auth()->login($user);
        flash('success', t('flash.welcome', ['name' => e($username)]));
        redirect('');
    }

    public function logout(): void {
        auth()->logout();
        redirect('');
    }

    public function verify(string $token): void {
        flash('success', t('flash.account_verified'));
        redirect('login');
    }
}
