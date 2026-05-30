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
        $pages = $this->pageModel->findAll('fr');
        $pagesFr = $pages;
        $pagesNl = $this->pageModel->findAll('nl');
        $allPages = array_merge($pagesFr, $pagesNl);
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/index', [
            'title'      => 'Pages',
            'pages'      => $allPages,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/form', [
            'title'      => 'Nouvelle page',
            'csrf_token' => $csrfToken,
            'page'       => null,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }

        $title  = trim($_POST['title'] ?? '');
        $lang   = in_array($_POST['lang'] ?? 'fr', ['fr', 'nl']) ? $_POST['lang'] : 'fr';
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/pages/new');
        }

        $slug = trim($_POST['slug'] ?? '') ?: $this->slugify($title);
        $slug = $this->slugify($slug);
        $slug = $this->ensureUniqueSlug('pages', $slug, $lang);

        $allowedTemplates = ['home', 'page', 'blog-list', 'portfolio'];
        $template = in_array($_POST['template'] ?? 'page', $allowedTemplates) ? $_POST['template'] : 'page';
        $status   = in_array($_POST['status'] ?? 'draft', ['draft', 'published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';

        $content = $_POST['content'] ?? null;
        if ($content !== null && json_decode($content) === null) $content = null;

        $robots = in_array($_POST['robots'] ?? 'index,follow', ['index,follow','noindex,follow','noindex,nofollow'])
            ? $_POST['robots'] : 'index,follow';

        $id = $this->pageModel->create([
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'template'         => $template,
            'status'           => $status,
            'content'          => $content,
            'meta_title'       => mb_substr(trim($_POST['meta_title'] ?? ''), 0, 70),
            'meta_description' => mb_substr(trim($_POST['meta_description'] ?? ''), 0, 160),
            'og_image'         => trim($_POST['og_image'] ?? ''),
            'canonical_url'    => trim($_POST['canonical_url'] ?? ''),
            'robots'           => $robots,
        ]);

        Auth::setFlash('success', 'Page créée avec succès.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $page = $this->pageModel->findById((int)$id);
        if (!$page) {
            Auth::setFlash('error', 'Page introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/form', [
            'title'      => 'Modifier la page',
            'csrf_token' => $csrfToken,
            'page'       => $page,
        ], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }

        $page = $this->pageModel->findById((int)$id);
        if (!$page) {
            Auth::setFlash('error', 'Page introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }

        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/pages/' . $id . '/edit');
        }

        $lang = in_array($_POST['lang'] ?? $page['lang'], ['fr', 'nl']) ? $_POST['lang'] : $page['lang'];
        $slug = trim($_POST['slug'] ?? '') ?: $this->slugify($title);
        $slug = $this->slugify($slug);
        $slug = $this->ensureUniqueSlug('pages', $slug, $lang, (int)$id);

        // Save revision before update
        $this->saveRevision('page', (int)$id, $page['content']);

        // Auto-redirection if slug changed
        if ($slug !== $page['slug'] || $lang !== $page['lang']) {
            $oldUrl = '/' . $page['slug'];
            $newUrl = '/' . $slug;
            $pdo = $this->db();
            $pdo->prepare(
                "INSERT INTO redirections (from_url, to_url) VALUES (?, ?) ON DUPLICATE KEY UPDATE to_url = ?"
            )->execute([$oldUrl, $newUrl, $newUrl]);
        }

        $allowedTemplates = ['home', 'page', 'blog-list', 'portfolio'];
        $template = in_array($_POST['template'] ?? 'page', $allowedTemplates) ? $_POST['template'] : 'page';
        $status   = in_array($_POST['status'] ?? 'draft', ['draft', 'published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';

        $content = $_POST['content'] ?? null;
        if ($content !== null && json_decode($content) === null) $content = null;

        $robots = in_array($_POST['robots'] ?? 'index,follow', ['index,follow','noindex,follow','noindex,nofollow'])
            ? $_POST['robots'] : 'index,follow';

        $this->pageModel->update((int)$id, [
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'template'         => $template,
            'status'           => $status,
            'content'          => $content,
            'meta_title'       => mb_substr(trim($_POST['meta_title'] ?? ''), 0, 70),
            'meta_description' => mb_substr(trim($_POST['meta_description'] ?? ''), 0, 160),
            'og_image'         => trim($_POST['og_image'] ?? ''),
            'canonical_url'    => trim($_POST['canonical_url'] ?? ''),
            'robots'           => $robots,
        ]);

        Auth::setFlash('success', 'Page mise à jour.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $this->pageModel->delete((int)$id);
        Auth::setFlash('success', 'Page supprimée.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }
}
