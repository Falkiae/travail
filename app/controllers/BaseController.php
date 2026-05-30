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

    protected function slugify(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        if (function_exists('transliterator_transliterate')) {
            $str = transliterator_transliterate('Any-Latin; Latin-ASCII', $str);
        }
        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
        $str = trim(preg_replace('/[\s-]+/', '-', $str), '-');
        return $str;
    }

    protected function saveRevision(string $entityType, int $entityId, ?string $content): void
    {
        $pdo = $this->db();
        $pdo->prepare(
            "DELETE FROM kn_revisions WHERE entity_type = ? AND entity_id = ? AND id NOT IN (
                SELECT id FROM (SELECT id FROM kn_revisions WHERE entity_type = ? AND entity_id = ? ORDER BY created_at DESC LIMIT 4) t
            )"
        )->execute([$entityType, $entityId, $entityType, $entityId]);
        $pdo->prepare("INSERT INTO kn_revisions (entity_type, entity_id, content) VALUES (?, ?, ?)")
            ->execute([$entityType, $entityId, $content]);
    }

    protected function ensureUniqueSlug(string $table, string $slug, string $lang, ?int $excludeId = null): string
    {
        $pdo = $this->db();
        $base = $slug;
        $i = 1;
        while (true) {
            $sql = "SELECT id FROM `$table` WHERE slug = ? AND lang = ?";
            $params = [$slug, $lang];
            if ($excludeId !== null) {
                $sql .= ' AND id != ?';
                $params[] = $excludeId;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            if (!$stmt->fetch()) break;
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
