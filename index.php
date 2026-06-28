<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once CONFIG_PATH . '/config.php';

if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
}

spl_autoload_register(function (string $class): void {
    $appPath = ROOT_PATH . '/app';
    $map = [
        'App\\Core\\'        => $appPath . '/core/',
        'App\\Controllers\\' => $appPath . '/controllers/',
        'App\\Models\\'      => $appPath . '/models/',
    ];
    foreach ($map as $prefix => $dir) {
        if (strncmp($class, $prefix, strlen($prefix)) === 0) {
            $file = $dir . substr($class, strlen($prefix)) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

use App\Core\Router;

$router = new Router();
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
