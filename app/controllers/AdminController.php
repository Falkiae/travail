<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class AdminController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireLogin();
        $pdo = $this->db();
        $stats = [
            'pages'    => $pdo->query("SELECT COUNT(*) FROM kn_pages WHERE status = 'published'")->fetchColumn(),
            'posts'    => $pdo->query("SELECT COUNT(*) FROM kn_posts WHERE status = 'published'")->fetchColumn(),
            'projects' => $pdo->query("SELECT COUNT(*) FROM kn_projects WHERE status = 'published'")->fetchColumn(),
            'errors404'=> $pdo->query("SELECT COUNT(*) FROM kn_error_logs")->fetchColumn(),
        ];
        $this->view->render('admin/dashboard', ['title' => 'Dashboard', 'stats' => $stats], 'admin');
    }

    public function loginForm(): void
    {
        if (Auth::isLoggedIn()) {
            $this->redirect('/' . ADMIN_PATH);
        }
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/login', ['title' => 'Connexion', 'csrf_token' => $csrfToken], 'admin');
    }

    public function loginPost(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!Auth::verifyCsrfToken($token)) {
            $this->redirect('/' . ADMIN_PATH . '/login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::login($email, $password)) {
            $this->redirect('/' . ADMIN_PATH);
        }

        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/login', [
            'title'      => 'Connexion',
            'csrf_token' => $csrfToken,
            'error'      => 'Email ou mot de passe incorrect.',
        ], 'admin');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/' . ADMIN_PATH . '/login');
    }
}
