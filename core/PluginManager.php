<?php
declare(strict_types=1);

namespace Core;

class PluginManager {
    private Application $app;
    private array $hooks   = [];
    private array $loaded  = [];

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function loadAll(): void {
        $pluginDir = ROOT . '/plugins';
        if (!is_dir($pluginDir)) return;

        $enabled = $this->getEnabled();
        foreach ($enabled as $name) {
            $file = "{$pluginDir}/{$name}/plugin.php";
            if (file_exists($file)) {
                require_once $file;
                $this->loaded[] = $name;
            }
        }
    }

    public function getEnabled(): array {
        try {
            $row = \App\Models\Setting::get('enabled_plugins');
            return $row ? json_decode($row, true) : [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function getAll(): array {
        $plugins = [];
        $dir = ROOT . '/plugins';
        if (!is_dir($dir)) return $plugins;

        foreach (glob("{$dir}/*/plugin.json") as $file) {
            $info = json_decode(file_get_contents($file), true) ?? [];
            $name = basename(dirname($file));
            $plugins[$name] = array_merge($info, [
                'enabled' => in_array($name, $this->loaded),
            ]);
        }
        return $plugins;
    }

    public function on(string $event, callable $callback): void {
        $this->hooks[$event][] = $callback;
    }

    public function fire(string $event, mixed $payload = null): mixed {
        foreach ($this->hooks[$event] ?? [] as $cb) {
            $result = $cb($payload);
            if ($result !== null) $payload = $result;
        }
        return $payload;
    }
}
