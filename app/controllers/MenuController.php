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
        $this->view->render('admin/menus/index', array(
            'title' => 'Menus',
            'menus' => $menus,
        ), 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/menus/create', array(
            'title'      => 'Nouveau menu',
            'csrf_token' => $csrfToken,
        ), 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/menus/new');
        }

        $name     = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $location = isset($_POST['location']) ? $_POST['location'] : 'header';
        $lang     = isset($_POST['lang']) ? $_POST['lang'] : 'fr';

        if ($name === '') {
            Auth::setFlash('error', 'Le nom est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/menus/new');
        }

        if (!in_array($location, array('header', 'footer'), true)) {
            $location = 'header';
        }
        if (!in_array($lang, array('fr', 'nl'), true)) {
            $lang = 'fr';
        }

        $pdo  = $this->db();
        $stmt = $pdo->prepare(
            "INSERT INTO kn_menus (name, location, lang, items) VALUES (?, ?, ?, '[]')"
        );
        $stmt->execute(array($name, $location, $lang));
        $newId = $pdo->lastInsertId();

        Auth::setFlash('success', 'Menu créé. Ajoutez des items ci-dessous.');
        $this->redirect('/' . ADMIN_PATH . '/menus/' . (int)$newId . '/edit');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $pdo  = $this->db();
        $stmt = $pdo->prepare("SELECT * FROM kn_menus WHERE id = ? LIMIT 1");
        $stmt->execute(array((int)$id));
        $menu = $stmt->fetch();
        if (!$menu) {
            Auth::setFlash('error', 'Menu introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/menus');
        }

        $raw   = isset($menu['items']) && $menu['items'] ? $menu['items'] : '[]';
        $items = json_decode($raw, true);
        if (!is_array($items)) {
            $items = array();
        }

        $pagesStmt = $pdo->prepare(
            "SELECT slug, title, lang FROM kn_pages WHERE status = 'published' AND lang = ? ORDER BY title"
        );
        $pagesStmt->execute(array($menu['lang']));
        $pages = $pagesStmt->fetchAll(\PDO::FETCH_ASSOC);

        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/menus/edit', array(
            'title'      => 'Modifier le menu : ' . $menu['name'],
            'menu'       => $menu,
            'items'      => $items,
            'pages'      => $pages,
            'csrf_token' => $csrfToken,
        ), 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/menus');
        }

        $pdo  = $this->db();
        $stmt = $pdo->prepare("SELECT id FROM kn_menus WHERE id = ? LIMIT 1");
        $stmt->execute(array((int)$id));
        if (!$stmt->fetch()) {
            Auth::setFlash('error', 'Menu introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/menus');
        }

        $rawJson = isset($_POST['items_json']) ? $_POST['items_json'] : '[]';
        $decoded = json_decode($rawJson, true);
        if (!is_array($decoded)) {
            $decoded = array();
        }
        $itemsJson = json_encode($decoded);

        $name     = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $location = isset($_POST['location']) ? $_POST['location'] : 'header';
        if (!in_array($location, array('header', 'footer'), true)) {
            $location = 'header';
        }
        if ($name === '') {
            Auth::setFlash('error', 'Le nom du menu est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/menus/' . (int)$id . '/edit');
        }

        $upd = $pdo->prepare("UPDATE kn_menus SET items = ?, name = ?, location = ? WHERE id = ?");
        $upd->execute(array($itemsJson, $name, $location, (int)$id));

        Auth::setFlash('success', 'Menu mis à jour.');
        $this->redirect('/' . ADMIN_PATH . '/menus/' . (int)$id . '/edit');
    }

    public function deleteMenu(string $id): void
    {
        $this->requireLogin();
        $pdo  = $this->db();
        $stmt = $pdo->prepare("DELETE FROM kn_menus WHERE id = ?");
        $stmt->execute(array((int)$id));

        Auth::setFlash('success', 'Menu supprimé.');
        $this->redirect('/' . ADMIN_PATH . '/menus');
    }
}
