<?php
declare(strict_types=1);

namespace Core;

class Updater {
    // Paths that are NEVER overwritten during update
    private const PROTECTED = [
        'config/config.php',
        'plugins/',
        'storage/',
    ];

    public function getLatestRelease(string $repo): ?array {
        $url = "https://api.github.com/repos/{$repo}/releases/latest";
        $ctx = stream_context_create(['http' => [
            'timeout'        => 10,
            'user_agent'     => 'NexusMC/' . APP_VERSION . ' (update-checker)',
            'ignore_errors'  => true,
        ]]);

        $json = $this->fetch($url, $ctx);
        if (!$json) return null;

        $data = json_decode($json, true);
        if (empty($data['tag_name'])) return null;

        return [
            'version'      => ltrim($data['tag_name'], 'v'),
            'tag'          => $data['tag_name'],
            'name'         => $data['name'] ?? $data['tag_name'],
            'body'         => $data['body'] ?? '',
            'published_at' => $data['published_at'] ?? '',
            'zip_url'      => $data['zipball_url'],
            'html_url'     => $data['html_url'] ?? '',
        ];
    }

    public function hasUpdate(string $latest): bool {
        return version_compare($latest, APP_VERSION, '>');
    }

    public function canWriteFiles(): bool {
        $paths = ['index.php', 'core/', 'app/', 'themes/'];
        foreach ($paths as $p) {
            if (!is_writable(ROOT . '/' . $p)) return false;
        }
        return true;
    }

    public function apply(string $zipUrl): array {
        if (!extension_loaded('zip')) {
            return ['ok' => false, 'message' => 'PHP ZipArchive extension mangler på serveren.'];
        }
        if (!$this->canWriteFiles()) {
            return ['ok' => false, 'message' => 'Serveren har ikke skrivetilladelse til filerne. Tjek fil-permissions.'];
        }

        // Download ZIP
        $zipFile = sys_get_temp_dir() . '/nexusmc_update_' . time() . '.zip';
        $content = $this->fetch($zipUrl);
        if (!$content) {
            return ['ok' => false, 'message' => 'Kunne ikke downloade update-filen fra GitHub.'];
        }
        file_put_contents($zipFile, $content);

        // Extract to temp dir
        $tmpDir = sys_get_temp_dir() . '/nexusmc_upd_' . time() . '/';
        mkdir($tmpDir, 0755, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipFile) !== true) {
            @unlink($zipFile);
            return ['ok' => false, 'message' => 'Kunne ikke åbne den downloadede ZIP-fil.'];
        }
        $zip->extractTo($tmpDir);
        $zip->close();
        @unlink($zipFile);

        // GitHub zips have one top-level subdirectory (e.g. "owner-repo-abc1234/")
        $subdirs = glob($tmpDir . '*/');
        $sourceDir = !empty($subdirs) ? $subdirs[0] : $tmpDir;

        // Backup config before overwriting anything
        $this->backup();

        // Copy files, skipping protected paths
        $errors = $this->copyDir($sourceDir, ROOT);

        // Cleanup temp
        $this->removeDir($tmpDir);

        if (!empty($errors)) {
            return ['ok' => false, 'message' => 'Delvist opdateret, men ' . count($errors) . ' filer kunne ikke skrives: ' . implode(', ', array_slice($errors, 0, 3))];
        }

        return ['ok' => true, 'message' => 'Opdatering gennemført! NexusMC er nu opdateret.'];
    }

    private function backup(): void {
        $backupDir = ROOT . '/storage/backups/' . date('Y-m-d_H-i-s') . '/';
        @mkdir($backupDir, 0755, true);

        // Backup config
        if (file_exists(ROOT . '/config/config.php')) {
            copy(ROOT . '/config/config.php', $backupDir . 'config.php');
        }
    }

    private function copyDir(string $src, string $dst): array {
        $errors = [];
        $src    = rtrim($src, '/\\') . '/';
        $dst    = rtrim($dst, '/\\') . '/';

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iter as $item) {
            $relative = str_replace($src, '', str_replace('\\', '/', $item->getPathname()));

            // Skip protected paths
            foreach (self::PROTECTED as $p) {
                if (str_starts_with($relative, $p)) continue 2;
            }

            $target = $dst . $relative;

            if ($item->isDir()) {
                @mkdir($target, 0755, true);
            } else {
                if (!@copy($item->getPathname(), $target)) {
                    $errors[] = $relative;
                }
            }
        }

        return $errors;
    }

    private function removeDir(string $dir): void {
        if (!is_dir($dir)) return;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
        }
        @rmdir($dir);
    }

    private function fetch(string $url, $ctx = null): string|false {
        // Try cURL first (more reliable, handles redirects)
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_USERAGENT      => 'NexusMC/' . APP_VERSION,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $body = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($body && $code < 400) ? $body : false;
        }

        // Fallback to file_get_contents
        if (ini_get('allow_url_fopen')) {
            return @file_get_contents($url, false, $ctx);
        }

        return false;
    }
}
