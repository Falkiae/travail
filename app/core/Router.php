<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = require CONFIG_PATH . '/routes.php';
    }

    public function dispatch(string $method, string $uri): void
    {
        Auth::start();

        // Nettoyer l'URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // Vérifier le mode maintenance (hors /admin)
        if (!str_starts_with($uri, '/' . ADMIN_PATH)) {
            $this->checkMaintenanceMode();
        }

        // Vérifier les redirections 301
        $this->checkRedirections($uri);

        foreach ($this->routes as $route => $handler) {
            [$routeMethod, $routePath] = explode(' ', $route, 2);

            if ($routeMethod !== $method) {
                continue;
            }

            $params = $this->matchRoute($routePath, $uri);
            if ($params !== null) {
                [$controllerName, $action] = $handler;
                $controllerClass = 'App\\Controllers\\' . $controllerName;
                $controller = new $controllerClass();
                $controller->$action(...array_values($params));
                return;
            }
        }

        // Aucune route trouvée → 404
        $this->handleNotFound($uri);
    }

    private function matchRoute(string $routePath, string $uri): ?array
    {
        $pattern = preg_replace('/\{([a-z_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return null;
        }

        // Extraire les noms de paramètres
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
                $msg = $stmt2->fetch()['value'] ?? 'Site en maintenance.';
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Maintenance</title></head><body style="font-family:sans-serif;text-align:center;padding:4rem"><h1>🛠</h1><p>' . htmlspecialchars($msg) . '</p></body></html>';
                exit;
            }
        } catch (\Exception $e) {
            // Si la DB n'est pas disponible, on laisse passer
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
        } catch (\Exception $e) {
            // Silencieux si la DB n'est pas encore configurée
        }
    }

    private function handleNotFound(string $uri): void
    {
        try {
            $pdo  = Database::getInstance();
            $ip   = $_SERVER['REMOTE_ADDR'] ?? '';
            $ref  = $_SERVER['HTTP_REFERER'] ?? null;
            $stmt = $pdo->prepare(
                'INSERT INTO kn_error_logs (url, referer, count, last_seen) VALUES (?, ?, 1, NOW())
                 ON DUPLICATE KEY UPDATE count = count + 1, last_seen = NOW()'
            );
            $stmt->execute([$uri, $ref]);
        } catch (\Exception $e) {}

        $controller = new \App\Controllers\FrontController();
        $controller->notFound();
    }
}
