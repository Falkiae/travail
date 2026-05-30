<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class AdminController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireLogin();
        $this->view->render('admin/dashboard', ['title' => 'Dashboard'], 'admin');
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
