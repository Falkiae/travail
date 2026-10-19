<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

/** Boîte de réception admin des messages du formulaire de contact. */
class ContactMessageController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();
        $pdo = $this->db();
        ContactController::ensureTable($pdo);
        $messages = $pdo->query(
            "SELECT id, name, email, phone, subject, message, page_url, mail_sent, status, created_at
             FROM kn_contact_messages ORDER BY (status = 'new') DESC, created_at DESC, id DESC LIMIT 300"
        )->fetchAll(\PDO::FETCH_ASSOC);
        $this->view->render('admin/messages/index', [
            'title'      => 'Messages',
            'messages'   => $messages,
            'csrf_token' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    private function guard(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/messages');
        }
    }

    public function status(string $id): void
    {
        $this->guard();
        $status = isset($_POST['status']) && $_POST['status'] === 'new' ? 'new' : 'done';
        $this->db()->prepare("UPDATE kn_contact_messages SET status = ? WHERE id = ?")->execute(array($status, (int) $id));
        $this->redirect('/' . ADMIN_PATH . '/messages');
    }

    public function delete(string $id): void
    {
        $this->guard();
        $this->db()->prepare("DELETE FROM kn_contact_messages WHERE id = ?")->execute(array((int) $id));
        Auth::setFlash('success', 'Message supprimé.');
        $this->redirect('/' . ADMIN_PATH . '/messages');
    }
}
