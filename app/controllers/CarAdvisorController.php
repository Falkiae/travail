<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

require_once APP_PATH . '/views/blocks/_car_advisor_config.php';

/**
 * Admin › Simulateur auto : configuration unique du bloc car-advisor
 * (formules, grille de prix, suppléments, parcours & recommandations, textes).
 */
class CarAdvisorController extends BaseController
{
    private const KEY = 'car_advisor_config';

    public function index(): void
    {
        $this->requireLogin();
        $pdo = $this->db();

        $config = null; $source = 'saved';
        $stmt = $pdo->prepare("SELECT `value` FROM kn_settings WHERE `key` = ? LIMIT 1");
        $stmt->execute(array(self::KEY));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && $row['value'] !== '') {
            $decoded = json_decode($row['value'], true);
            if (is_array($decoded)) $config = knCaNormalize($decoded);
        }
        if ($config === null) {
            $legacy = $this->findLegacyBlock($pdo);
            if ($legacy) { $config = knCaLoad($legacy); $source = 'legacy'; }
            else { $config = knCaNormalize(knCaDefaultConfig()); $source = 'default'; }
        }

        $this->view->render('admin/car-advisor/index', array(
            'title'       => 'Simulateur auto',
            'config'      => $config,
            'source'      => $source,
            'preview_url' => $this->findPreviewUrl($pdo),
            'csrf_token'  => Auth::generateCsrfToken(),
        ), 'admin');
    }

    public function update(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/car-advisor');
        }

        $in = array(
            'formulas' => array_values(isset($_POST['formulas']) && is_array($_POST['formulas']) ? $_POST['formulas'] : array()),
            'extras'   => array_values(isset($_POST['extras'])   && is_array($_POST['extras'])   ? $_POST['extras']   : array()),
            'interior' => array_values(isset($_POST['interior']) && is_array($_POST['interior']) ? $_POST['interior'] : array()),
            'exterior' => array_values(isset($_POST['exterior']) && is_array($_POST['exterior']) ? $_POST['exterior'] : array()),
            'grid'     => isset($_POST['grid']) && is_array($_POST['grid']) ? $_POST['grid'] : array(),
            'txt'      => isset($_POST['txt'])    && is_array($_POST['txt'])    ? $_POST['txt']    : array(),
        );
        // Cases à cocher : présence = vrai
        foreach ($in['extras'] as $i => $e) {
            if (is_array($e)) { $in['extras'][$i]['atelier'] = !empty($e['atelier']); $in['extras'][$i]['needs_exterior'] = !empty($e['needs_exterior']); }
        }
        if (!$in['formulas']) {
            Auth::setFlash('error', 'Il faut au moins une formule.');
            $this->redirect('/' . ADMIN_PATH . '/car-advisor');
        }

        $config = knCaNormalize($in);
        $json   = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->db()->prepare("INSERT INTO kn_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?")
            ->execute(array(self::KEY, $json, $json));

        Auth::setFlash('success', 'Simulateur auto enregistré.');
        $tab = isset($_POST['tab']) ? preg_replace('/[^a-z]/', '', (string) $_POST['tab']) : '';
        $this->redirect('/' . ADMIN_PATH . '/car-advisor' . ($tab ? '#' . $tab : ''));
    }

    /** Premier bloc car-advisor d'une page ou d'un article qui contient encore ses propres réglages. */
    private function findLegacyBlock(\PDO $pdo): ?array
    {
        foreach (array('kn_pages', 'kn_posts') as $table) {
            try {
                $rows = $pdo->query("SELECT content FROM $table WHERE content LIKE '%car-advisor%'")->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Throwable $e) { continue; }
            foreach ($rows as $r) {
                $blocks = json_decode((string) $r['content'], true);
                if (!is_array($blocks)) continue;
                foreach ($blocks as $b) {
                    if (is_array($b) && isset($b['type']) && $b['type'] === 'car-advisor' && (!empty($b['formulas']) || !empty($b['extras']))) return $b;
                }
            }
        }
        return null;
    }

    private function findPreviewUrl(\PDO $pdo): string
    {
        try {
            $st = $pdo->query("SELECT slug FROM kn_pages WHERE status = 'published' AND content LIKE '%\"car-advisor\"%' LIMIT 1");
            $slug = $st ? $st->fetchColumn() : false;
            if ($slug !== false && $slug !== null) return '/' . ltrim((string) $slug, '/');
        } catch (\Throwable $e) {}
        return '';
    }
}
