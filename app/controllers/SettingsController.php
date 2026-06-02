<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class SettingsController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();
        $pdo  = $this->db();
        $stmt = $pdo->query("SELECT `key`, `value` FROM kn_settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/settings/index', [
            'title'      => 'Réglages',
            'settings'   => $settings,
            'csrf_token' => $csrfToken,
        ], 'admin');
    }

    public function update(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/settings');
        }

        $pdo = $this->db();
        $allowed = [
            'site_name', 'site_baseline', 'gtm_id', 'ga4_id',
            'head_custom_code', 'body_custom_code',
            'og_image_default', 'favicon',
            'maintenance_mode', 'maintenance_message', 'robots_global',
            'cookie_banner_enabled', 'cookie_banner_text', 'cookie_policy_url',
            'social_linkedin', 'social_twitter', 'social_facebook', 'social_instagram',
            'booking_url', 'google_api_key', 'google_place_id',
            'logo_url', 'logo_alt',
            'google_reviews_api_key',
        ];

        // Champs checkbox : toujours sauvegardés (0 si absent du POST)
        $checkboxes = ['noindex_all', 'maintenance_mode', 'cookie_banner_enabled'];

        $upsert = $pdo->prepare(
            "INSERT INTO kn_settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?"
        );

        foreach ($checkboxes as $key) {
            $value = isset($_POST[$key]) && $_POST[$key] === '1' ? '1' : '0';
            $upsert->execute([$key, $value, $value]);
        }

        foreach ($allowed as $key) {
            if (in_array($key, $checkboxes, true)) {
                continue;
            }
            $value = isset($_POST[$key]) ? $_POST[$key] : null;
            if ($value !== null) {
                $upsert->execute([$key, $value, $value]);
            }
        }

        Auth::setFlash('success', 'Réglages enregistrés.');
        $this->redirect('/' . ADMIN_PATH . '/settings');
    }
}
