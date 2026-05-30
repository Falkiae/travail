<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Post;

class PostController extends BaseController
{
    private $postModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
    }

    private function getCategories(): array
    {
        $stmt = $this->db()->query("SELECT id, name FROM kn_categories ORDER BY name");
        return $stmt->fetchAll();
    }

    public function index(): void
    {
        $this->requireLogin();
        $posts     = array_merge($this->postModel->findAll('fr'), $this->postModel->findAll('nl'));
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/posts/index', [
            'title'      => 'Articles',
            'posts'      => $posts,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/posts/form', [
            'title'      => 'Nouvel article',
            'csrf_token' => $csrfToken,
            'post'       => null,
            'categories' => $this->getCategories(),
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }

        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/posts/new');
        }

        $lang   = in_array(isset($_POST['lang']) ? $_POST['lang'] : 'fr', ['fr', 'nl']) ? $_POST['lang'] : 'fr';
        $slug   = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug   = $this->ensureUniqueSlug('posts', $slug, $lang);
        $status = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft', 'published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';

        $content = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;

        $publishedAt = trim(isset($_POST['published_at']) ? $_POST['published_at'] : '');
        $publishedAt = $publishedAt ? date('Y-m-d H:i:s', strtotime($publishedAt)) : null;
        $categoryId  = (int)(isset($_POST['category_id']) ? $_POST['category_id'] : 0) ?: null;
        $featuredImage = (int)(isset($_POST['featured_image']) ? $_POST['featured_image'] : 0) ?: null;

        $this->postModel->create([
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'excerpt'          => trim(isset($_POST['excerpt']) ? $_POST['excerpt'] : ''),
            'content'          => $content,
            'status'           => $status,
            'published_at'     => $publishedAt,
            'category_id'      => $categoryId,
            'featured_image'   => $featuredImage,
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
        ]);

        Auth::setFlash('success', 'Article créé avec succès.');
        $this->redirect('/' . ADMIN_PATH . '/posts');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $post = $this->postModel->findById((int)$id);
        if (!$post) {
            Auth::setFlash('error', 'Article introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/posts/form', [
            'title'      => "Modifier l'article",
            'csrf_token' => $csrfToken,
            'post'       => $post,
            'categories' => $this->getCategories(),
        ], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }

        $post = $this->postModel->findById((int)$id);
        if (!$post) {
            Auth::setFlash('error', 'Article introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }

        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/posts/' . $id . '/edit');
        }

        $this->saveRevision('post', (int)$id, $post['content']);

        $lang   = in_array(isset($_POST['lang']) ? $_POST['lang'] : $post['lang'], ['fr', 'nl']) ? $_POST['lang'] : $post['lang'];
        $slug   = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug   = $this->ensureUniqueSlug('posts', $slug, $lang, (int)$id);
        $status = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft', 'published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';

        $content = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;

        $publishedAt   = trim(isset($_POST['published_at']) ? $_POST['published_at'] : '');
        $publishedAt   = $publishedAt ? date('Y-m-d H:i:s', strtotime($publishedAt)) : null;
        $categoryId    = (int)(isset($_POST['category_id']) ? $_POST['category_id'] : 0) ?: null;
        $featuredImage = (int)(isset($_POST['featured_image']) ? $_POST['featured_image'] : 0) ?: null;

        $this->postModel->update((int)$id, [
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'excerpt'          => trim(isset($_POST['excerpt']) ? $_POST['excerpt'] : ''),
            'content'          => $content,
            'status'           => $status,
            'published_at'     => $publishedAt,
            'category_id'      => $categoryId,
            'featured_image'   => $featuredImage,
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
        ]);

        Auth::setFlash('success', 'Article mis à jour.');
        $this->redirect('/' . ADMIN_PATH . '/posts');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }
        $this->postModel->delete((int)$id);
        Auth::setFlash('success', 'Article supprimé.');
        $this->redirect('/' . ADMIN_PATH . '/posts');
    }
}
