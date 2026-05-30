<?php
declare(strict_types=1);

namespace App\Models;

class Media extends BaseModel
{
    protected string $table = 'media';

    public function findAll(string $lang = 'fr'): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM media ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
