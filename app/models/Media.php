<?php
declare(strict_types=1);

namespace App\Models;

class Media extends BaseModel
{
    protected string $table = 'kn_media';

    public function findAll(string $lang = 'fr'): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM kn_media ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
