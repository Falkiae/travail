<?php

function knGetMediaByPath(string $path): ?array
{
    static $cache = [];
    if (isset($cache[$path])) return $cache[$path];
    try {
        $pdo = \App\Core\Database::getInstance();
        $stmt = $pdo->prepare('SELECT path, webp_path, sizes, width, height FROM kn_media WHERE path = ? OR webp_path = ? LIMIT 1');
        $stmt->execute([$path, $path]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        $cache[$path] = $row;
        return $row;
    } catch (\Exception $e) {
        $cache[$path] = null;
        return null;
    }
}

function knImage(string $src, string $alt = '', array $opts = []): string
{
    if (!$src) return '';
    $loading  = isset($opts['loading']) ? $opts['loading'] : 'lazy';
    $class    = isset($opts['class']) ? $opts['class'] : '';
    $imgSizes = isset($opts['imgSizes']) ? $opts['imgSizes'] : '100vw';
    $style    = isset($opts['style']) ? $opts['style'] : '';

    $media = knGetMediaByPath($src);

    $origPath = $media && !empty($media['path']) ? $media['path'] : $src;
    $webp  = $media && !empty($media['webp_path']) ? $media['webp_path'] : null;
    $w     = $media && !empty($media['width']) ? (int) $media['width'] : null;
    $h     = $media && !empty($media['height']) ? (int) $media['height'] : null;
    $sizes = $media && !empty($media['sizes']) ? json_decode($media['sizes'], true) : null;

    $dimAttr = '';
    if ($w && $h) $dimAttr = ' width="' . $w . '" height="' . $h . '"';

    $classAttr = $class ? ' class="' . htmlspecialchars($class) . '"' : '';

    $baseStyle = 'max-width:100%;height:auto';
    if ($style) $baseStyle .= ';' . $style;
    $styleAttr = ' style="' . htmlspecialchars($baseStyle) . '"';

    $escapedOrig = htmlspecialchars($origPath);
    $escapedAlt  = htmlspecialchars($alt);

    if ($sizes && is_array($sizes)) {
        $webpSrcset = [];
        $origSrcset = [];
        $widths = ['thumb' => 400, 'medium' => 800, 'large' => 1400];
        foreach ($widths as $label => $px) {
            if (isset($sizes[$label . '_webp'])) {
                $webpSrcset[] = htmlspecialchars($sizes[$label . '_webp']) . ' ' . $px . 'w';
            }
            if (isset($sizes[$label])) {
                $origSrcset[] = htmlspecialchars($sizes[$label]) . ' ' . $px . 'w';
            }
        }
        if ($webp) $webpSrcset[] = htmlspecialchars($webp) . ' ' . ($w ?: 1920) . 'w';
        $origSrcset[] = $escapedOrig . ' ' . ($w ?: 1920) . 'w';

        $html = '<picture>';
        if ($webpSrcset) {
            $html .= '<source type="image/webp" srcset="' . implode(', ', $webpSrcset) . '" sizes="' . htmlspecialchars($imgSizes) . '">';
        }
        $html .= '<img src="' . $escapedOrig . '" srcset="' . implode(', ', $origSrcset) . '" sizes="' . htmlspecialchars($imgSizes) . '" alt="' . $escapedAlt . '" loading="' . $loading . '"' . $dimAttr . $classAttr . $styleAttr . '>';
        $html .= '</picture>';
        return $html;
    }

    if ($webp) {
        $html = '<picture>';
        $html .= '<source type="image/webp" srcset="' . htmlspecialchars($webp) . '">';
        $html .= '<img src="' . $escapedOrig . '" alt="' . $escapedAlt . '" loading="' . $loading . '"' . $dimAttr . $classAttr . $styleAttr . '>';
        $html .= '</picture>';
        return $html;
    }

    return '<img src="' . $escapedOrig . '" alt="' . $escapedAlt . '" loading="' . $loading . '"' . $dimAttr . $classAttr . $styleAttr . '>';
}

/**
 * Jeu d'icônes Lucide (stroke 1.5) — seule icônothèque autorisée par la charte.
 * Tracés en 24x24, sans remplissage : « pas d'icônes pleines colorées ».
 */
function knIconPaths(): array
{
    static $paths = array(
        'sofa'      => '<path d="M20 9V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v3"/><path d="M2 11v5a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v2H6v-2a2 2 0 0 0-4 0Z"/><path d="M4 18v2"/><path d="M20 18v2"/><path d="M12 4v9"/>',
        'armchair'  => '<path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"/><path d="M3 11v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v2H7v-2a2 2 0 0 0-4 0Z"/><path d="M5 18v2"/><path d="M19 18v2"/>',
        'bed'       => '<path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M12 4v6"/><path d="M2 18h20"/>',
        'car'       => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>',
        'truck'     => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
        'home'      => '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
        'factory'   => '<path d="M12 16h.01"/><path d="M16 16h.01"/><path d="M3 19a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9.5a.5.5 0 0 0-.769-.422l-4.462 2.844A.5.5 0 0 1 15 11.5v-2a.5.5 0 0 0-.769-.422L9.77 11.922A.5.5 0 0 1 9 11.5V3a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/><path d="M8 16h.01"/>',
        'sparkles'  => '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/><path d="M4 17v2"/><path d="M5 18H3"/>',
        'wrench'    => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'spray'     => '<path d="M3 3h.01"/><path d="M7 5h.01"/><path d="M11 7h.01"/><path d="M3 7h.01"/><path d="M7 9h.01"/><path d="M3 11h.01"/><rect width="4" height="4" x="15" y="5"/><path d="m19 9 2 2v10c0 .6-.4 1-1 1h-6c-.6 0-1-.4-1-1V11l2-2"/><path d="m13 14 8-2"/><path d="m13 19 8-2"/>',
        'droplets'  => '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"/><path d="M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97"/>',
        'wind'      => '<path d="M12.8 19.6A2 2 0 1 0 14 16H2"/><path d="M17.5 8a2.5 2.5 0 1 1 2 4H2"/><path d="M9.8 4.4A2 2 0 1 1 11 8H2"/>',
        'leaf'      => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
        'calendar'  => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>',
        'clock'     => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'check'     => '<path d="M20 6 9 17l-5-5"/>',
        'check-big' => '<path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/>',
        'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'shield'    => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
        'award'     => '<path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/><circle cx="12" cy="8" r="6"/>',
        'heart'     => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
        'zap'       => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>',
        'pin'       => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
        'phone'     => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'arrow'     => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
    );
    return $paths;
}

/**
 * Résout un nom d'icône : accepte les noms Lucide, des alias français,
 * et les emoji historiquement stockés en base (contenu existant).
 */
function knIconName(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') return '';

    static $alias = array(
        // Emoji hérités du contenu déjà enregistré
        '🛋' => 'sofa',   '🛋️' => 'sofa',   '🪑' => 'armchair',
        '🛏' => 'bed',    '🛏️' => 'bed',
        '🚗' => 'car',    '🚙' => 'car',     '🚚' => 'truck',
        '🏠' => 'home',   '🏡' => 'home',    '🏭' => 'factory',
        '✨' => 'sparkles', '🔧' => 'wrench', '🧴' => 'spray',
        '💧' => 'droplets', '🚿' => 'droplets', '🌿' => 'leaf', '♻' => 'leaf',
        '📅' => 'calendar', '🗓' => 'calendar', '⏱' => 'clock', '⏰' => 'clock',
        '✓' => 'check',  '✔' => 'check',    '✅' => 'check-big',
        '★' => 'star',   '⭐' => 'star',     '🛡' => 'shield',
        '🏆' => 'award', '💙' => 'heart',   '❤' => 'heart',  '❤️' => 'heart',
        '⚡' => 'zap',    '📍' => 'pin',     '📞' => 'phone', '☎' => 'phone',
        '→' => 'arrow',
        // Alias français
        'canape' => 'sofa', 'canapé' => 'sofa', 'fauteuil' => 'armchair',
        'matelas' => 'bed', 'lit' => 'bed',
        'voiture' => 'car', 'auto' => 'car', 'camion' => 'truck',
        'domicile' => 'home', 'maison' => 'home', 'terrasse' => 'home',
        'atelier' => 'factory', 'usine' => 'factory',
        'polissage' => 'sparkles', 'etincelle' => 'sparkles', 'brillance' => 'sparkles',
        'outil' => 'wrench', 'cle' => 'wrench',
        'vaporisateur' => 'spray', 'produit' => 'spray',
        'eau' => 'droplets', 'vapeur' => 'droplets', 'sechage' => 'wind', 'séchage' => 'wind',
        'ecologique' => 'leaf', 'écologique' => 'leaf', 'nature' => 'leaf',
        'rdv' => 'calendar', 'reservation' => 'calendar', 'réservation' => 'calendar', 'agenda' => 'calendar',
        'duree' => 'clock', 'durée' => 'clock', 'temps' => 'clock', 'heure' => 'clock',
        'valide' => 'check', 'coche' => 'check', 'inclus' => 'check',
        'garantie' => 'shield', 'bouclier' => 'shield',
        'etoile' => 'star', 'étoile' => 'star', 'avis' => 'star', 'note' => 'star',
        'recompense' => 'award', 'récompense' => 'award', 'trophee' => 'award', 'trophée' => 'award',
        'coeur' => 'heart', 'cœur' => 'heart',
        'rapide' => 'zap', 'eclair' => 'zap', 'éclair' => 'zap',
        'adresse' => 'pin', 'lieu' => 'pin', 'zone' => 'pin', 'localisation' => 'pin',
        'telephone' => 'phone', 'téléphone' => 'phone', 'appel' => 'phone',
        'fleche' => 'arrow', 'flèche' => 'arrow',
        // Variantes Lucide
        'bed-double' => 'bed', 'house' => 'home', 'map-pin' => 'pin',
        'calendar-days' => 'calendar', 'shield-check' => 'shield',
        'spray-can' => 'spray', 'arrow-right' => 'arrow',
    );

    $paths = knIconPaths();
    if (isset($paths[$raw])) return $raw;

    // Sans le sélecteur de variante emoji
    $stripped = str_replace("\xEF\xB8\x8F", '', $raw);
    if (isset($alias[$stripped])) return $alias[$stripped];

    $lower = function_exists('mb_strtolower') ? mb_strtolower($stripped, 'UTF-8') : strtolower($stripped);
    if (isset($alias[$lower])) return $alias[$lower];
    if (isset($paths[$lower])) return $lower;

    return '';
}

/**
 * Rend une icône Lucide en SVG inline (stroke 1.5, currentColor).
 * Retourne l'étincelle ✦ de la marque si le nom est inconnu et qu'un
 * repli est demandé.
 */
/**
 * Cherche un fichier SVG fourni par la marque dans public/assets/icons/.
 * Ces fichiers priment toujours sur le jeu Lucide de secours.
 */
function knIconFile(string $name): ?string
{
    static $cache = array();
    if (array_key_exists($name, $cache)) return $cache[$name];

    $publicDir = dirname(__DIR__, 3) . '/public';
    $file      = null;

    if (substr(strtolower(trim($name)), -4) === '.svg') {
        // Chemin de fichier : médiathèque (/uploads/…) ou tout SVG servi par le site
        $rel  = '/' . ltrim(parse_url(trim($name), PHP_URL_PATH) ?: '', '/');
        $real = realpath($publicDir . $rel);
        // Confiné à public/ : pas de remontée d'arborescence
        if ($real !== false && strpos($real, $publicDir . DIRECTORY_SEPARATOR) === 0 && is_file($real)) {
            $file = $real;
        }
    } else {
        // Nom court : fichier de la bibliothèque d'icônes
        $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($name));
        if ($slug !== '') {
            $candidate = $publicDir . '/assets/icons/' . $slug . '.svg';
            if (is_file($candidate)) $file = $candidate;
        }
    }

    if ($file === null) { $cache[$name] = null; return null; }

    $svg = file_get_contents($file);
    if ($svg === false || stripos($svg, '<svg') === false) { $cache[$name] = null; return null; }

    // Un SVG inline peut porter du script : on retire ce qui est exécutable
    $svg = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $svg);
    $svg = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $svg);

    $cache[$name] = $svg;
    return $svg;
}

