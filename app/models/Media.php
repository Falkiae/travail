<?php
declare(strict_types=1);

namespace App\Models;

class Media extends BaseModel
{
    protected string $table = 'kn_media';

    public function findPaginated(int $limit = 60, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM kn_media ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit',  $limit,  \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM kn_media')->fetchColumn();
    }
}
