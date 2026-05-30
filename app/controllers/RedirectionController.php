<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class RedirectionController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();
        $pdo  = $this->db();
        $redirections = $pdo->query("SELECT * FROM kn_redirections ORDER BY id DESC")->fetchAll();
        $csrfToken = Auth::generateCsrfToken();
        $fromPrefill = trim($_GET['from'] ?? '');
        $this->view->render('admin/redirections/index', [
            'title'        => 'Redirections',
            'redirections' => $redirections,
            'csrf_token'   => $csrfToken,
            'fromPrefill'  => $fromPrefill,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/redirections');
        }

        $fromUrl = trim($_POST['from_url'] ?? '');
        $toUrl   = trim($_POST['to_url'] ?? '');

        if ($fromUrl === '' || $toUrl === '') {
            Auth::setFlash('error', 'Les deux URLs sont obligatoires.');
            $this->redirect('/' . ADMIN_PATH . '/redirections');
        }

        $pdo = $this->db();
        $pdo->prepare(
            "INSERT INTO kn_redirections (from_url, to_url) VALUES (?, ?) ON DUPLICATE KEY UPDATE to_url = ?"
        )->execute([$fromUrl, $toUrl, $toUrl]);

        Auth::setFlash('success', 'Redirection ajoutée.');
        $this->redirect('/' . ADMIN_PATH . '/redirections');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/redirections');
        }
        $this->db()->prepare("DELETE FROM kn_redirections WHERE id = ?")->execute([(int)$id]);
        Auth::setFlash('success', 'Redirection supprimée.');
        $this->redirect('/' . ADMIN_PATH . '/redirections');
    }
}
