<?php $booking_url = isset($booking_url) ? $booking_url : '#'; ?>
<section class="kn-section kn-cta-pave kn-bg--blue">
  <div class="container" style="text-align:center;">
    <span class="kn-eyebrow">&#128197; <?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'RÉSERVATION EN LIGNE'); ?></span>
    <?php if (!empty($block['title'])): ?>
    <p class="kn-cta-pave__title"><?php echo htmlspecialchars($block['title']); ?></p>
    <?php else: ?>
    <p class="kn-cta-pave__title">Prendre RDV en 2&nbsp;min</p>
    <?php endif; ?>
    <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-pave__arrow" aria-label="Prendre rendez-vous">&#8594;</a>
    <?php if (!empty($block['phone'])): ?>
    <p class="kn-cta-pave__phone"><a href="tel:<?php echo preg_replace('/[^+0-9]/', '', $block['phone']); ?>"><?php echo htmlspecialchars($block['phone']); ?></a></p>
    <?php else: ?>
    <p class="kn-cta-pave__phone"><a href="tel:+3245513841">+32 (0)4 55 13 84 19</a></p>
    <?php endif; ?>
  </div>
</section>
