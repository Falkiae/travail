<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class ErrorLogController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();
        $pdo  = $this->db();
        $logs = $pdo->query("SELECT * FROM kn_error_logs ORDER BY count DESC, last_seen DESC")->fetchAll();
        $this->view->render('admin/error-logs/index', [
            'title' => 'Erreurs 404',
            'logs'  => $logs,
        ], 'admin');
    }
}
