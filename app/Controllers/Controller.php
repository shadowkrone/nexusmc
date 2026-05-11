<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Application;
use Core\View;

abstract class Controller {
    protected Application $app;
    protected View $view;

    public function __construct(Application $app) {
        $this->app  = $app;
        $this->view = new View();
    }

    protected function render(string $view, array $data = []): void {
        $this->view->render($view, $data);
    }

    protected function renderAdmin(string $view, array $data = []): void {
        $this->view->renderAdmin($view, $data);
    }

    protected function requireAuth(): void {
        if (!auth()->check()) {
            flash('error', 'Du skal være logget ind for at gøre dette.');
            redirect('login');
        }
    }

    protected function requireAdmin(): void {
        $this->requireAuth();
        if (!auth()->isAdmin()) {
            http_response_code(403);
            die('<h1 style="font-family:sans-serif">403 – Ingen adgang</h1>');
        }
    }

    protected function json(mixed $data, int $status = 200): never {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function back(): never {
        $ref = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $ref);
        exit;
    }
}
