<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class MenuController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();
        $pdo   = $this->db();
        $menus = $pdo->query("SELECT * FROM kn_menus ORDER BY lang, location")->fetchAll();
        $this->view->render('admin/menus/index', [
            'title' => 'Menus',
            'menus' => $menus,
        ], 'admin');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $pdo  = $this->db();
        $stmt = $pdo->prepare("SELECT * FROM kn_menus WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        $menu = $stmt->fetch();
        if (!$menu) {
            Auth::setFlash('error', 'Menu introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/menus');
        }
        $itemStmt = $pdo->prepare("SELECT * FROM kn_menu_items WHERE menu_id = ? ORDER BY sort_order");
        $itemStmt->execute([(int)$id]);
        $items = $itemStmt->fetchAll();

        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/menus/form', [
            'title'      => 'Modifier le menu : ' . $menu['name'],
            'menu'       => $menu,
            'items'      => $items,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/menus');
        }

        $pdo = $this->db();
        // Check menu exists
        $stmt = $pdo->prepare("SELECT id FROM kn_menus WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        if (!$stmt->fetch()) {
            Auth::setFlash('error', 'Menu introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/menus');
        }

        // Delete existing items and re-insert
        $pdo->prepare("DELETE FROM kn_menu_items WHERE menu_id = ?")->execute([(int)$id]);

        $items = $_POST['items'] ?? [];
        foreach ($items as $i => $item) {
            $label  = trim($item['label'] ?? '');
            $url    = trim($item['url'] ?? '');
            $target = in_array($item['target'] ?? '_self', ['_self', '_blank']) ? $item['target'] : '_self';
            $order  = (int)($item['sort_order'] ?? $i);
            if ($label === '') continue;
            $pdo->prepare("INSERT INTO kn_menu_items (menu_id, label, url, target, sort_order) VALUES (?, ?, ?, ?, ?)")
                ->execute([(int)$id, $label, $url, $target, $order]);
        }

        Auth::setFlash('success', 'Menu mis à jour.');
        $this->redirect('/' . ADMIN_PATH . '/menus');
    }
}
