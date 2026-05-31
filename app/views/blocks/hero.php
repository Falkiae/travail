<?php
$booking_url = isset($booking_url) ? $booking_url : '#';

$bg = isset($block['bg']) ? $block['bg'] : 'blue';
if (!in_array($bg, ['white','alt','blue','night','cream','rose'], true)) {
    $bg = 'blue';
}
$bgClass = 'kn-bg--' . $bg;

// Dark backgrounds: blue, night → light text, yellow/white CTA
$isDark = ($bg === 'blue' || $bg === 'night');

// Eyebrow color
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';

// CTA block style: on dark bg → frosted glass; on light bg → blue card
$ctaBlockStyle = $isDark
    ? 'background:rgba(255,255,255,.12);backdrop-filter:blur(8px);'
    : 'background:var(--kn-blue);';

// CTA arrow + title color
$ctaTitleColor = $isDark ? 'var(--kn-white)' : 'var(--kn-white)';
$ctaEyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-yellow)';

// Pills: on dark → white pills; on light → blue pills
$pillClass = $isDark ? 'kn-pill--white' : 'kn-pill--blue';

// Social proof color
$proofColor = $isDark ? 'rgba(255,255,255,.7)' : 'var(--kn-muted)';

// Arrow (CTA pavé) button style
$arrowClass = $isDark ? 'kn-cta-pave__arrow kn-cta-pave__arrow--white' : 'kn-cta-pave__arrow kn-cta-pave__arrow--solid';
?>
<section class="kn-section <?php echo $bgClass; ?> kn-hero">
  <div class="container">
    <div class="kn-hero__inner">
      <div class="kn-hero__content">
        <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;">
          <?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'NETTOYAGE À DOMICILE · LIÈGE · NAMUR · BRUXELLES'); ?>
        </span>
        <h1 class="kn-hero__heading">
          <?php
            $h1 = isset($block['h1']) ? htmlspecialchars($block['h1']) : 'Nettoyage de canapés, matelas &amp; voitures';
            $h1sub = isset($block['h1_sub']) ? $block['h1_sub'] : 'à domicile ou en atelier.';
            echo $h1;
          ?>
          <br><em class="kn-hero__sub"><?php echo htmlspecialchars($h1sub); ?></em>
        </h1>
        <div class="kn-hero__pills">
          <?php
          $defaultPills = ['&#10003; À domicile','&#9733; Garantie','&#9889; En 2&nbsp;min','&#127463;&#127466; Belgique'];
          $pills = isset($block['pills']) && is_array($block['pills']) ? $block['pills'] : $defaultPills;
          foreach ($pills as $pill):
          ?>
          <span class="kn-pill <?php echo $pillClass; ?>"><?php echo is_array($pill) ? htmlspecialchars($pill['text']) : $pill; ?></span>
          <?php endforeach; ?>
        </div>
        <div class="kn-hero__cta-block" style="<?php echo $ctaBlockStyle; ?>border-radius:var(--r-card);padding:2rem;display:flex;flex-direction:column;align-items:flex-start;gap:.75rem;">
          <span class="kn-eyebrow" style="color:<?php echo $ctaEyebrowColor; ?>;">&#128197; <?php echo htmlspecialchars(isset($block['cta_eyebrow']) ? $block['cta_eyebrow'] : 'RÉSERVATION EN LIGNE'); ?></span>
          <p class="kn-cta-pave__title" style="color:<?php echo $ctaTitleColor; ?>;font-size:clamp(1.6rem,2.5vw,2.2rem);font-weight:900;font-style:italic;line-height:.95;margin:0;">
            <?php echo htmlspecialchars(isset($block['cta_title']) ? $block['cta_title'] : 'Prendre RDV en 2 min'); ?>
          </p>
          <a href="<?php echo htmlspecialchars($booking_url); ?>" class="<?php echo $arrowClass; ?>" aria-label="Prendre rendez-vous">&#8594;</a>
        </div>
        <p class="kn-hero__proof" style="color:<?php echo $proofColor; ?>;">
          <?php echo htmlspecialchars(isset($block['social_proof']) ? $block['social_proof'] : '4,9/5 · +110 avis · +400 canapés · +250 voitures'); ?>
        </p>
      </div>
      <div class="kn-hero__visual" aria-hidden="true">
        <?php if (!empty($block['image_url'])): ?>
          <img src="<?php echo htmlspecialchars($block['image_url']); ?>" alt="<?php echo htmlspecialchars(isset($block['image_alt']) ? $block['image_alt'] : ''); ?>" loading="eager">
        <?php else: ?>
          <div class="kn-placeholder kn-placeholder--hero">Photo héro</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
