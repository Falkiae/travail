<?php
declare(strict_types=1);

namespace App\Models;

class Menu extends BaseModel
{
    protected string $table = 'kn_menus';

    public function findAll(string $lang = 'fr'): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM kn_menus WHERE lang = ? ORDER BY id ASC');
        $stmt->execute([$lang]);
        return $stmt->fetchAll();
    }

    public function getItemsByMenu(int $menuId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM kn_menu_items WHERE menu_id = ? ORDER BY sort_order ASC');
        $stmt->execute([$menuId]);
        return $stmt->fetchAll();
    }
}
