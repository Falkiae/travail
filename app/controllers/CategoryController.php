<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class CategoryController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();
        $stmt = $this->db()->query(
            "SELECT c.*, (SELECT COUNT(*) FROM kn_posts p WHERE p.category_id = c.id) AS post_count
             FROM kn_categories c ORDER BY c.lang, c.name"
        );
        $categories = $stmt->fetchAll();
        $csrfToken  = Auth::generateCsrfToken();
        $this->view->render('admin/categories/index', [
            'title'      => 'Catégories du blog',
            'categories' => $categories,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/categories');
        }

        $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
        if ($name === '') {
            Auth::setFlash('error', 'Le nom est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/categories');
        }

        $lang = in_array(isset($_POST['lang']) ? $_POST['lang'] : 'fr', ['fr', 'nl']) ? $_POST['lang'] : 'fr';
        $slug = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $name);
        $slug = $this->ensureUniqueSlug('categories', $slug, $lang);

        $this->db()->prepare('INSERT INTO kn_categories (lang, slug, name) VALUES (?, ?, ?)')
            ->execute([$lang, $slug, $name]);

        Auth::setFlash('success', 'Catégorie créée.');
        $this->redirect('/' . ADMIN_PATH . '/categories');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/categories');
        }

        // Détache les articles plutôt que de les laisser pointer vers une catégorie fantôme
        $this->db()->prepare('UPDATE kn_posts SET category_id = NULL WHERE category_id = ?')->execute([(int) $id]);
        $this->db()->prepare('DELETE FROM kn_categories WHERE id = ?')->execute([(int) $id]);

        Auth::setFlash('success', 'Catégorie supprimée.');
        $this->redirect('/' . ADMIN_PATH . '/categories');
    }
}
