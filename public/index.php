<?php
declare(strict_types=1);

// Fonctionne aussi bien en local (public/ est un sous-dossier) que sur hébergement mutualisé
// (index.php déplacé à la racine, à côté de app/, config/, etc.)
define('ROOT_PATH', is_dir(__DIR__ . '/app') ? __DIR__ : dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once CONFIG_PATH . '/config.php';

spl_autoload_register(function (string $class): void {
    $map = [
        'App\\Core\\' => APP_PATH . '/core/',
        'App\\Controllers\\' => APP_PATH . '/controllers/',
        'App\\Models\\' => APP_PATH . '/models/',
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
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
