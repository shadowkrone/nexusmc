<?php
declare(strict_types=1);

namespace Core;

class Router {
    private array $routes = [];
    private Application $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function get(string $path, string $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $uri    = '/' . ltrim(substr($uri, strlen($base)), '/');

        foreach (($this->routes[$method] ?? []) as $pattern => $handler) {
            $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($handler, $params);
                return;
            }
        }

        http_response_code(404);
        $view = ROOT . '/themes/' . THEME . '/404.php';
        if (file_exists($view)) {
            require $view;
        } else {
            echo '<h1 style="font-family:sans-serif;color:#e11d48">404 – Page not found</h1>';
        }
    }

    private function call(string $handler, array $params): void {
        [$class, $method] = explode('@', $handler);
        $fqcn = str_contains($class, '\\') ? $class : "App\\Controllers\\{$class}";
        $controller = new $fqcn($this->app);
        $params = array_map(
            fn($v) => ctype_digit($v) ? (int)$v : $v,
            $params
        );
        $controller->$method(...array_values($params));
    }
}
