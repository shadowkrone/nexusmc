<?php
declare(strict_types=1);

define('ROOT', __DIR__);
define('APP_VERSION', '1.0.0');
define('APP_NAME', 'NexusMC');

if (!file_exists(ROOT . '/config/config.php')) {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    header('Location: ' . $base . '/install/');
    exit;
}

require_once ROOT . '/config/config.php';
require_once ROOT . '/core/helpers.php';

$app = new Core\Application();
$app->run();
