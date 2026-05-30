<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Database;
use App\Core\Auth;

abstract class BaseController
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function db(): \PDO
    {
        return Database::getInstance();
    }

    protected function requireLogin(): void
    {
        Auth::requireLogin();
    }
}