/**
 * Réécrit les attributs d'un SVG fourni : taille et classe imposées,
 * viewBox et tracés conservés tels que dessinés.
 */
function knIconInline(string $svg, int $size, string $class): string
{
    // Retire prologue XML, DOCTYPE et commentaires
    $svg = preg_replace('/<\?xml.*?\?>|<!DOCTYPE.*?>|<!--.*?-->/is', '', $svg);

    if (!preg_match('/<svg\b([^>]*)>/i', $svg, $m)) return '';
    $attrs = $m[1];

    $viewBox = preg_match('/viewBox\s*=\s*"([^"]*)"/i', $attrs, $vb) ? $vb[1] : '0 0 24 24';

    // Conserve les attributs de rendu du fichier d'origine
    $keep = '';
    foreach (array('fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin') as $a) {
        if (preg_match('/\b' . preg_quote($a, '/') . '\s*=\s*"([^"]*)"/i', $attrs, $k)) {
            $keep .= ' ' . $a . '="' . htmlspecialchars($k[1]) . '"';
        }
    }

    $inner = preg_replace('/^.*?<svg\b[^>]*>/is', '', $svg);
    $inner = preg_replace('/<\/svg>\s*$/i', '', $inner);

    return '<svg class="' . htmlspecialchars($class) . '" width="' . $size . '" height="' . $size . '"'
         . ' viewBox="' . htmlspecialchars($viewBox) . '"' . $keep
         . ' aria-hidden="true" focusable="false">' . trim($inner) . '</svg>';
}

