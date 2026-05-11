<?php
declare(strict_types=1);

namespace Core;

class View {
    private string $theme;

    public function __construct() {
        $this->theme = defined('THEME') ? THEME : 'default';
    }

    public function render(string $view, array $data = [], bool $layout = true): void {
        extract($data, EXTR_SKIP);
        $viewFile = ROOT . "/themes/{$this->theme}/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        if ($layout) {
            $content = $this->capture($viewFile, $data);
            require ROOT . "/themes/{$this->theme}/layout.php";
        } else {
            require $viewFile;
        }
    }

    public function renderAdmin(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        $viewFile = ROOT . "/themes/{$this->theme}/admin/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Admin view not found: {$view}");
        }

        $content = $this->capture($viewFile, $data);
        require ROOT . "/themes/{$this->theme}/admin/layout.php";
    }

    private function capture(string $file, array $data): string {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
