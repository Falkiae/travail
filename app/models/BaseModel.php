<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

abstract class BaseModel
{
    protected PDO $pdo;
    protected string $table;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findAll(string $lang = 'fr'): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE lang = ? ORDER BY id DESC");
        $stmt->execute([$lang]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBySlug(string $slug, string $lang = 'fr'): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE slug = ? AND lang = ? LIMIT 1");
        $stmt->execute([$slug, $lang]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $cols   = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($cols) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $sets WHERE id = ?");
        return $stmt->execute([...array_values($data), $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
