<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class BlockController extends BaseController
{
    private $blockTypes = [
        'hero', 'services', 'two-col', 'how', 'reviews', 'zone',
        'accordion', 'cta-final', 'heading', 'text', 'image',
        'cta', 'html', 'video', 'file', 'quote', 'domicile-vs-atelier',
    ];

    public function index(): void
    {
        $this->requireLogin();
        $pdo      = $this->db();
        $blockCss = array();
        $stmt     = $pdo->query("SELECT `key`, `value` FROM kn_settings WHERE `key` LIKE 'block_css_%'");
        while ($row = $stmt->fetch()) {
            $type             = substr($row['key'], 10);
            $blockCss[$type]  = $row['value'];
        }
        $this->view->render('admin/blocks/index', [
            'title'      => 'Blocs',
            'blockTypes' => $this->blockTypes,
            'blockCss'   => $blockCss,
        ], 'admin');
    }

    public function edit(string $type): void
    {
        $this->requireLogin();
        if (!in_array($type, $this->blockTypes, true)) {
            Auth::setFlash('error', 'Type de bloc invalide.');
            $this->redirect('/' . ADMIN_PATH . '/blocks');
        }
        $pdo     = $this->db();
        $file    = APP_PATH . '/views/blocks/' . $type . '.php';
        $content = file_exists($file) ? file_get_contents($file) : '';
        $cssKey  = 'block_css_' . $type;
        $stmt    = $pdo->prepare("SELECT `value` FROM kn_settings WHERE `key` = ? LIMIT 1");
        $stmt->execute([$cssKey]);
        $row       = $stmt->fetch();
        $css       = $row ? $row['value'] : '';
        $backupDir = ROOT_PATH . '/storage/block-backups';
        $backups   = file_exists($backupDir) ? glob($backupDir . '/' . $type . '_*.php') : array();
        $hasBackup = is_array($backups) && count($backups) > 0;
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/blocks/edit', [
            'title'      => 'Bloc : ' . $type,
            'type'       => $type,
            'content'    => $content,
            'css'        => $css,
            'hasBackup'  => $hasBackup,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function update(string $type): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/blocks/' . $type . '/edit');
        }
        if (!in_array($type, $this->blockTypes, true)) {
            Auth::setFlash('error', 'Type de bloc invalide.');
            $this->redirect('/' . ADMIN_PATH . '/blocks');
        }

        $pdo = $this->db();

        // Save CSS
        $css    = isset($_POST['css']) ? $_POST['css'] : '';
        $cssKey = 'block_css_' . $type;
        $pdo->prepare("INSERT INTO kn_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?")
            ->execute([$cssKey, $css, $css]);

        // Save PHP template
        $phpContent = isset($_POST['php_content']) ? $_POST['php_content'] : '';
        if (trim($phpContent) !== '') {
            $file    = APP_PATH . '/views/blocks/' . $type . '.php';
            $tmpFile = sys_get_temp_dir() . '/kn_block_' . $type . '_' . time() . '.php';
            file_put_contents($tmpFile, $phpContent);
            $lintOut = shell_exec('php -l ' . escapeshellarg($tmpFile) . ' 2>&1');
            @unlink($tmpFile);

            if (strpos($lintOut, 'No syntax errors') === false) {
                Auth::setFlash('error', 'Erreur de syntaxe PHP : ' . strip_tags($lintOut));
                $this->redirect('/' . ADMIN_PATH . '/blocks/' . $type . '/edit');
            }

            // Backup
            if (file_exists($file)) {
                $backupDir = ROOT_PATH . '/storage/block-backups';
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }
                $backup   = $backupDir . '/' . $type . '_' . date('Ymd_His') . '.php';
                copy($file, $backup);
                $existing = glob($backupDir . '/' . $type . '_*.php');
                if (is_array($existing) && count($existing) > 5) {
                    sort($existing);
                    foreach (array_slice($existing, 0, count($existing) - 5) as $old) {
                        @unlink($old);
                    }
                }
            }

            file_put_contents($file, $phpContent);
        }

        Auth::setFlash('success', 'Bloc mis à jour.');
        $this->redirect('/' . ADMIN_PATH . '/blocks/' . $type . '/edit');
    }

    public function restore(string $type): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/blocks/' . $type . '/edit');
        }
        if (!in_array($type, $this->blockTypes, true)) {
            Auth::setFlash('error', 'Type invalide.');
            $this->redirect('/' . ADMIN_PATH . '/blocks');
        }
        $backupDir = ROOT_PATH . '/storage/block-backups';
        $backups   = glob($backupDir . '/' . $type . '_*.php');
        if (!$backups || count($backups) === 0) {
            Auth::setFlash('error', 'Aucune sauvegarde disponible.');
            $this->redirect('/' . ADMIN_PATH . '/blocks/' . $type . '/edit');
        }
        sort($backups);
        $latest = end($backups);
        $file   = APP_PATH . '/views/blocks/' . $type . '.php';
        copy($latest, $file);
        @unlink($latest);
        Auth::setFlash('success', 'Version précédente restaurée.');
        $this->redirect('/' . ADMIN_PATH . '/blocks/' . $type . '/edit');
    }
}
