<?php
declare(strict_types=1);

namespace Plugin\Applications\Controllers;

use Core\Application;

abstract class PluginController {
    protected Application $app;
    private string $theme;
    private string $pluginDir;

    public function __construct(Application $app) {
        $this->app       = $app;
        $this->theme     = defined('THEME') ? THEME : 'default';
        $this->pluginDir = dirname(__DIR__);
    }

    protected function render(string $view, array $data = []): void {
        $viewFile = $this->pluginDir . "/views/{$view}.php";
        extract($data, EXTR_SKIP);
        $content = (function() use ($viewFile, $data) {
            extract($data, EXTR_SKIP);
            ob_start();
            require $viewFile;
            return ob_get_clean();
        })();
        require ROOT . "/themes/{$this->theme}/layout.php";
    }

    protected function renderAdmin(string $view, array $data = []): void {
        $viewFile = $this->pluginDir . "/views/admin/{$view}.php";
        extract($data, EXTR_SKIP);
        $content = (function() use ($viewFile, $data) {
            extract($data, EXTR_SKIP);
            ob_start();
            require $viewFile;
            return ob_get_clean();
        })();
        require ROOT . "/themes/{$this->theme}/admin/layout.php";
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
}
