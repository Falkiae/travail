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

        $blocks = array();
        try {
            $stmt = $pdo->prepare("SELECT content FROM kn_pages WHERE slug = 'home' AND lang = 'fr' LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row && isset($row['content']) && $row['content']) {
                $decoded = json_decode($row['content'], true);
                if (is_array($decoded)) {
                    $blocks = $decoded;
                }
            }
        } catch (\Exception $e) {
            $blocks = array();
        }

        $block_types = ['hero','services','two-col','how','reviews','zone','accordion','cta-final','heading','text','image','cta','html','video','file','quote','pricing','before-after','logos','seo-content','domicile-vs-atelier','sofa-simulator','prestation'];
        $block_styles = '';
        foreach ($block_types as $bt) {
            $css_key = 'block_css_' . $bt;
            if (!empty($settings[$css_key])) {
                $block_styles .= '/* ' . $bt . " */\n" . $settings[$css_key] . "\n";
            }
        }

        $lang      = isset($settings['lang']) ? $settings['lang'] : 'fr';
        $nav_items = $this->fetchNavItems($pdo, $lang);

        $this->view->render('home', [
            'title'       => 'Nettoyage à domicile — Canapés, Matelas &amp; Voitures | Keepnew',
            'meta_title'  => 'Nettoyage de canapés, matelas &amp; voitures à domicile | Keepnew',
            'meta_description' => 'Keepnew — Service de nettoyage professionnel à domicile en Belgique. Canapés, matelas, voitures, terrasses. Zone Liège, Namur, Bruxelles, Luxembourg. Devis gratuit.',
            'booking_url' => $booking_url,
            'site_name'   => $site_name,
            'logo_url'       => isset($settings['logo_url']) ? $settings['logo_url'] : '',
            'logo_light_url' => isset($settings['logo_light_url']) ? $settings['logo_light_url'] : '',
            'logo_rose_url'  => isset($settings['logo_rose_url'])  ? $settings['logo_rose_url']  : '',
            'logo_alt'       => isset($settings['logo_alt']) ? $settings['logo_alt'] : $site_name,
            'reviews'     => $reviews,
            'blocks'      => $blocks,
            'nav_items'   => $nav_items,
            'gtm_id'       => isset($settings['gtm_id']) ? $settings['gtm_id'] : null,
            'noindex_all'  => isset($settings['noindex_all']) && $settings['noindex_all'] === '1',
            'robots_global' => isset($settings['robots_global']) ? $settings['robots_global'] : 'index,follow',
            'block_styles'  => $block_styles,
            'cache_version' => (isset($settings['cache_enabled']) && $settings['cache_enabled'] === '1' && isset($settings['cache_version'])) ? $settings['cache_version'] : '',
            'footer_menus'      => $this->fetchFooterMenus($pdo, $lang),
            'footer_tagline'    => isset($settings['footer_tagline']) ? $settings['footer_tagline'] : '',
            'footer_phone'      => isset($settings['footer_phone']) ? $settings['footer_phone'] : '',
            'footer_legal_text' => isset($settings['footer_legal_text']) ? $settings['footer_legal_text'] : '',
            'sticky_cta_enabled'      => ($settings['sticky_cta_enabled'] ?? '1') === '1',
            'sticky_cta_threshold'    => isset($settings['sticky_cta_threshold']) && $settings['sticky_cta_threshold'] !== '' ? (float) $settings['sticky_cta_threshold'] : 40,
            'sticky_cta_phone_label'  => isset($settings['sticky_cta_phone_label']) ? $settings['sticky_cta_phone_label'] : '',
            'sticky_cta_phone_number' => isset($settings['sticky_cta_phone_number']) && $settings['sticky_cta_phone_number'] !== '' ? $settings['sticky_cta_phone_number'] : (isset($settings['footer_phone']) ? $settings['footer_phone'] : ''),
            'sticky_cta_phone_icon'   => isset($settings['sticky_cta_phone_icon']) && $settings['sticky_cta_phone_icon'] !== '' ? $settings['sticky_cta_phone_icon'] : 'phone',
            'sticky_cta_book_label'   => isset($settings['sticky_cta_book_label']) && $settings['sticky_cta_book_label'] !== '' ? $settings['sticky_cta_book_label'] : 'Réserver un créneau',
            'sticky_cta_book_url'     => isset($settings['sticky_cta_book_url']) && $settings['sticky_cta_book_url'] !== '' ? $settings['sticky_cta_book_url'] : (isset($settings['booking_url']) && $settings['booking_url'] ? $settings['booking_url'] : '#'),
            'sticky_cta_book_icon'    => isset($settings['sticky_cta_book_icon']) && $settings['sticky_cta_book_icon'] !== '' ? $settings['sticky_cta_book_icon'] : 'calendar',
            'cookie_banner_enabled' => ($settings['cookie_banner_enabled'] ?? '1') === '1',
            'cookie_banner_text'    => $settings['cookie_banner_text'] ?? 'Nous utilisons des cookies pour améliorer votre expérience. Vous pouvez accepter ou refuser les cookies analytiques et publicitaires.',
            'cookie_policy_url'     => $settings['cookie_policy_url'] ?? '/politique-cookies',
        ], 'public');
    }

    private function fetchFooterMenus(\PDO $pdo, string $lang = 'fr'): array
    {
        try {
            $stmt = $pdo->prepare("SELECT name, items FROM kn_menus WHERE location = 'footer' AND lang = ? ORDER BY id");
            $stmt->execute([$lang]);
            $out = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $items = json_decode($row['items'] ?? '[]', true);
                $out[] = [
                    'name'  => $row['name'],
                    'items' => is_array($items) ? $items : [],
                ];
            }
            return $out;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function slugifyMenuName(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        if (function_exists('transliterator_transliterate')) {
            $name = transliterator_transliterate('Any-Latin; Latin-ASCII', $name);
        }
        $name = preg_replace('/[^a-z0-9]+/', '-', $name);
        return trim($name, '-');
    }

    /**
     * Fetch nav items from kn_menus for the given location and lang.
     */
    private function fetchNavItems(\PDO $pdo, string $lang = 'fr'): array
    {
        try {
            $stmt = $pdo->prepare(
                "SELECT items FROM kn_menus WHERE location = 'header' AND lang = ? LIMIT 1"
            );
            $stmt->execute(array($lang));
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$row || empty($row['items'])) {
                return array();
            }
            $decoded = json_decode($row['items'], true);
            return is_array($decoded) ? $decoded : array();
        } catch (\Exception $e) {
            return array();
        }
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
    /**
     * Renvoie ['items' => [...avis...], 'rating' => 4.9, 'total' => 110].
     *
     * Limite dure de l'API Place Details (legacy) : Google ne renvoie
     * jamais plus de 5 avis par appel, quel que soit le tri demandé —
     * ce n'est pas un choix de ce code, impossible d'en obtenir plus par
     * cette voie. « rating » et « total » en revanche portent sur
     * l'intégralité des avis de la fiche, pas seulement les 5 renvoyés.
     */
    /** Nombre d'avis conservés dans l'archive glissante (voir plus bas). */
    private const REVIEWS_ARCHIVE_SIZE = 15;

    /**
     * Renvoie ['items' => [...avis...], 'rating' => 4.9, 'total' => 110].
     *
     * Limite dure de l'API Place Details (legacy) : Google ne renvoie
     * jamais plus de 5 avis par appel, quel que soit le tri demandé — ce
     * n'est pas un choix de ce code. Pour afficher un historique plus
     * large sans passer par l'API payante Business Profile (OAuth), on
     * cumule une archive glissante : chaque rafraîchissement (au plus une
     * fois par 24h grâce au cache) ajoute les avis pas encore vus à un
     * historique conservé en base, jusqu'à REVIEWS_ARCHIVE_SIZE. Cette
     * archive grossit donc progressivement (5 au départ, jusqu'à 15 au
     * fil des semaines si de nouveaux avis arrivent), elle n'affiche pas
     * 15 avis dès le premier chargement.
     */
    private function fetchGoogleReviews(\PDO $pdo, array $settings): array
    {
        $empty = array('items' => array(), 'rating' => null, 'total' => null);

        $cache    = isset($settings['google_reviews_cache']) ? $settings['google_reviews_cache'] : '';
        $cache_at = isset($settings['google_reviews_cache_at']) ? $settings['google_reviews_cache_at'] : '';
        $archive  = isset($settings['google_reviews_archive']) ? $settings['google_reviews_archive'] : '';
        // Le formulaire des réglages enregistre la clé sous google_reviews_api_key —
        // c'était lu ici sous google_api_key, une clé qu'aucun champ n'a jamais
        // écrite : l'appel API ne s'est donc jamais déclenché, quelle que soit la
        // valeur saisie dans l'admin.
        $api_key  = isset($settings['google_reviews_api_key']) ? trim($settings['google_reviews_api_key']) : '';
        $place_id = isset($settings['google_place_id']) ? trim($settings['google_place_id']) : '';

        $archiveItems = array();
        $decodedArchive = json_decode($archive, true);
        if (is_array($decodedArchive)) $archiveItems = $decodedArchive;

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
            if (is_array($data) && isset($data['items'])) {
                return array(
                    'items'  => $this->filterReviews($archiveItems ?: $data['items']),
                    'rating' => isset($data['rating']) ? $data['rating'] : null,
                    'total'  => isset($data['total']) ? $data['total'] : null,
                );
            }
        }

        // Pas de cache valide : on retente l'API, mais seulement si les deux
        // réglages sont réellement configurés.
        if ($api_key === '' || $place_id === '') {
            return $empty;
        }

        $url = 'https://maps.googleapis.com/maps/api/place/details/json'
             . '?place_id=' . rawurlencode($place_id)
             . '&fields=reviews,rating,user_ratings_total'
             . '&reviews_sort=newest'
             . '&key=' . rawurlencode($api_key)
             . '&language=fr';

        $ctx = stream_context_create(array(
            'http' => array(
                'timeout'       => 5,
                'method'        => 'GET',
                'ignore_errors' => true, // pour lire le corps même sur 4xx
            ),
        ));

        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            error_log('[Keepnew] Google Reviews : échec de connexion à l\'API Google Places.');
            // On sert l'archive existante plutôt que de tout vider sur un
            // simple accroc réseau.
            return array('items' => $this->filterReviews($archiveItems), 'rating' => null, 'total' => null);
        }

        $decoded = json_decode($response, true);
        $status  = isset($decoded['status']) ? $decoded['status'] : 'UNKNOWN';

        if (!is_array($decoded) || $status !== 'OK' || !isset($decoded['result'])) {
            // Toujours renvoyé silencieusement au visiteur (le bloc affiche
            // simplement son message de repli), mais consigné pour pouvoir
            // diagnostiquer une clé invalide, une API non activée, un Place
            // ID incorrect ou un quota dépassé.
            $message = isset($decoded['error_message']) ? $decoded['error_message'] : '';
            error_log('[Keepnew] Google Reviews : réponse API status=' . $status . ($message !== '' ? ' — ' . $message : ''));
            return array('items' => $this->filterReviews($archiveItems), 'rating' => null, 'total' => null);
        }

        $result   = $decoded['result'];
        $freshRaw = isset($result['reviews']) && is_array($result['reviews']) ? $result['reviews'] : array();
        $rating   = isset($result['rating']) ? (float) $result['rating'] : null;
        $total    = isset($result['user_ratings_total']) ? (int) $result['user_ratings_total'] : null;

        $mergedArchive = $this->mergeReviewsArchive($archiveItems, $freshRaw);

        // Persist to cache
        try {
            $now = date('Y-m-d H:i:s');
            $this->upsertSetting($pdo, 'google_reviews_cache', json_encode(array(
                'items'  => $freshRaw,
                'rating' => $rating,
                'total'  => $total,
            )));
            $this->upsertSetting($pdo, 'google_reviews_cache_at', $now);
            $this->upsertSetting($pdo, 'google_reviews_archive', json_encode($mergedArchive));
        } catch (\Exception $e) {
            // Non-fatal — continue without caching
        }

        return array(
            'items'  => $this->filterReviews($mergedArchive),
            'rating' => $rating,
            'total'  => $total,
        );
    }

    /**
     * Fusionne un lot d'avis fraîchement récupéré dans l'archive existante :
     * dédoublonne (auteur + horodatage — un même avis renvoyé deux jours de
     * suite ne doit pas apparaître deux fois), trie du plus récent au plus
     * ancien, et conserve au maximum REVIEWS_ARCHIVE_SIZE entrées.
     */
    private function mergeReviewsArchive(array $existing, array $fresh): array
    {
        $byKey = array();
        foreach (array_merge($existing, $fresh) as $r) {
            if (!isset($r['author_name']) || !isset($r['time'])) continue;
            $key = $r['author_name'] . '|' . $r['time'];
            $byKey[$key] = $r; // la version la plus récemment vue écrase l'ancienne
        }
        $merged = array_values($byKey);
        usort($merged, function ($a, $b) {
            return (int) $b['time'] - (int) $a['time'];
        });
        return array_slice($merged, 0, self::REVIEWS_ARCHIVE_SIZE);
    }

    /**
     * Filtre les avis (note >= 4, les plus récents en premier, jusqu'à
     * REVIEWS_ARCHIVE_SIZE). L'archive fait déjà ce tri/cette limite ; cette
     * méthode reste le filet de sécurité pour toute source d'avis passée
     * directement (cache legacy, réponse API brute non archivée…).
     */
    private function filterReviews(array $reviews): array
    {
        $filtered = array();
        foreach ($reviews as $r) {
            if (isset($r['rating']) && (int)$r['rating'] >= 4) {
                $filtered[] = $r;
            }
        }
        usort($filtered, function ($a, $b) {
            $ta = isset($a['time']) ? (int)$a['time'] : 0;
            $tb = isset($b['time']) ? (int)$b['time'] : 0;
            return $tb - $ta;
        });
        return array_slice($filtered, 0, self::REVIEWS_ARCHIVE_SIZE);
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

    /**
     * Convertit un chemin relatif (/uploads/x.jpg) en URL absolue. Laisse
     * intactes les URLs déjà absolues. Requis par les données structurées,
     * que les moteurs ne doivent jamais recevoir en chemin relatif.
     */
    private function absUrl(string $path): string
    {
        if ($path === '') return '';
        if (preg_match('#^https?://#i', $path)) return $path;
        return $this->baseUrl() . '/' . ltrim($path, '/');
    }

    /**
     * Variables communes à toutes les pages publiques (nav, footer, logo…),
     * pour éviter de les répéter dans chaque action qui rend le layout public.
     */
    private function commonPublicData(\PDO $pdo, array $settings, string $lang): array
    {
        $site_name = isset($settings['site_name']) && $settings['site_name'] ? $settings['site_name'] : 'Keepnew';
        return array(
            'booking_url'    => isset($settings['booking_url']) && $settings['booking_url'] ? $settings['booking_url'] : '#',
            'site_name'      => $site_name,
            'logo_url'       => isset($settings['logo_url']) ? $settings['logo_url'] : '',
            'logo_light_url' => isset($settings['logo_light_url']) ? $settings['logo_light_url'] : '',
            'logo_rose_url'  => isset($settings['logo_rose_url'])  ? $settings['logo_rose_url']  : '',
            'logo_alt'       => isset($settings['logo_alt']) ? $settings['logo_alt'] : $site_name,
            'nav_items'      => $this->fetchNavItems($pdo, $lang),
            'gtm_id'         => isset($settings['gtm_id']) ? $settings['gtm_id'] : null,
            'noindex_all'    => isset($settings['noindex_all']) && $settings['noindex_all'] === '1',
            'robots_global'  => isset($settings['robots_global']) ? $settings['robots_global'] : 'index,follow',
            'cache_version'  => (isset($settings['cache_enabled']) && $settings['cache_enabled'] === '1' && isset($settings['cache_version'])) ? $settings['cache_version'] : '',
            'footer_menus'      => $this->fetchFooterMenus($pdo, $lang),
            'footer_tagline'    => isset($settings['footer_tagline']) ? $settings['footer_tagline'] : '',
            'footer_phone'      => isset($settings['footer_phone']) ? $settings['footer_phone'] : '',
            'footer_legal_text' => isset($settings['footer_legal_text']) ? $settings['footer_legal_text'] : '',
            'sticky_cta_enabled'      => ($settings['sticky_cta_enabled'] ?? '1') === '1',
            'sticky_cta_threshold'    => isset($settings['sticky_cta_threshold']) && $settings['sticky_cta_threshold'] !== '' ? (float) $settings['sticky_cta_threshold'] : 40,
            'sticky_cta_phone_label'  => isset($settings['sticky_cta_phone_label']) ? $settings['sticky_cta_phone_label'] : '',
            'sticky_cta_phone_number' => isset($settings['sticky_cta_phone_number']) && $settings['sticky_cta_phone_number'] !== '' ? $settings['sticky_cta_phone_number'] : (isset($settings['footer_phone']) ? $settings['footer_phone'] : ''),
            'sticky_cta_phone_icon'   => isset($settings['sticky_cta_phone_icon']) && $settings['sticky_cta_phone_icon'] !== '' ? $settings['sticky_cta_phone_icon'] : 'phone',
            'sticky_cta_book_label'   => isset($settings['sticky_cta_book_label']) && $settings['sticky_cta_book_label'] !== '' ? $settings['sticky_cta_book_label'] : 'Réserver un créneau',
            'sticky_cta_book_url'     => isset($settings['sticky_cta_book_url']) && $settings['sticky_cta_book_url'] !== '' ? $settings['sticky_cta_book_url'] : (isset($settings['booking_url']) && $settings['booking_url'] ? $settings['booking_url'] : '#'),
            'sticky_cta_book_icon'    => isset($settings['sticky_cta_book_icon']) && $settings['sticky_cta_book_icon'] !== '' ? $settings['sticky_cta_book_icon'] : 'calendar',
            'cookie_banner_enabled' => ($settings['cookie_banner_enabled'] ?? '1') === '1',
            'cookie_banner_text'    => $settings['cookie_banner_text'] ?? 'Nous utilisons des cookies pour améliorer votre expérience. Vous pouvez accepter ou refuser les cookies analytiques et publicitaires.',
            'cookie_policy_url'     => $settings['cookie_policy_url'] ?? '/politique-cookies',
        );
    }

    /**
     * Ajoute l'URL de la vignette (webp de préférence) et le nom de la
     * catégorie à une liste d'articles issue de kn_posts.
     */
    private function hydratePosts(\PDO $pdo, array $posts): array
    {
        if (!$posts) return $posts;

        $mediaIds = array_values(array_unique(array_filter(array_map(function ($p) {
            return isset($p['featured_image']) ? (int) $p['featured_image'] : 0;
        }, $posts))));
        $media = array();
        if ($mediaIds) {
            $in   = implode(',', array_fill(0, count($mediaIds), '?'));
            $stmt = $pdo->prepare("SELECT id, path, webp_path FROM kn_media WHERE id IN ($in)");
            $stmt->execute($mediaIds);
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $media[(int) $row['id']] = $row;
            }
        }

        $catIds = array_values(array_unique(array_filter(array_map(function ($p) {
            return isset($p['category_id']) ? (int) $p['category_id'] : 0;
        }, $posts))));
        $cats = array();
        if ($catIds) {
            $in   = implode(',', array_fill(0, count($catIds), '?'));
            $stmt = $pdo->prepare("SELECT id, name, slug FROM kn_categories WHERE id IN ($in)");
            $stmt->execute($catIds);
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $cats[(int) $row['id']] = $row;
            }
        }

        foreach ($posts as &$p) {
            $imgId = isset($p['featured_image']) ? (int) $p['featured_image'] : 0;
            $p['image_url'] = ($imgId && isset($media[$imgId]))
                ? (!empty($media[$imgId]['webp_path']) ? $media[$imgId]['webp_path'] : $media[$imgId]['path'])
                : '';
            $catId = isset($p['category_id']) ? (int) $p['category_id'] : 0;
            $p['category_name'] = ($catId && isset($cats[$catId])) ? $cats[$catId]['name'] : '';
            $p['category_slug'] = ($catId && isset($cats[$catId])) ? $cats[$catId]['slug'] : '';
        }
        unset($p);

        return $posts;
    }

    public function blogList(): void
    {
        $pdo      = $this->db();
        $settings = $this->fetchSettings($pdo);
        $lang     = isset($settings['lang']) ? $settings['lang'] : 'fr';

        $perPage = 9;
        $page    = max(1, (int) (isset($_GET['page']) ? $_GET['page'] : 1));
        $catSlug = trim((string) (isset($_GET['cat']) ? $_GET['cat'] : ''));

        $where  = 'status = ? AND lang = ?';
        $params = array('published', $lang);

        $activeCategory = null;
        if ($catSlug !== '') {
            $stmt = $pdo->prepare('SELECT id, name, slug FROM kn_categories WHERE slug = ? AND lang = ? LIMIT 1');
            $stmt->execute(array($catSlug, $lang));
            $activeCategory = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            if ($activeCategory) {
                $where   .= ' AND category_id = ?';
                $params[] = (int) $activeCategory['id'];
            } else {
                // Catégorie inconnue : aucun résultat plutôt qu'une liste non filtrée trompeuse
                $where   .= ' AND 1 = 0';
            }
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM kn_posts WHERE $where");
        $countStmt->execute($params);
        $total      = (int) $countStmt->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $stmt = $pdo->prepare(
            "SELECT * FROM kn_posts WHERE $where ORDER BY COALESCE(published_at, created_at) DESC LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        $posts = $this->hydratePosts($pdo, $stmt->fetchAll(\PDO::FETCH_ASSOC));

        $catStmt = $pdo->prepare('SELECT id, name, slug FROM kn_categories WHERE lang = ? ORDER BY name');
        $catStmt->execute(array($lang));
        $categories = $catStmt->fetchAll(\PDO::FETCH_ASSOC);

        $metaTitle = $activeCategory
            ? $activeCategory['name'] . ' — Blog | ' . (isset($settings['site_name']) && $settings['site_name'] ? $settings['site_name'] : 'Keepnew')
            : 'Blog — Conseils d\'entretien';
        $canonicalUrl = $this->baseUrl() . '/blog' . ($activeCategory ? '?cat=' . urlencode($activeCategory['slug']) : '');

        $breadcrumb = array(
            array('@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => $this->baseUrl() . '/'),
            array('@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $this->baseUrl() . '/blog'),
        );
        if ($activeCategory) {
            $breadcrumb[] = array('@type' => 'ListItem', 'position' => 3, 'name' => $activeCategory['name'], 'item' => $canonicalUrl);
        }
        $structuredData = array(
            array('@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumb),
        );

        $data = array_merge($this->commonPublicData($pdo, $settings, $lang), array(
            'title'             => $metaTitle,
            'meta_title'        => $metaTitle,
            'meta_description'  => $activeCategory
                ? 'Tous les articles Keepnew de la catégorie ' . $activeCategory['name'] . ' — conseils d\'entretien, actualités et coulisses de l\'atelier.'
                : 'Conseils, astuces et actualités Keepnew pour l\'entretien de vos canapés, matelas et véhicules.',
            'canonical_url'     => $canonicalUrl,
            'structured_data'   => $structuredData,
            'posts'             => $posts,
            'categories'        => $categories,
            'active_category'   => $activeCategory,
            'page'              => $page,
            'total_pages'       => $totalPages,
            'block_styles'      => '',
        ));
        $this->view->render('blog-list', $data);
    }

    public function blogPost(string $slug): void
    {
        $pdo      = $this->db();
        $settings = $this->fetchSettings($pdo);
        $lang     = isset($settings['lang']) ? $settings['lang'] : 'fr';

        $stmt = $pdo->prepare('SELECT * FROM kn_posts WHERE slug = ? AND lang = ? LIMIT 1');
        $stmt->execute(array($slug, $lang));
        $post = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$post || $post['status'] !== 'published') {
            http_response_code(404);
            $data = array_merge($this->commonPublicData($pdo, $settings, $lang), array(
                'title' => 'Article introuvable',
            ));
            $this->view->render('blog-not-found', $data);
            return;
        }

        $posts = $this->hydratePosts($pdo, array($post));
        $post  = $posts[0];

        $blocks = array();
        if (!empty($post['content'])) {
            $decoded = json_decode($post['content'], true);
            if (is_array($decoded)) $blocks = $decoded;
        }

        // Articles liés : même catégorie de préférence, sinon les plus récents
        $related = array();
        if (!empty($post['category_id'])) {
            $stmt = $pdo->prepare(
                "SELECT * FROM kn_posts WHERE status = 'published' AND lang = ? AND category_id = ? AND id != ?
                 ORDER BY COALESCE(published_at, created_at) DESC LIMIT 3"
            );
            $stmt->execute(array($lang, (int) $post['category_id'], (int) $post['id']));
            $related = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        if (count($related) < 3) {
            $stmt = $pdo->prepare(
                "SELECT * FROM kn_posts WHERE status = 'published' AND lang = ? AND id != ?
                 ORDER BY COALESCE(published_at, created_at) DESC LIMIT ?"
            );
            $need = 3 - count($related);
            $excludeIds = array_merge(array((int) $post['id']), array_column($related, 'id'));
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $stmt = $pdo->prepare(
                "SELECT * FROM kn_posts WHERE status = 'published' AND lang = ? AND id NOT IN ($placeholders)
                 ORDER BY COALESCE(published_at, created_at) DESC LIMIT $need"
            );
            $stmt->execute(array_merge(array($lang), $excludeIds));
            $related = array_merge($related, $stmt->fetchAll(\PDO::FETCH_ASSOC));
        }
        $related = $this->hydratePosts($pdo, $related);

        // Temps de lecture estimé (~200 mots/min) à partir du texte des blocs
        $wordCount = 0;
        array_walk_recursive($blocks, function ($v) use (&$wordCount) {
            if (is_string($v)) $wordCount += str_word_count(strip_tags($v));
        });
        $readingMinutes = max(1, (int) ceil($wordCount / 200));

        $siteName     = isset($settings['site_name']) && $settings['site_name'] ? $settings['site_name'] : 'Keepnew';
        $canonicalUrl = $this->baseUrl() . '/blog/' . $slug;
        $ogImageUrl   = isset($post['og_image']) && $post['og_image'] ? $post['og_image'] : $post['image_url'];
        $ogImageAbs   = $ogImageUrl ? $this->absUrl($ogImageUrl) : '';
        $logoAbs      = !empty($settings['logo_url']) ? $this->absUrl($settings['logo_url']) : '';
        $publishedIso = date('c', strtotime(!empty($post['published_at']) ? $post['published_at'] : $post['created_at']));
        $modifiedIso  = date('c', strtotime(!empty($post['updated_at']) ? $post['updated_at'] : $publishedIso));
        $metaDesc     = isset($post['meta_description']) && $post['meta_description'] ? $post['meta_description'] : $post['excerpt'];

        // BlogPosting : les moteurs classiques comme les moteurs génératifs
        // (GEO) s'appuient sur ces champs pour citer la page avec ses faits
        // exacts — auteur, dates, temps de lecture, catégorie.
        $blogPosting = array(
            '@context'         => 'https://schema.org',
            '@type'            => 'BlogPosting',
            'headline'         => $post['title'],
            'description'      => $metaDesc,
            'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => $canonicalUrl),
            'url'              => $canonicalUrl,
            'datePublished'    => $publishedIso,
            'dateModified'     => $modifiedIso,
            'inLanguage'       => $lang === 'nl' ? 'nl-BE' : 'fr-BE',
            'author'           => array('@type' => 'Organization', 'name' => $siteName, 'url' => $this->baseUrl() . '/'),
            'publisher'        => array_filter(array(
                '@type' => 'Organization',
                'name'  => $siteName,
                'logo'  => $logoAbs ? array('@type' => 'ImageObject', 'url' => $logoAbs) : null,
            )),
            'wordCount'        => $wordCount,
            'timeRequired'     => 'PT' . $readingMinutes . 'M',
        );
        if ($ogImageAbs) $blogPosting['image'] = $ogImageAbs;
        if (!empty($post['category_name'])) $blogPosting['articleSection'] = $post['category_name'];

        $breadcrumb = array(
            array('@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => $this->baseUrl() . '/'),
            array('@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $this->baseUrl() . '/blog'),
        );
        $pos = 3;
        if (!empty($post['category_name'])) {
            $breadcrumb[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => $post['category_name'], 'item' => $this->baseUrl() . '/blog?cat=' . urlencode($post['category_slug']));
        }
        $breadcrumb[] = array('@type' => 'ListItem', 'position' => $pos, 'name' => $post['title'], 'item' => $canonicalUrl);

        $structuredData = array(
            $blogPosting,
            array('@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumb),
        );

        $data = array_merge($this->commonPublicData($pdo, $settings, $lang), array(
            'title'             => isset($post['meta_title']) && $post['meta_title'] ? $post['meta_title'] : $post['title'],
            'meta_title'        => isset($post['meta_title']) ? $post['meta_title'] : $post['title'],
            'meta_description'  => $metaDesc,
            'og_image'          => $ogImageAbs,
            'og_type'           => 'article',
            'canonical_url'     => $canonicalUrl,
            'article_published_time' => $publishedIso,
            'article_modified_time'  => $modifiedIso,
            'article_section'        => isset($post['category_name']) ? $post['category_name'] : '',
            'structured_data'   => $structuredData,
            'slug'              => $slug,
            'post'              => $post,
            'blocks'            => $blocks,
            'related_posts'     => $related,
            'reading_minutes'   => $readingMinutes,
            'block_styles'      => '',
        ));
        $this->view->render('blog-post', $data);
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
        $pdo      = $this->db();
        $settings = $this->fetchSettings($pdo);

        $booking_url = isset($settings['booking_url']) && $settings['booking_url']
            ? $settings['booking_url']
            : '#';

        $site_name = isset($settings['site_name']) && $settings['site_name']
            ? $settings['site_name']
            : 'Keepnew';

        $lang      = isset($settings['lang']) ? $settings['lang'] : 'fr';
        $nav_items = $this->fetchNavItems($pdo, $lang);

        $blocks = array();
        $page_row = null;
        $template = 'page';
        try {
            $stmt = $pdo->prepare("SELECT * FROM kn_pages WHERE slug = ? AND lang = ? LIMIT 1");
            $stmt->execute([$slug, $lang]);
            $page_row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($page_row && isset($page_row['content']) && $page_row['content']) {
                $decoded = json_decode($page_row['content'], true);
                if (is_array($decoded)) {
                    $blocks = $decoded;
                }
            }
            if ($page_row && isset($page_row['template']) && $page_row['template']) {
                $template = $page_row['template'];
            }
        } catch (\Exception $e) {
            $blocks = array();
        }

        $block_types = ['hero','services','two-col','how','reviews','zone','accordion','cta-final','heading','text','image','cta','html','video','file','quote','pricing','before-after','logos','seo-content','domicile-vs-atelier','sofa-simulator','prestation'];
        $block_styles = '';
        foreach ($block_types as $bt) {
            $css_key = 'block_css_' . $bt;
            if (!empty($settings[$css_key])) {
                $block_styles .= '/* ' . $bt . " */\n" . $settings[$css_key] . "\n";
            }
        }

        $reviews = $this->fetchGoogleReviews($pdo, $settings);

        $this->view->render($template, array(
            'title'       => isset($page_row['meta_title']) && $page_row['meta_title'] ? $page_row['meta_title'] : (isset($page_row['title']) ? $page_row['title'] : $slug),
            'meta_title'  => isset($page_row['meta_title']) ? $page_row['meta_title'] : '',
            'meta_description' => isset($page_row['meta_description']) ? $page_row['meta_description'] : '',
            'og_image'    => isset($page_row['og_image']) ? $page_row['og_image'] : '',
            'canonical_url' => isset($page_row['canonical_url']) ? $page_row['canonical_url'] : '',
            'robots'      => isset($page_row['robots']) ? $page_row['robots'] : '',
            'slug'        => $slug,
            'booking_url' => $booking_url,
            'site_name'   => $site_name,
            'logo_url'       => isset($settings['logo_url']) ? $settings['logo_url'] : '',
            'logo_light_url' => isset($settings['logo_light_url']) ? $settings['logo_light_url'] : '',
            'logo_rose_url'  => isset($settings['logo_rose_url'])  ? $settings['logo_rose_url']  : '',
            'logo_alt'       => isset($settings['logo_alt']) ? $settings['logo_alt'] : $site_name,
            'nav_items'   => $nav_items,
            'blocks'      => $blocks,
            'reviews'     => $reviews,
            'gtm_id'      => isset($settings['gtm_id']) ? $settings['gtm_id'] : null,
            'noindex_all' => isset($settings['noindex_all']) && $settings['noindex_all'] === '1',
            'robots_global' => isset($settings['robots_global']) ? $settings['robots_global'] : 'index,follow',
            'block_styles'  => $block_styles,
            'cache_version' => (isset($settings['cache_enabled']) && $settings['cache_enabled'] === '1' && isset($settings['cache_version'])) ? $settings['cache_version'] : '',
            'footer_menus'      => $this->fetchFooterMenus($pdo, $lang),
            'footer_tagline'    => isset($settings['footer_tagline']) ? $settings['footer_tagline'] : '',
            'footer_phone'      => isset($settings['footer_phone']) ? $settings['footer_phone'] : '',
            'footer_legal_text' => isset($settings['footer_legal_text']) ? $settings['footer_legal_text'] : '',
            'sticky_cta_enabled'      => ($settings['sticky_cta_enabled'] ?? '1') === '1',
            'sticky_cta_threshold'    => isset($settings['sticky_cta_threshold']) && $settings['sticky_cta_threshold'] !== '' ? (float) $settings['sticky_cta_threshold'] : 40,
            'sticky_cta_phone_label'  => isset($settings['sticky_cta_phone_label']) ? $settings['sticky_cta_phone_label'] : '',
            'sticky_cta_phone_number' => isset($settings['sticky_cta_phone_number']) && $settings['sticky_cta_phone_number'] !== '' ? $settings['sticky_cta_phone_number'] : (isset($settings['footer_phone']) ? $settings['footer_phone'] : ''),
            'sticky_cta_phone_icon'   => isset($settings['sticky_cta_phone_icon']) && $settings['sticky_cta_phone_icon'] !== '' ? $settings['sticky_cta_phone_icon'] : 'phone',
            'sticky_cta_book_label'   => isset($settings['sticky_cta_book_label']) && $settings['sticky_cta_book_label'] !== '' ? $settings['sticky_cta_book_label'] : 'Réserver un créneau',
            'sticky_cta_book_url'     => isset($settings['sticky_cta_book_url']) && $settings['sticky_cta_book_url'] !== '' ? $settings['sticky_cta_book_url'] : (isset($settings['booking_url']) && $settings['booking_url'] ? $settings['booking_url'] : '#'),
            'sticky_cta_book_icon'    => isset($settings['sticky_cta_book_icon']) && $settings['sticky_cta_book_icon'] !== '' ? $settings['sticky_cta_book_icon'] : 'calendar',
            'cookie_banner_enabled' => ($settings['cookie_banner_enabled'] ?? '1') === '1',
            'cookie_banner_text'    => $settings['cookie_banner_text'] ?? 'Nous utilisons des cookies pour améliorer votre expérience. Vous pouvez accepter ou refuser les cookies analytiques et publicitaires.',
            'cookie_policy_url'     => $settings['cookie_policy_url'] ?? '/politique-cookies',
        ), 'public');
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view->render('page', ['title' => 'Page introuvable']);
    }
}
