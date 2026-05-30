<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Post;

class PostController extends BaseController
{
    private Post $postModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
    }

    public function index(): void
    {
        $this->requireLogin();
        $posts = $this->postModel->findAll();
        $this->view->render('admin/posts/index', ['title' => 'Articles', 'posts' => $posts], 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/posts/form', ['title' => 'Nouvel article', 'csrf_token' => $csrfToken, 'post' => null], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/posts');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $post = $this->postModel->findById((int)$id);
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/posts/form', ['title' => "Modifier l'article", 'csrf_token' => $csrfToken, 'post' => $post], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/posts');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/posts');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/posts');
    }
}
