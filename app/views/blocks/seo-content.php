<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'white';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass = blockClasses($block, 'white', 'kn-seo-content');
$headingColor = $isDark ? 'var(--kn-white)' : 'var(--kn-night)';
$bodyColor    = $isDark ? 'rgba(255,255,255,.85)' : '';

$layout   = isset($block['layout']) ? $block['layout'] : 'text-only';
$imageUrl = isset($block['image_url']) ? $block['image_url'] : '';
$imageAlt = isset($block['image_alt']) ? $block['image_alt'] : '';

$hasImage = $imageUrl && $layout !== 'text-only';

// CTA optionnel (libellé + lien requis) : style, icône, alignement, ouverture, mention
if (!function_exists('knSeoCta')) {
    function knSeoCta(array $block, bool $isDark, string $bookingUrl = '#'): string
    {
        $label = isset($block['cta_label']) ? trim((string) $block['cta_label']) : '';
        if ($label === '') return '';
        $url    = !empty($block['cta_url']) ? $block['cta_url'] : $bookingUrl;
        $style  = isset($block['cta_style']) && in_array($block['cta_style'], array('primary', 'outline', 'card', 'link'), true) ? $block['cta_style'] : 'primary';
        $align  = isset($block['cta_align']) && in_array($block['cta_align'], array('left', 'center', 'right'), true) ? $block['cta_align'] : 'left';
        $target = isset($block['cta_target']) && $block['cta_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';
        $icon   = !empty($block['cta_icon']) ? knIcon($block['cta_icon'], array('size' => 16)) : '';
        $note   = isset($block['cta_note']) ? trim((string) $block['cta_note']) : '';
        $href   = htmlspecialchars($url);
        $text   = htmlspecialchars($label);

        if ($style === 'card') {
            $cls  = $isDark ? 'kn-cta-card kn-cta-card--glass' : 'kn-cta-card kn-cta-card--navy';
            $link = '<a href="' . $href . '" class="' . $cls . ' kn-seo-content__cta-card"' . $target . '>'
                  . '<div class="kn-cta-card__content"><p class="kn-cta-card__title">' . $icon . $text . '</p></div>'
                  . '<span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span></a>';
        } elseif ($style === 'link') {
            $link = '<a href="' . $href . '" class="kn-seo-content__link"' . $target . '>' . $icon . '<span>' . $text . '</span><span aria-hidden="true">&#8594;</span></a>';
        } elseif ($style === 'outline') {
            $link = '<a href="' . $href . '" class="kn-btn-outline kn-seo-content__btn"' . $target . '>' . $icon . $text . '</a>';
        } else {
            $link = '<a href="' . $href . '" class="kn-btn kn-seo-content__btn"' . $target . '>' . $icon . $text . '</a>';
        }
        $noteHtml = $note !== '' ? '<p class="kn-seo-content__cta-note">' . htmlspecialchars($note) . '</p>' : '';
        return '<div class="kn-seo-content__cta kn-seo-content__cta--' . $align . ' kn-seo-content__cta--' . $style . '">' . $link . $noteHtml . '</div>';
    }
}
$booking_url = isset($booking_url) ? $booking_url : '#';
$ctaHtml     = knSeoCta($block, $isDark, $booking_url);
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">
    <?php if ($hasImage): ?>
    <div class="kn-grid kn-grid--<?php echo $layout === 'image-left' ? '1-2' : '2-1'; ?>">
      <?php if ($layout === 'image-left'): ?>
      <div class="kn-seo-content__visual">
        <?php echo knImage($imageUrl, $imageAlt, ['imgSizes' => '(max-width:768px) 100vw, 50vw']); ?>
      </div>
      <?php endif; ?>
      <div class="kn-seo-content__text">
        <?php if (!empty($block['h2'])): ?>
        <h2 style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
        <?php endif; ?>
        <?php if (!empty($block['body'])): ?>
        <div class="kn-prose" style="<?php echo $bodyColor ? 'color:' . $bodyColor . ';' : ''; ?>"><?php echo $block['body']; ?></div>
        <?php endif; ?>
        <?php echo $ctaHtml; ?>
      </div>
      <?php if ($layout === 'image-right'): ?>
      <div class="kn-seo-content__visual">
        <?php echo knImage($imageUrl, $imageAlt, ['imgSizes' => '(max-width:768px) 100vw, 50vw']); ?>
      </div>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="kn-seo-content__text kn-seo-content__text--full">
      <?php if (!empty($block['h2'])): ?>
      <h2 style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
      <?php endif; ?>
      <?php if (!empty($block['body'])): ?>
      <div class="kn-prose" style="<?php echo $bodyColor ? 'color:' . $bodyColor . ';' : ''; ?>"><?php echo $block['body']; ?></div>
      <?php endif; ?>
      <?php echo $ctaHtml; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
