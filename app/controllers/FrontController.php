<?php
declare(strict_types=1);

namespace App\Controllers;

class FrontController extends BaseController
{
    public function home(): void
    {
        $pdo      = $this->db();
        $settings = $this->fetchSettings($pdo);

        $booking_url = isset($settings['booking_url']) && $settings['booking_url']
            ? $settings['booking_url']
            : '#';

        $site_name = isset($settings['site_name']) && $settings['site_name']
            ? $settings['site_name']
            : 'Keepnew';

        $reviews = $this->fetchGoogleReviews($pdo, $settings);

        $this->view->render('home', [
            'title'       => 'Nettoyage à domicile — Canapés, Matelas &amp; Voitures | Keepnew',
            'meta_title'  => 'Nettoyage de canapés, matelas &amp; voitures à domicile | Keepnew',
            'meta_description' => 'Keepnew — Service de nettoyage professionnel à domicile en Belgique. Canapés, matelas, voitures, terrasses. Zone Liège, Namur, Bruxelles, Luxembourg. Devis gratuit.',
            'booking_url' => $booking_url,
            'site_name'   => $site_name,
            'reviews'     => $reviews,
            'gtm_id'      => isset($settings['gtm_id']) ? $settings['gtm_id'] : null,
        ], 'public');
    }

    /**
     * Load all kn_settings key→value pairs into a flat array.
     */
    private function fetchSettings(\PDO $pdo): array
    {
        try {
            $stmt = $pdo->query("SELECT `key`, `value` FROM kn_settings");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $out  = array();
            foreach ($rows as $row) {
                $out[$row['key']] = $row['value'];
            }
            return $out;
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * Fetch Google reviews, using a 24h cache stored in kn_settings.
     * Returns array of up to 3 reviews with rating >= 4.
     */
    private function fetchGoogleReviews(\PDO $pdo, array $settings): array
    {
        $cache    = isset($settings['google_reviews_cache']) ? $settings['google_reviews_cache'] : '';
        $cache_at = isset($settings['google_reviews_cache_at']) ? $settings['google_reviews_cache_at'] : '';
        $api_key  = isset($settings['google_api_key']) ? $settings['google_api_key'] : '';

        // Check whether cache is still valid (< 24h old)
        $cache_valid = false;
        if ($cache && $cache_at) {
            $age = time() - strtotime($cache_at);
            if ($age !== false && $age < 86400) {
                $cache_valid = true;
            }
        }

        if ($cache_valid) {
            $data = json_decode($cache, true);
            if (is_array($data)) {
                return $this->filterReviews($data);
            }
        }

        // No valid cache — try API
        if (!$api_key) {
            return array();
        }

        $place_id = 'ChIJ3eozWDz5wEcR8MOdpMxrwsY';
        $url      = 'https://maps.googleapis.com/maps/api/place/details/json'
                  . '?place_id=' . rawurlencode($place_id)
                  . '&fields=reviews,rating,user_ratings_total'
                  . '&key=' . rawurlencode($api_key)
                  . '&language=fr';

        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 5,
                'method'  => 'GET',
            ),
        ));

        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            return array();
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded) || !isset($decoded['result']['reviews'])) {
            return array();
        }

        $raw_reviews = $decoded['result']['reviews'];

        // Persist to cache
        try {
            $json_cache = json_encode($raw_reviews);
            $now        = date('Y-m-d H:i:s');
            $this->upsertSetting($pdo, 'google_reviews_cache', $json_cache);
            $this->upsertSetting($pdo, 'google_reviews_cache_at', $now);
        } catch (\Exception $e) {
            // Non-fatal — continue without caching
        }

        return $this->filterReviews($raw_reviews);
    }

    /**
     * Filter reviews: rating >= 4, max 3, most recent first.
     */
    private function filterReviews(array $reviews): array
    {
        $filtered = array();
        foreach ($reviews as $r) {
            if (isset($r['rating']) && (int)$r['rating'] >= 4) {
                $filtered[] = $r;
            }
        }
        // Sort by time descending (Google already does this, but be safe)
        usort($filtered, function ($a, $b) {
            $ta = isset($a['time']) ? (int)$a['time'] : 0;
            $tb = isset($b['time']) ? (int)$b['time'] : 0;
            return $tb - $ta;
        });
        return array_slice($filtered, 0, 3);
    }

    /**
     * INSERT or UPDATE a kn_settings row.
     */
    private function upsertSetting(\PDO $pdo, string $key, string $value): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO kn_settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
        );
        $stmt->execute(array($key, $value));
    }

    public function blogList(): void
    {
        $this->view->render('blog-list', ['title' => 'Blog']);
    }

    public function blogPost(string $slug): void
    {
        $this->view->render('blog-post', ['title' => $slug, 'slug' => $slug]);
    }

    public function portfolioList(): void
    {
        $this->view->render('portfolio', ['title' => 'Portfolio']);
    }

    public function portfolioItem(string $slug): void
    {
        $this->view->render('portfolio', ['title' => $slug, 'slug' => $slug]);
    }

    public function page(string $slug): void
    {
        $this->view->render('page', ['title' => $slug, 'slug' => $slug]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view->render('page', ['title' => 'Page introuvable']);
    }
}
