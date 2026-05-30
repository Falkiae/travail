<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Media;

class MediaController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();
        $media = (new Media())->findAll();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/media/index', [
            'title'      => 'Médiathèque',
            'media'      => $media,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function upload(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }

        $file = $_FILES['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Auth::setFlash('error', 'Erreur lors de l\'upload.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            Auth::setFlash('error', 'Fichier trop lourd (max 5 Mo).');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        if (!in_array($mime, $allowed, true)) {
            Auth::setFlash('error', 'Type de fichier non autorisé.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }

        $ext = match($mime) {
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'image/gif'       => 'gif',
            'image/webp'      => 'webp',
            'application/pdf' => 'pdf',
            default           => 'bin',
        };

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest     = UPLOAD_DIR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Auth::setFlash('error', 'Erreur lors du déplacement du fichier.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }

        $width = $height = null;
        if (str_starts_with($mime, 'image/') && $mime !== 'image/webp') {
            [$width, $height] = getimagesize($dest) ?: [null, null];
        }

        $webpPath = null;
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true) && function_exists('imagewebp')) {
            $img = match($mime) {
                'image/jpeg' => imagecreatefromjpeg($dest),
                'image/png'  => imagecreatefrompng($dest),
                'image/gif'  => imagecreatefromgif($dest),
                default      => null,
            };
            if ($img) {
                $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webpDest = UPLOAD_DIR . $webpFilename;
                imagewebp($img, $webpDest, 85);
                imagedestroy($img);
                $webpPath = '/uploads/' . $webpFilename;
            }
        }

        (new Media())->create([
            'filename'      => $filename,
            'original_name' => basename($file['name']),
            'path'          => '/uploads/' . $filename,
            'webp_path'     => $webpPath,
            'alt'           => '',
            'mime_type'     => $mime,
            'size'          => $file['size'],
            'width'         => $width,
            'height'        => $height,
        ]);

        Auth::setFlash('success', 'Fichier uploadé avec succès.');
        $this->redirect('/' . ADMIN_PATH . '/media');
    }

    public function updateAlt(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['error' => 'Token invalide'], 403);
        }
        $id  = (int)($_POST['id'] ?? 0);
        $alt = trim($_POST['alt'] ?? '');
        (new Media())->update($id, ['alt' => $alt]);
        $this->json(['ok' => true]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['error' => 'Token invalide'], 403);
        }
        $id    = (int)($_POST['id'] ?? 0);
        $media = (new Media())->findById($id);
        if ($media) {
            @unlink(UPLOAD_DIR . $media['filename']);
            if (!empty($media['webp_path'])) {
                @unlink(UPLOAD_DIR . basename($media['webp_path']));
            }
            (new Media())->delete($id);
        }
        Auth::setFlash('success', 'Fichier supprimé.');
        $this->redirect('/' . ADMIN_PATH . '/media');
    }

    public function jsonList(): void
    {
        $this->requireLogin();
        $media = (new Media())->findAll();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($media);
        exit;
    }
}
