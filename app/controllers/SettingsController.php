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

        // Si le Place ID ou la clé API change, le cache d'avis en place (jusqu'à
        // 24h) correspond encore à l'ancienne fiche : on le purge pour que le
        // changement soit visible immédiatement plutôt que dans jusqu'à 24h.
        $reviewKeys = ['google_place_id', 'google_reviews_api_key'];
        $stmt = $pdo->prepare("SELECT `value` FROM kn_settings WHERE `key` = ? LIMIT 1");
        $reviewSettingChanged = false;
        foreach ($reviewKeys as $key) {
            if (!isset($_POST[$key])) continue;
            $stmt->execute([$key]);
            $current = $stmt->fetchColumn();
            if ($current === false) $current = '';
            if (trim((string) $_POST[$key]) !== trim((string) $current)) {
                $reviewSettingChanged = true;
                break;
            }
        }
        if ($reviewSettingChanged) {
            $pdo->exec("DELETE FROM kn_settings WHERE `key` IN ('google_reviews_cache', 'google_reviews_cache_at')");
        }

        $allowed = [
            'site_name', 'site_baseline', 'gtm_id', 'ga4_id',
            'head_custom_code', 'body_custom_code',
            'og_image_default', 'favicon',
            'maintenance_mode', 'maintenance_message', 'robots_global',
            'cookie_banner_enabled', 'cookie_banner_text', 'cookie_policy_url',
            'social_linkedin', 'social_twitter', 'social_facebook', 'social_instagram',
            'booking_url', 'google_place_id',
            'logo_url', 'logo_light_url', 'logo_rose_url', 'logo_alt',
            'google_reviews_api_key',
            'cache_enabled',
            'footer_tagline', 'footer_phone', 'footer_legal_text',
            'sticky_cta_enabled', 'sticky_cta_threshold',
            'sticky_cta_phone_label', 'sticky_cta_phone_number', 'sticky_cta_phone_icon',
            'sticky_cta_book_label', 'sticky_cta_book_url', 'sticky_cta_book_icon',
        ];

        $checkboxes = ['noindex_all', 'maintenance_mode', 'cookie_banner_enabled', 'cache_enabled', 'sticky_cta_enabled'];

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

        Auth::setFlash('success', $reviewSettingChanged
            ? 'Réglages enregistrés. Le cache des avis Google a été vidé suite au changement de Place ID / clé API : les nouveaux avis apparaîtront dès le prochain chargement de la page.'
            : 'Réglages enregistrés.');
        $this->redirect('/' . ADMIN_PATH . '/settings');
    }

    public function clearCache(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/settings');
        }
        $pdo = $this->db();
        $version = (string) time();
        $pdo->prepare("INSERT INTO kn_settings (`key`, `value`) VALUES ('cache_version', ?) ON DUPLICATE KEY UPDATE `value` = ?")
            ->execute([$version, $version]);
        Auth::setFlash('success', 'Cache vidé — les navigateurs re-téléchargeront les fichiers.');
        $this->redirect('/' . ADMIN_PATH . '/settings');
    }

    /**
     * Teste la connexion à l'API Google Places en direct, avec les valeurs
     * du formulaire (pas forcément encore enregistrées) — pour valider une
     * clé API et un Place ID sans attendre l'expiration du cache 24h.
     */
    public function testGoogleReviews(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            $this->jsonResponse(['ok' => false, 'message' => 'Token CSRF invalide.'], 403);
        }

        $api_key  = trim(isset($_POST['api_key']) ? $_POST['api_key'] : '');
        $place_id = trim(isset($_POST['place_id']) ? $_POST['place_id'] : '');

        if ($api_key === '' || $place_id === '') {
            $this->jsonResponse(['ok' => false, 'message' => 'Renseignez la clé API et le Place ID avant de tester.']);
        }

        $url = 'https://maps.googleapis.com/maps/api/place/details/json'
             . '?place_id=' . rawurlencode($place_id)
             . '&fields=reviews,rating,user_ratings_total,name'
             . '&reviews_sort=newest'
             . '&key=' . rawurlencode($api_key)
             . '&language=fr';

        $ctx = stream_context_create([
            'http' => ['timeout' => 8, 'method' => 'GET', 'ignore_errors' => true],
        ]);
        $response = @file_get_contents($url, false, $ctx);

        if ($response === false) {
            $this->jsonResponse(['ok' => false, 'message' => 'Connexion à l\'API Google impossible (réseau ou délai dépassé).']);
        }

        $decoded = json_decode($response, true);
        $status  = is_array($decoded) && isset($decoded['status']) ? $decoded['status'] : 'UNKNOWN';

        if ($status !== 'OK') {
            $hints = [
                'REQUEST_DENIED'   => 'Clé refusée : vérifiez qu\'elle est valide, que l\'API « Places API » (legacy, pas « Places API (New) ») est activée sur ce projet Google Cloud, et qu\'un moyen de facturation y est associé.',
                'INVALID_REQUEST'  => 'Requête invalide : le Place ID semble mal formé.',
                'NOT_FOUND'        => 'Aucun établissement trouvé pour ce Place ID.',
                'OVER_QUERY_LIMIT' => 'Quota de requêtes dépassé pour cette clé API.',
            ];
            $message = isset($decoded['error_message']) && $decoded['error_message']
                ? $decoded['error_message']
                : ($hints[$status] ?? ('Statut inattendu renvoyé par Google : ' . $status));
            $this->jsonResponse(['ok' => false, 'status' => $status, 'message' => $message]);
        }

        $result = isset($decoded['result']) && is_array($decoded['result']) ? $decoded['result'] : [];
        $this->jsonResponse([
            'ok'           => true,
            'status'       => 'OK',
            'name'         => isset($result['name']) ? $result['name'] : '',
            'rating'       => isset($result['rating']) ? $result['rating'] : null,
            'total'        => isset($result['user_ratings_total']) ? $result['user_ratings_total'] : null,
            'review_count' => isset($result['reviews']) && is_array($result['reviews']) ? count($result['reviews']) : 0,
        ]);
    }
}