function knIcon(string $name, array $opts = array()): string
{
    $size     = isset($opts['size']) ? (int) $opts['size'] : 24;
    $class    = isset($opts['class']) ? trim('kn-icon ' . $opts['class']) : 'kn-icon';
    $fallback = isset($opts['fallback']) ? $opts['fallback'] : '';

    $resolved = knIconName($name);

    // 1. Fichier SVG fourni par la marque — sous le nom demandé ou son alias
    foreach (array(trim($name), $resolved) as $candidate) {
        if ($candidate === '') continue;
        $file = knIconFile($candidate);
        if ($file !== null) {
            $out = knIconInline($file, $size, $class);
            if ($out !== '') return $out;
        }
    }

    // 2. Jeu Lucide intégré
    if ($resolved !== '') {
        $paths = knIconPaths();
        return '<svg class="' . htmlspecialchars($class) . '" width="' . $size . '" height="' . $size . '"'
             . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"'
             . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
             . $paths[$resolved]
             . '</svg>';
    }

    // 3. Étincelle de la marque
    if ($fallback === 'sparkle') {
        return '<span class="kn-icon kn-icon--sparkle" aria-hidden="true">&#10022;</span>';
    }
    return '';
}

/**
 * Returns CSS classes for a block's <section> element.
 * Handles: bg ambiance + responsive visibility.
 */
