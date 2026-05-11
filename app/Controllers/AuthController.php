<?php
declare(strict_types=1);

namespace App\Controllers;

class AuthController extends Controller {
    public function showLogin(): void {
        if (auth()->check()) redirect('');
        $this->render('auth/login', ['error' => flash('error'), 'success' => flash('success')]);
    }

    public function login(): void {
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('login'); }
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', 'Udfyld alle felter.');
            redirect('login');
        }

        if (auth()->attempt($email, $password)) {
            $next = $_POST['next'] ?? '';
            redirect($next ?: '');
        }

        flash('error', 'Forkert email eller adgangskode.');
        redirect('login');
    }

    public function showRegister(): void {
        if (auth()->check()) redirect('');
        $this->render('auth/register', ['error' => flash('error')]);
    }

    public function register(): void {
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('register'); }

        $username  = trim($_POST['username'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (!$username || !$email || !$password) {
            flash('error', 'Udfyld alle felter.'); redirect('register');
        }
        if (strlen($username) < 3 || strlen($username) > 20) {
            flash('error', 'Brugernavn skal være 3–20 tegn.'); redirect('register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Ugyldig email.'); redirect('register');
        }
        if (strlen($password) < 8) {
            flash('error', 'Adgangskode skal mindst være 8 tegn.'); redirect('register');
        }
        if ($password !== $password2) {
            flash('error', 'Adgangskoderne matcher ikke.'); redirect('register');
        }

        $id = auth()->register($username, $email, $password);
        if (!$id) {
            flash('error', 'Email eller brugernavn er allerede i brug.'); redirect('register');
        }

        $user = \App\Models\User::find($id);
        auth()->login($user);
        flash('success', 'Velkommen, ' . e($username) . '! Din konto er oprettet.');
        redirect('');
    }

    public function logout(): void {
        auth()->logout();
        redirect('');
    }

    public function verify(string $token): void {
        // Email verification — placeholder
        flash('success', 'Konto verificeret!');
        redirect('login');
    }
}
