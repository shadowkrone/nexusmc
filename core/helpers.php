<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $map = [
        'Core\\'             => ROOT . '/core/',
        'App\\Controllers\\' => ROOT . '/app/Controllers/',
        'App\\Models\\'      => ROOT . '/app/Models/',
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

function app(): Core\Application  { return Core\Application::$instance; }
function db(): Core\Database       { return app()->db; }
function auth(): Core\Auth         { return app()->auth; }
function session(): Core\Session   { return app()->session; }

function setting(string $key, mixed $default = null): mixed {
    static $cache = null;
    if ($cache === null) {
        try { $cache = \App\Models\Setting::allMap(); }
        catch (\Throwable) { $cache = []; }
    }
    return $cache[$key] ?? $default;
}

function e(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function url(string $path = ''): string {
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}

function csrf(): string {
    if (!session()->get('csrf_token')) {
        session()->set('csrf_token', bin2hex(random_bytes(32)));
    }
    return session()->get('csrf_token');
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . csrf() . '">';
}

function csrf_verify(): bool {
    $token = $_POST['_csrf'] ?? '';
    return hash_equals(csrf(), $token);
}

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)      return 'lige nu';
    if ($diff < 3600)    return round($diff / 60) . ' min. siden';
    if ($diff < 86400)   return round($diff / 3600) . ' t. siden';
    if ($diff < 604800)  return round($diff / 86400) . ' dage siden';
    return date('d/m/Y', strtotime($datetime));
}

function mcHead(string $name, int $size = 32): string {
    return "https://mc-heads.net/avatar/{$name}/{$size}";
}

function serverStatus(string $host, int $port = 25565): array {
    $ctx = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
    $json = @file_get_contents("https://api.mcsrvstat.us/3/{$host}:{$port}", false, $ctx);
    if (!$json) return ['online' => false];
    $d = json_decode($json, true);
    if (!$d || empty($d['online'])) return ['online' => false];
    return [
        'online'          => true,
        'players_online'  => $d['players']['online'] ?? 0,
        'players_max'     => $d['players']['max'] ?? 20,
        'version'         => $d['version'] ?? '?',
        'motd'            => $d['motd']['clean'][0] ?? '',
        'icon'            => $d['icon'] ?? null,
    ];
}

function flash(string $key, mixed $value = null): mixed {
    if ($value !== null) {
        session()->set("flash_{$key}", $value);
        return null;
    }
    $v = session()->get("flash_{$key}");
    session()->remove("flash_{$key}");
    return $v;
}
