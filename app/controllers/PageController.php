<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Page;

class PageController extends BaseController
{
    private Page $pageModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
    }

    public function index(): void
    {
        $this->requireLogin();
        $pages = $this->pageModel->findAll();
        $this->view->render('admin/pages/index', ['title' => 'Pages', 'pages' => $pages], 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/form', ['title' => 'Nouvelle page', 'csrf_token' => $csrfToken, 'page' => null], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $page = $this->pageModel->findById((int)$id);
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/form', ['title' => 'Modifier la page', 'csrf_token' => $csrfToken, 'page' => $page], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }
}
