<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Page;

class PageController extends BaseController
{
    private $pageModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
    }

    public function index(): void
    {
        $this->requireLogin();
        $pages     = array_merge($this->pageModel->findAll('fr'), $this->pageModel->findAll('nl'));
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/index', ['title' => 'Pages', 'pages' => $pages, 'csrf_token' => $csrfToken], 'admin');
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
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/pages/new');
        }
        $lang     = in_array(isset($_POST['lang']) ? $_POST['lang'] : 'fr', ['fr', 'nl']) ? $_POST['lang'] : 'fr';
        $slug     = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug     = $this->ensureUniqueSlug('pages', $slug, $lang);
        $template = in_array(isset($_POST['template']) ? $_POST['template'] : 'page', ['home','page','blog-list','portfolio']) ? $_POST['template'] : 'page';
        $status   = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft','published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';
        $content  = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;
        $robots   = in_array(isset($_POST['robots']) ? $_POST['robots'] : 'index,follow', ['index,follow','noindex,follow','noindex,nofollow']) ? $_POST['robots'] : 'index,follow';

        $this->pageModel->create([
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'template'         => $template,
            'status'           => $status,
            'content'          => $content,
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
            'canonical_url'    => trim(isset($_POST['canonical_url']) ? $_POST['canonical_url'] : ''),
            'robots'           => $robots,
        ]);
        Auth::setFlash('success', 'Page créée.');
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
        $this->view->render('admin/pages/form', ['title' => 'Modifier la page', 'csrf_token' => $csrfToken, 'page' => $page], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $page = $this->pageModel->findById((int)$id);
        if (!$page) {
            Auth::setFlash('error', 'Page introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/pages/' . $id . '/edit');
        }
        $lang    = in_array(isset($_POST['lang']) ? $_POST['lang'] : $page['lang'], ['fr','nl']) ? $_POST['lang'] : $page['lang'];
        $slug    = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug    = $this->ensureUniqueSlug('pages', $slug, $lang, (int)$id);
        $this->saveRevision('page', (int)$id, $page['content']);
        if ($slug !== $page['slug']) {
            $pdo = $this->db();
            $pdo->prepare("INSERT INTO kn_redirections (from_url, to_url) VALUES (?, ?) ON DUPLICATE KEY UPDATE to_url = ?")
                ->execute(['/' . $page['slug'], '/' . $slug, '/' . $slug]);
        }
        $template = in_array(isset($_POST['template']) ? $_POST['template'] : 'page', ['home','page','blog-list','portfolio']) ? $_POST['template'] : 'page';
        $status   = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft','published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';
        $content  = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;
        $robots   = in_array(isset($_POST['robots']) ? $_POST['robots'] : 'index,follow', ['index,follow','noindex,follow','noindex,nofollow']) ? $_POST['robots'] : 'index,follow';
        $this->pageModel->update((int)$id, [
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'template'         => $template,
            'status'           => $status,
            'content'          => $content,
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
            'canonical_url'    => trim(isset($_POST['canonical_url']) ? $_POST['canonical_url'] : ''),
            'robots'           => $robots,
        ]);
        Auth::setFlash('success', 'Page mise à jour.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $this->pageModel->delete((int)$id);
        Auth::setFlash('success', 'Page supprimée.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function seed(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }

        $defaults = [
            [
                'slug' => 'home', 'lang' => 'fr', 'title' => 'Accueil',
                'template' => 'home', 'status' => 'published',
                'meta_title' => 'Nettoyage de canapés, matelas & voitures à domicile | Keepnew',
                'meta_description' => 'Keepnew — Nettoyage professionnel à domicile : canapés, matelas, voitures, terrasses. Liège, Namur, Bruxelles, Luxembourg. Devis gratuit.',
                'content' => '[]', 'sort_order' => 0,
            ],
            [
                'slug' => 'blog', 'lang' => 'fr', 'title' => 'Blog',
                'template' => 'blog-list', 'status' => 'published',
                'meta_title' => 'Blog nettoyage à domicile — Conseils & astuces | Keepnew',
                'meta_description' => 'Conseils et actualités sur le nettoyage à domicile par Keepnew.',
                'content' => '[]', 'sort_order' => 2,
            ],
            [
                'slug' => 'portfolio', 'lang' => 'fr', 'title' => 'Réalisations',
                'template' => 'portfolio', 'status' => 'published',
                'meta_title' => 'Nos réalisations — Avant / Après | Keepnew',
                'meta_description' => 'Découvrez les réalisations de Keepnew : canapés, matelas et voitures nettoyés.',
                'content' => '[]', 'sort_order' => 3,
            ],
        ];

        $pdo  = $this->db();
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO kn_pages (slug, lang, title, template, status, meta_title, meta_description, content, sort_order)
             VALUES (:slug, :lang, :title, :template, :status, :meta_title, :meta_description, :content, :sort_order)'
        );

        foreach ($defaults as $page) {
            $stmt->execute($page);
        }

        Auth::setFlash('success', 'Pages par défaut créées.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }
}
