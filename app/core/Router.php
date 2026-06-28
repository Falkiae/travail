<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private $routes = [];

    public function __construct()
    {
        $this->routes = require CONFIG_PATH . '/routes.php';
    }

    public function dispatch(string $method, string $uri): void
    {
        Auth::start();

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-XSS-Protection: 1; mode=block');

        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        $adminPrefix = '/' . ADMIN_PATH;

        if (strncmp($uri, $adminPrefix, strlen($adminPrefix)) === 0) {
            header('X-Robots-Tag: noindex, nofollow');
        }

        if (strncmp($uri, $adminPrefix, strlen($adminPrefix)) !== 0) {
            $this->checkMaintenanceMode();
            $this->checkRedirections($uri);
        }

        foreach ($this->routes as $route => $handler) {
            $parts       = explode(' ', $route, 2);
            $routeMethod = $parts[0];
            $routePath   = $parts[1];

            if ($routeMethod !== $method) {
                continue;
            }

            $params = $this->matchRoute($routePath, $uri);
            if ($params !== null) {
                $controllerName  = $handler[0];
                $action          = $handler[1];
                $controllerClass = 'App\\Controllers\\' . $controllerName;
                $controller      = new $controllerClass();
                call_user_func_array([$controller, $action], array_values($params));
                return;
            }
        }

        $this->handleNotFound($uri);
    }

    private function matchRoute(string $routePath, string $uri): ?array
    {
        $pattern = preg_replace('/\{([a-z_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return null;
        }

        preg_match_all('/\{([a-z_]+)\}/', $routePath, $paramNames);
        $params = [];
        foreach ($paramNames[1] as $i => $name) {
            $params[$name] = $matches[$i + 1];
        }
        return $params;
    }

    private function checkMaintenanceMode(): void
    {
        try {
            $pdo  = Database::getInstance();
            $stmt = $pdo->prepare("SELECT `value` FROM kn_settings WHERE `key` = 'maintenance_mode'");
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row && $row['value'] === '1') {
                http_response_code(503);
                $stmt2 = $pdo->prepare("SELECT `value` FROM kn_settings WHERE `key` = 'maintenance_message'");
                $stmt2->execute();
                $row2 = $stmt2->fetch();
                $msg  = $row2 ? $row2['value'] : 'Site en maintenance.';
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Maintenance</title></head>'
                   . '<body style="font-family:sans-serif;text-align:center;padding:4rem">'
                   . '<h1>Maintenance</h1><p>' . htmlspecialchars($msg) . '</p></body></html>';
                exit;
            }
        } catch (\Exception $e) {
            // DB pas encore dispo, on laisse passer
        }
    }

    private function checkRedirections(string $uri): void
    {
        try {
            $pdo  = Database::getInstance();
            $stmt = $pdo->prepare('SELECT to_url FROM kn_redirections WHERE from_url = ? LIMIT 1');
            $stmt->execute([$uri]);
            $row = $stmt->fetch();
            if ($row) {
                header('Location: ' . $row['to_url'], true, 301);
                exit;
            }
        } catch (\Exception $e) {}
    }

    private function handleNotFound(string $uri): void
    {
        try {
            $pdo  = Database::getInstance();
            $ref  = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
            $pdo->prepare(
                'INSERT INTO kn_error_logs (url, referer, count, last_seen) VALUES (?, ?, 1, NOW())
                 ON DUPLICATE KEY UPDATE count = count + 1, last_seen = NOW()'
            )->execute([$uri, $ref]);
        } catch (\Exception $e) {}

        $controller = new \App\Controllers\FrontController();
        $controller->notFound();
    }
}
