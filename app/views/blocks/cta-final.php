<?php
require_once __DIR__ . '/_block_helpers.php';
$booking_url = isset($booking_url) ? $booking_url : '#';

$bg = isset($block['bg']) ? $block['bg'] : 'blue';
if (!in_array($bg, ['white','alt','blue','night','cream','rose'], true)) {
    $bg = 'blue';
}
$bgClass = 'kn-bg--' . $bg;
$isDark  = ($bg === 'blue' || $bg === 'night');
// Sur un fond déjà sombre, une carte navy s'y fondait presque
// entièrement (même couleur que la section) : verre dépoli à la place,
// comme sur les blocs hero et domicile-vs-atelier.
$cardStyle = $isDark ? 'kn-cta-card--glass' : 'kn-cta-card--navy';

// Visibility classes
$visClasses = '';
$visible = isset($block['visible']) && is_array($block['visible']) ? $block['visible'] : ['desktop','tablet','mobile'];
if (!in_array('mobile',  $visible, true)) $visClasses .= ' kn-hide-mobile';
if (!in_array('tablet',  $visible, true)) $visClasses .= ' kn-hide-tablet';
if (!in_array('desktop', $visible, true)) $visClasses .= ' kn-hide-desktop';

$eyebrow = isset($block['eyebrow']) ? $block['eyebrow'] : 'RÉSERVATION EN LIGNE';
$title   = isset($block['title'])   ? $block['title']   : 'Prendre RDV en 2 min';
$phone   = isset($block['phone'])   ? $block['phone']   : '+32 (0)4 55 13 84 19';
$phoneHref = 'tel:' . preg_replace('/[^+0-9]/', '', $phone);
?>
<section class="kn-section kn-cta-pave <?php echo $bgClass . $visClasses; ?>">
  <div class="container">
    <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-card <?php echo $cardStyle; ?>">
      <div class="kn-cta-card__content">
        <span class="kn-eyebrow"><?php echo knIcon('calendar', array('size' => 14, 'class' => 'kn-icon--eyebrow')); ?><?php echo htmlspecialchars($eyebrow); ?></span>
        <p class="kn-cta-card__title"><?php echo htmlspecialchars($title); ?></p>
      </div>
      <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
    </a>
    <p class="kn-cta-pave__phone">
      <a href="<?php echo htmlspecialchars($phoneHref); ?>"><?php echo htmlspecialchars($phone); ?></a>
    </p>
  </div>
</section>
