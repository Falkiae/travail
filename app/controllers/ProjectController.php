<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Project;

class ProjectController extends BaseController
{
    private Project $projectModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = new Project();
    }

    public function index(): void
    {
        $this->requireLogin();
        $projects = $this->projectModel->findAll();
        $this->view->render('admin/projects/index', ['title' => 'Projets', 'projects' => $projects], 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/projects/form', ['title' => 'Nouveau projet', 'csrf_token' => $csrfToken, 'project' => null], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/projects');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $project = $this->projectModel->findById((int)$id);
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/projects/form', ['title' => 'Modifier le projet', 'csrf_token' => $csrfToken, 'project' => $project], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/projects');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }
        // TODO Phase 2
        $this->redirect('/' . ADMIN_PATH . '/projects');
    }
}
