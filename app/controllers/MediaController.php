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
        $media     = (new Media())->findAll();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/media/index', ['title' => 'Médiathèque', 'media' => $media, 'csrf_token' => $csrfToken], 'admin');
    }

    public function upload(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token invalide.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Auth::setFlash('error', 'Erreur upload.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            Auth::setFlash('error', 'Fichier trop lourd (max 5 Mo).');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        $finfo   = new \finfo(FILEINFO_MIME_TYPE);
        $mime    = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        if (!in_array($mime, $allowed, true)) {
            Auth::setFlash('error', 'Type de fichier non autorisé.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        switch ($mime) {
            case 'image/jpeg':      $ext = 'jpg'; break;
            case 'image/png':       $ext = 'png'; break;
            case 'image/gif':       $ext = 'gif'; break;
            case 'image/webp':      $ext = 'webp'; break;
            case 'application/pdf': $ext = 'pdf'; break;
            default:                $ext = 'bin'; break;
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest     = UPLOAD_DIR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Auth::setFlash('error', 'Erreur déplacement fichier.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        $width = null; $height = null;
        if (strncmp($mime, 'image/', 6) === 0 && $mime !== 'image/webp') {
            $size   = getimagesize($dest);
            $width  = $size ? $size[0] : null;
            $height = $size ? $size[1] : null;
        }
        $webpPath = null;
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true) && function_exists('imagewebp')) {
            switch ($mime) {
                case 'image/jpeg': $img = imagecreatefromjpeg($dest); break;
                case 'image/png':  $img = imagecreatefrompng($dest); break;
                case 'image/gif':  $img = imagecreatefromgif($dest); break;
                default:           $img = null; break;
            }
            if ($img) {
                $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                imagewebp($img, UPLOAD_DIR . $webpFilename, 85);
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
        Auth::setFlash('success', 'Fichier uploadé.');
        $this->redirect('/' . ADMIN_PATH . '/media');
    }

    public function updateAlt(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            $this->jsonResponse(['error' => 'Token invalide'], 403);
        }
        $id  = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
        $alt = trim(isset($_POST['alt']) ? $_POST['alt'] : '');
        (new Media())->update($id, ['alt' => $alt]);
        $this->jsonResponse(['ok' => true]);
    }

    public function deleteMedia(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token invalide.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        $id    = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
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

    public function serveFile(string $file): void
    {
        // Sanitize: no path traversal
        $file = basename($file);
        $path = UPLOAD_DIR . $file;
        if (!$file || !file_exists($path) || !is_file($path)) {
            http_response_code(404);
            exit;
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($path);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=31536000');
        readfile($path);
        exit;
    }
}
