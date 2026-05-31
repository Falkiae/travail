<?php
$booking_url = isset($booking_url) ? $booking_url : '#';

$bg = isset($block['bg']) ? $block['bg'] : 'blue';
if (!in_array($bg, ['white','alt','blue','night','cream','rose'], true)) {
    $bg = 'blue';
}
$bgClass  = 'kn-bg--' . $bg;
$isDark   = ($bg === 'blue' || $bg === 'night');
$arrowClass = $isDark ? 'kn-cta-pave__arrow kn-cta-pave__arrow--white' : 'kn-cta-pave__arrow kn-cta-pave__arrow--solid';
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
$titleColor   = $isDark ? 'var(--kn-white)' : 'var(--kn-night)';
?>
<section class="kn-section kn-cta-pave <?php echo $bgClass; ?>">
  <div class="container" style="text-align:center;">
    <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;">&#128197; <?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'RÉSERVATION EN LIGNE'); ?></span>
    <div class="kn-cta-pave__row">
      <?php if (!empty($block['title'])): ?>
      <p class="kn-cta-pave__title" style="color:<?php echo $titleColor; ?>;"><?php echo htmlspecialchars($block['title']); ?></p>
      <?php else: ?>
      <p class="kn-cta-pave__title" style="color:<?php echo $titleColor; ?>;">Prendre RDV en 2&nbsp;min</p>
      <?php endif; ?>
      <a href="<?php echo htmlspecialchars($booking_url); ?>" class="<?php echo $arrowClass; ?>" aria-label="Prendre rendez-vous">&#8594;</a>
    </div>
    <?php if (!empty($block['phone'])): ?>
    <p class="kn-cta-pave__phone"><a href="tel:<?php echo preg_replace('/[^+0-9]/', '', $block['phone']); ?>"><?php echo htmlspecialchars($block['phone']); ?></a></p>
    <?php else: ?>
    <p class="kn-cta-pave__phone"><a href="tel:+32455138419">+32 (0)4 55 13 84 19</a></p>
    <?php endif; ?>
  </div>
</section>
