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
