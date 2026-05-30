<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Project;

class ProjectController extends BaseController
{
    private $projectModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = new Project();
    }

    public function index(): void
    {
        $this->requireLogin();
        $projects  = array_merge($this->projectModel->findAll('fr'), $this->projectModel->findAll('nl'));
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/projects/index', [
            'title'      => 'Projets',
            'projects'   => $projects,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/projects/form', [
            'title'      => 'Nouveau projet',
            'csrf_token' => $csrfToken,
            'project'    => null,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }

        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/projects/new');
        }

        $lang   = in_array(isset($_POST['lang']) ? $_POST['lang'] : 'fr', ['fr', 'nl']) ? $_POST['lang'] : 'fr';
        $slug   = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug   = $this->ensureUniqueSlug('projects', $slug, $lang);
        $status = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft', 'published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';

        $content = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;

        $thumbnail = (int)(isset($_POST['thumbnail']) ? $_POST['thumbnail'] : 0) ?: null;

        $this->projectModel->create([
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'excerpt'          => trim(isset($_POST['excerpt']) ? $_POST['excerpt'] : ''),
            'content'          => $content,
            'status'           => $status,
            'sort_order'       => (int)(isset($_POST['sort_order']) ? $_POST['sort_order'] : 0),
            'thumbnail'        => $thumbnail,
            'tags'             => trim(isset($_POST['tags']) ? $_POST['tags'] : ''),
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
        ]);

        Auth::setFlash('success', 'Projet créé avec succès.');
        $this->redirect('/' . ADMIN_PATH . '/projects');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $project = $this->projectModel->findById((int)$id);
        if (!$project) {
            Auth::setFlash('error', 'Projet introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/projects/form', [
            'title'      => 'Modifier le projet',
            'csrf_token' => $csrfToken,
            'project'    => $project,
        ], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }

        $project = $this->projectModel->findById((int)$id);
        if (!$project) {
            Auth::setFlash('error', 'Projet introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }

        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/projects/' . $id . '/edit');
        }

        $this->saveRevision('project', (int)$id, $project['content']);

        $lang   = in_array(isset($_POST['lang']) ? $_POST['lang'] : $project['lang'], ['fr', 'nl']) ? $_POST['lang'] : $project['lang'];
        $slug   = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug   = $this->ensureUniqueSlug('projects', $slug, $lang, (int)$id);
        $status = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft', 'published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';

        $content = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;

        $thumbnail = (int)(isset($_POST['thumbnail']) ? $_POST['thumbnail'] : 0) ?: null;

        $this->projectModel->update((int)$id, [
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'excerpt'          => trim(isset($_POST['excerpt']) ? $_POST['excerpt'] : ''),
            'content'          => $content,
            'status'           => $status,
            'sort_order'       => (int)(isset($_POST['sort_order']) ? $_POST['sort_order'] : 0),
            'thumbnail'        => $thumbnail,
            'tags'             => trim(isset($_POST['tags']) ? $_POST['tags'] : ''),
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
        ]);

        Auth::setFlash('success', 'Projet mis à jour.');
        $this->redirect('/' . ADMIN_PATH . '/projects');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/projects');
        }
        $this->projectModel->delete((int)$id);
        Auth::setFlash('success', 'Projet supprimé.');
        $this->redirect('/' . ADMIN_PATH . '/projects');
    }
}
