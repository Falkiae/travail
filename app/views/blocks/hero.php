<?php
$booking_url = isset($booking_url) ? $booking_url : '#';

$bg = isset($block['bg']) ? $block['bg'] : 'cream';
if (!in_array($bg, ['white','alt','blue','night','cream','rose'], true)) {
    $bg = 'cream';
}
$bgClass = 'kn-bg--' . $bg;
$isDark  = ($bg === 'blue' || $bg === 'night');

// Eyebrow color
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';

// H1 color
$h1Color = $isDark ? 'var(--kn-white)' : 'var(--kn-night)';

// Social proof color
$proofColor = $isDark ? 'rgba(255,255,255,.7)' : 'var(--kn-muted)';

// Pills — each pill can have {text, style} or just a string
$defaultPills = [
    ['text' => '✓ À domicile',   'style' => 'white'],
    ['text' => '★ Garantie',     'style' => 'yellow'],
    ['text' => '⚡ Devis en 2 min','style' => 'night'],
];
$rawPills = isset($block['pills']) && is_array($block['pills']) ? $block['pills'] : $defaultPills;

// Pill class map
$pillClassMap = [
    'white'  => 'kn-pill--' . ($isDark ? 'white' : 'blue'),
    'yellow' => 'kn-pill--yellow',
    'night'  => 'kn-pill--night',
    'blue'   => 'kn-pill--blue',
    'cream'  => 'kn-pill--cream',
];
?>
<section class="kn-section <?php echo $bgClass; ?> kn-hero">
  <div class="container">
    <div class="kn-hero__inner">

      <div class="kn-hero__content">

        <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;">
          <?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'CANAPÉ · VOITURE · MATELAS — À DOMICILE'); ?>
        </span>

        <h1 class="kn-hero__heading" style="color:<?php echo $h1Color; ?>;">
          <?php echo isset($block['h1']) ? htmlspecialchars($block['h1']) : 'Nettoyage à domicile de canapé, matelas et voitures.'; ?>
        </h1>

        <?php if (!empty($block['body'])): ?>
        <p class="kn-hero__body"><?php echo $block['body']; ?></p>
        <?php endif; ?>

        <div class="kn-hero__pills">
          <?php foreach ($rawPills as $pill):
            $pillText  = is_array($pill) ? (isset($pill['text'])  ? $pill['text']  : '') : $pill;
            $pillStyle = is_array($pill) ? (isset($pill['style']) ? $pill['style'] : 'white') : 'white';
            $pillClass = isset($pillClassMap[$pillStyle]) ? $pillClassMap[$pillStyle] : 'kn-pill--blue';
          ?>
          <span class="kn-pill <?php echo $pillClass; ?>"><?php echo htmlspecialchars($pillText); ?></span>
          <?php endforeach; ?>
        </div>

        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-card kn-cta-card--blue">
          <div class="kn-cta-card__content">
            <span class="kn-eyebrow">&#128197; <?php echo htmlspecialchars(isset($block['cta_eyebrow']) ? $block['cta_eyebrow'] : 'RÉSERVATION EN LIGNE'); ?></span>
            <p class="kn-cta-card__title"><?php echo htmlspecialchars(isset($block['cta_title']) ? $block['cta_title'] : 'Prendre RDV en 2 min'); ?></p>
          </div>
          <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
        </a>

        <?php if (!empty($block['social_proof'])): ?>
        <p class="kn-hero__proof" style="color:<?php echo $proofColor; ?>;">
          <?php echo htmlspecialchars($block['social_proof']); ?>
        </p>
        <?php endif; ?>

      </div>

      <div class="kn-hero__visual">
        <?php if (!empty($block['image_url'])): ?>
          <img src="<?php echo htmlspecialchars($block['image_url']); ?>"
               alt="<?php echo htmlspecialchars(isset($block['image_alt']) ? $block['image_alt'] : ''); ?>"
               loading="eager">
        <?php else: ?>
          <div class="kn-placeholder kn-placeholder--hero">Photo / illustration héro</div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
