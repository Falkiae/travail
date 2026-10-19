<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

/**
 * Blocs types enregistrés : un bloc de page configuré peut être sauvegardé
 * sous un nom, puis réinséré (en copie indépendante) sur une autre page.
 */
class BlockPresetController extends BaseController
{
    private const TYPES = [
        'hero', 'services', 'two-col', 'how', 'reviews', 'zone', 'accordion',
        'cta-final', 'heading', 'text', 'image', 'cta', 'html', 'video', 'file',
        'quote', 'pricing', 'before-after', 'logos', 'seo-content',
        'domicile-vs-atelier', 'sofa-simulator', 'prestation', 'blog-latest',
        'vehicle-pricing', 'contact',
    ];
    private const MAX_CONTENT_BYTES = 204800;

    // Le déploiement se fait par git sans exécuter les migrations : la table
    // est créée à la demande plutôt que de provoquer une erreur SQL.
    private function ensureTable(): \PDO
    {
        $pdo = $this->db();
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `kn_block_presets` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `type` varchar(40) NOT NULL,
              `content` mediumtext NOT NULL,
              `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
              `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        return $pdo;
    }

    private function requireCsrf(): void
    {
        $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
        if (!Auth::verifyCsrfToken($token)) {
            $this->jsonResponse(array('ok' => false, 'error' => 'Token CSRF invalide.'), 403);
        }
    }

    public function listJson(): void
    {
        $this->requireLogin();
        $pdo  = $this->ensureTable();
        $rows = $pdo->query("SELECT id, name, type, content FROM kn_block_presets ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $out  = array();
        foreach ($rows as $r) {
            $content = json_decode((string) $r['content'], true);
            if (!is_array($content)) continue;
            $out[] = array('id' => (int) $r['id'], 'name' => $r['name'], 'type' => $r['type'], 'content' => $content);
        }
        $this->jsonResponse(array('ok' => true, 'items' => $out));
    }

    public function save(): void
    {
        $this->requireLogin();
        $this->requireCsrf();

        $name = trim(isset($_POST['name']) ? (string) $_POST['name'] : '');
        if ($name === '' || mb_strlen($name) > 100) {
            $this->jsonResponse(array('ok' => false, 'error' => 'Le nom doit faire entre 1 et 100 caractères.'), 422);
        }
        $type = isset($_POST['type']) ? (string) $_POST['type'] : '';
        if (!in_array($type, self::TYPES, true)) {
            $this->jsonResponse(array('ok' => false, 'error' => 'Type de bloc inconnu.'), 422);
        }
        $raw = isset($_POST['content']) ? (string) $_POST['content'] : '';
        if ($raw === '' || strlen($raw) > self::MAX_CONTENT_BYTES) {
            $this->jsonResponse(array('ok' => false, 'error' => 'Contenu du bloc vide ou trop volumineux.'), 422);
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || !isset($decoded['type']) || $decoded['type'] !== $type) {
            $this->jsonResponse(array('ok' => false, 'error' => 'Contenu du bloc invalide.'), 422);
        }

        $pdo  = $this->ensureTable();
        $stmt = $pdo->prepare("SELECT id FROM kn_block_presets WHERE name = ? LIMIT 1");
        $stmt->execute(array($name));
        $existing = $stmt->fetchColumn();

        if ($existing !== false) {
            if (empty($_POST['overwrite'])) {
                $this->jsonResponse(array('ok' => false, 'exists' => true), 409);
            }
            $pdo->prepare("UPDATE kn_block_presets SET type = ?, content = ? WHERE id = ?")
                ->execute(array($type, $raw, (int) $existing));
            $this->jsonResponse(array('ok' => true, 'id' => (int) $existing, 'updated' => true));
        }

        $pdo->prepare("INSERT INTO kn_block_presets (name, type, content) VALUES (?, ?, ?)")
            ->execute(array($name, $type, $raw));
        $this->jsonResponse(array('ok' => true, 'id' => (int) $pdo->lastInsertId(), 'updated' => false));
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        $this->requireCsrf();
        $pdo = $this->ensureTable();
        $pdo->prepare("DELETE FROM kn_block_presets WHERE id = ?")->execute(array((int) $id));
        $this->jsonResponse(array('ok' => true));
    }
}
