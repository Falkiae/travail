<?php $booking_url = isset($booking_url) ? $booking_url : '#'; ?>
<section class="kn-cta-pave" aria-labelledby="cta-final-heading">
    <div class="kn-cta-pave__inner">
        <span class="kn-eyebrow" style="color:var(--kn-yellow);"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : '📅 RÉSERVATION EN LIGNE'); ?></span>
        <h2 id="cta-final-heading" class="kn-cta-pave__title"><?php echo htmlspecialchars(isset($block['title']) ? $block['title'] : 'Prendre RDV en 2 min'); ?></h2>
        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-pave__arrow" aria-label="Prendre rendez-vous en ligne">&#8594;</a>
        <a class="kn-cta-pave__phone" href="tel:+32455138419">+32 (0)4 55 13 84 19</a>
    </div>
</section>
