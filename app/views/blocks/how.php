<?php
$booking_url = isset($booking_url) ? $booking_url : '#';
$steps = array();
if (isset($block['steps']) && is_array($block['steps'])) {
    $steps = $block['steps'];
} elseif (isset($block['steps']) && is_string($block['steps'])) {
    $d = json_decode($block['steps'], true);
    if (is_array($d)) $steps = $d;
}
?>
<section class="kn-how kn-section" aria-labelledby="how-heading">
    <div class="container">
        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'RÉSERVATION EN LIGNE'); ?></span>
            <h2 id="how-heading"><?php echo htmlspecialchars(isset($block['h2']) ? $block['h2'] : 'Réservez votre nettoyage à domicile en 2 minutes'); ?></h2>
        </header>
        <ol class="kn-how__steps" role="list">
        <?php foreach ($steps as $si => $step): ?>
            <li class="kn-step">
                <div class="kn-step__number" aria-hidden="true"><?php echo (int)$si + 1; ?></div>
                <div>
                    <p class="kn-step__title"><?php echo htmlspecialchars(isset($step['title']) ? $step['title'] : ''); ?></p>
                    <p class="kn-step__desc"><?php echo htmlspecialchars(isset($step['body']) ? $step['body'] : ''); ?></p>
                </div>
            </li>
        <?php endforeach; ?>
        </ol>
        <div style="text-align:center;">
            <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-btn kn-btn--lg">Prendre RDV en 2 min &#8594;</a>
        </div>
    </div>
</section>