function blockClasses(array $block, string $defaultBg = 'white', string $extra = ''): string
{
    $bg = isset($block['bg']) ? $block['bg'] : $defaultBg;
    if (!in_array($bg, ['white','alt','blue','night','cream','rose'], true)) {
        $bg = $defaultBg;
    }
    $classes = ['kn-section', 'kn-bg--' . $bg];

    if (!empty($extra)) {
        $classes[] = $extra;
    }

    // Responsive visibility
    $visible = isset($block['visible']) && is_array($block['visible']) ? $block['visible'] : ['desktop','tablet','mobile'];
    if (!in_array('mobile',  $visible, true)) $classes[] = 'kn-hide-mobile';
    if (!in_array('tablet',  $visible, true)) $classes[] = 'kn-hide-tablet';
    if (!in_array('desktop', $visible, true)) $classes[] = 'kn-hide-desktop';

    return implode(' ', $classes);
}

/**
 * Returns CSS classes for a two-column grid inside a block.
 * Handles: column ratio + reverse + mobile options.
 */
function gridClasses(array $block, string $defaultLayout = '1-1'): string
{
    $layout = isset($block['layout']) ? $block['layout'] : $defaultLayout;
    $allowed = ['1-1','1-2','2-1','1-3','3-1','3-2','2-3','full'];
    if (!in_array($layout, $allowed, true)) $layout = $defaultLayout;

    $classes = ['kn-grid', 'kn-grid--' . $layout];

    if (!empty($block['reverse']))       $classes[] = 'kn-grid--reverse';
    if (!empty($block['mobile_reverse'])) $classes[] = 'kn-grid--mobile-reverse';
    if (!empty($block['mobile_hide_visual'])) $classes[] = 'kn-grid--mobile-hide-visual';

    return implode(' ', $classes);
}
