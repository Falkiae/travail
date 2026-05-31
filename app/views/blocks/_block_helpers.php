<?php
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
