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
        $limit      = 60;
        $mediaModel = new Media();
        $media      = $mediaModel->findAll($limit, 0);
        $total      = $mediaModel->count();
        $csrfToken  = Auth::generateCsrfToken();
        $this->view->render('admin/media/index', [
            'title'      => 'Médiathèque',
            'media'      => $media,
            'total'      => $total,
            'has_more'   => $total > $limit,
            'csrf_token' => $csrfToken,
        ], 'admin');
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
        $finfo   = new \finfo(FILEINFO_MIME_TYPE);
        $mime    = $finfo->file($file['tmp_name']);
        $isVideo = (strncmp($mime, 'video/', 6) === 0);
        $maxSize = $isVideo ? 100 * 1024 * 1024 : MAX_UPLOAD_SIZE;
        if ($file['size'] > $maxSize) {
            $maxLabel = $isVideo ? '100 Mo' : '5 Mo';
            Auth::setFlash('error', 'Fichier trop lourd (max ' . $maxLabel . ').');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'application/pdf', 'video/mp4', 'video/webm', 'video/ogg'];
        if (!in_array($mime, $allowed, true)) {
            Auth::setFlash('error', 'Type de fichier non autorisé.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        switch ($mime) {
            case 'image/jpeg':      $ext = 'jpg';  break;
            case 'image/png':       $ext = 'png';  break;
            case 'image/gif':       $ext = 'gif';  break;
            case 'image/webp':      $ext = 'webp'; break;
            case 'image/svg+xml':   $ext = 'svg';  break;
            case 'application/pdf': $ext = 'pdf';  break;
            case 'video/mp4':       $ext = 'mp4';  break;
            case 'video/webm':      $ext = 'webm'; break;
            case 'video/ogg':       $ext = 'ogv';  break;
            default:                $ext = 'bin';  break;
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest     = UPLOAD_DIR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Auth::setFlash('error', 'Erreur déplacement fichier.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }
        $width = null; $height = null;
        $webpPath = null;
        $sizesJson = null;
        $isImage    = !$isVideo && strncmp($mime, 'image/', 6) === 0;
        $isSvg      = ($mime === 'image/svg+xml');
        $canProcess = $isImage && !$isSvg && in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true);

        if ($canProcess) {
            $img = $this->loadImage($dest, $mime);
            if ($img) {
                $width  = imagesx($img);
                $height = imagesy($img);

                if ($width > 1920) {
                    $newH = (int) round($height * (1920 / $width));
                    $resized = imagecreatetruecolor(1920, $newH);
                    $this->preserveTransparency($resized, $mime);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, 1920, $newH, $width, $height);
                    imagedestroy($img);
                    $img = $resized;
                    $width = 1920;
                    $height = $newH;
                }

                $this->saveImage($img, $dest, $mime);

                if (function_exists('imagewebp')) {
                    $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                    imagewebp($img, UPLOAD_DIR . $webpFilename, 82);
                    $webpPath = '/uploads/' . $webpFilename;
                }

                $sizesJson = $this->generateSizes($img, $filename, $mime, $width, $height);

                imagedestroy($img);
            }
        } elseif ($isImage && !$isSvg && $mime !== 'image/webp') {
            $size   = getimagesize($dest);
            $width  = $size ? $size[0] : null;
            $height = $size ? $size[1] : null;
        }

        (new Media())->create([
            'filename'      => $filename,
            'original_name' => basename($file['name']),
            'path'          => '/uploads/' . $filename,
            'webp_path'     => $webpPath,
            'sizes'         => $sizesJson,
            'alt'           => '',
            'mime_type'     => $mime,
            'size'          => filesize($dest),
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
            if (!empty($media['sizes'])) {
                $sizes = json_decode($media['sizes'], true);
                if (is_array($sizes)) {
                    foreach ($sizes as $path) {
                        @unlink(UPLOAD_DIR . basename($path));
                    }
                }
            }
            (new Media())->delete($id);
        }
        Auth::setFlash('success', 'Fichier supprimé.');
        $this->redirect('/' . ADMIN_PATH . '/media');
    }

    public function jsonList(): void
    {
        $this->requireLogin();
        $limit      = 60;
        $offset     = max(0, (int)($_GET['offset'] ?? 0));
        $mediaModel = new Media();
        $items      = $mediaModel->findAll($limit, $offset);
        $total      = $mediaModel->count();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['items' => $items, 'total' => $total, 'offset' => $offset, 'limit' => $limit]);
        exit;
    }

    public function reprocessAll(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token invalide.');
            $this->redirect('/' . ADMIN_PATH . '/media');
        }

        set_time_limit(300);

        $pdo = $this->db();
        $stmt = $pdo->prepare(
            "SELECT * FROM kn_media WHERE mime_type IN ('image/jpeg','image/png','image/gif') AND (sizes IS NULL OR sizes = '')"
        );
        $stmt->execute();
        $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $count = 0;
        $mediaModel = new Media();

        foreach ($images as $row) {
            $dest = UPLOAD_DIR . $row['filename'];
            if (!file_exists($dest)) continue;

            $mime = $row['mime_type'];
            $img = $this->loadImage($dest, $mime);
            if (!$img) continue;

            $width  = imagesx($img);
            $height = imagesy($img);

            if ($width > 1920) {
                $newH = (int) round($height * (1920 / $width));
                $resized = imagecreatetruecolor(1920, $newH);
                $this->preserveTransparency($resized, $mime);
                imagecopyresampled($resized, $img, 0, 0, 0, 0, 1920, $newH, $width, $height);
                imagedestroy($img);
                $img = $resized;
                $width = 1920;
                $height = $newH;
            }

            $this->saveImage($img, $dest, $mime);

            $webpPath = $row['webp_path'];
            if (function_exists('imagewebp')) {
                $webpFilename = pathinfo($row['filename'], PATHINFO_FILENAME) . '.webp';
                imagewebp($img, UPLOAD_DIR . $webpFilename, 82);
                $webpPath = '/uploads/' . $webpFilename;
            }

            $sizesJson = $this->generateSizes($img, $row['filename'], $mime, $width, $height);

            imagedestroy($img);

            $mediaModel->update((int) $row['id'], [
                'webp_path' => $webpPath,
                'sizes'     => $sizesJson,
                'width'     => $width,
                'height'    => $height,
                'size'      => filesize($dest),
            ]);

            $count++;
        }

        Auth::setFlash('success', $count . ' image(s) retraitée(s) avec succès.');
        $this->redirect('/' . ADMIN_PATH . '/media');
    }

    private function loadImage(string $path, string $mime)
    {
        switch ($mime) {
            case 'image/jpeg': return imagecreatefromjpeg($path);
            case 'image/png':  return imagecreatefrompng($path);
            case 'image/gif':  return imagecreatefromgif($path);
            default: return null;
        }
    }

    private function saveImage($img, string $path, string $mime): void
    {
        switch ($mime) {
            case 'image/jpeg': imagejpeg($img, $path, 82); break;
            case 'image/png':  imagepng($img, $path, 6); break;
            case 'image/gif':  imagegif($img, $path); break;
        }
    }

    private function preserveTransparency($img, string $mime): void
    {
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($img, false);
            imagesavealpha($img, true);
            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $transparent);
        }
    }

    private function generateSizes($img, string $filename, string $mime, int $origW, int $origH): ?string
    {
        $breakpoints = ['thumb' => 400, 'medium' => 800, 'large' => 1400];
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $sizes = [];

        foreach ($breakpoints as $label => $maxW) {
            if ($origW <= $maxW) {
                continue;
            }
            $newH = (int) round($origH * ($maxW / $origW));
            $resized = imagecreatetruecolor($maxW, $newH);
            $this->preserveTransparency($resized, $mime);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $maxW, $newH, $origW, $origH);

            $sizedFilename = $baseName . '-' . $label . '.' . $ext;
            $this->saveImage($resized, UPLOAD_DIR . $sizedFilename, $mime);
            $sizes[$label] = '/uploads/' . $sizedFilename;

            if (function_exists('imagewebp')) {
                $webpFilename = $baseName . '-' . $label . '.webp';
                imagewebp($resized, UPLOAD_DIR . $webpFilename, 82);
                $sizes[$label . '_webp'] = '/uploads/' . $webpFilename;
            }

            imagedestroy($resized);
        }

        return $sizes ? json_encode($sizes) : null;
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
