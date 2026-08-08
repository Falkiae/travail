<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'white';
$isDark = ($bg === 'blue' || $bg === 'night');
$isRose = ($bg === 'rose');

$sectionClass = blockClasses($block, 'white', 'kn-dva');
$eyebrowColor = $isDark ? 'var(--kn-rose-mid)' : ($isRose ? 'var(--kn-rose-600)' : 'var(--kn-blue)');
$headingColor = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';
$introColor   = $isDark ? 'rgba(255,255,255,.8)' : 'var(--kn-muted)';

$steps          = isset($block['steps']) && is_array($block['steps']) ? $block['steps'] : [];
$homeItems      = isset($block['home_items']) && is_array($block['home_items']) ? $block['home_items'] : [];
$workshopItems  = isset($block['workshop_items']) && is_array($block['workshop_items']) ? $block['workshop_items'] : [];

$homeTitle    = isset($block['home_title']) ? $block['home_title'] : 'À domicile';
$homeIcon     = isset($block['home_icon']) && $block['home_icon'] !== '' ? $block['home_icon'] : 'home';
$homeDesc     = isset($block['home_desc']) ? $block['home_desc'] : '';
$homeNote     = isset($block['home_note']) ? $block['home_note'] : '';

$wsTitle      = isset($block['workshop_title']) ? $block['workshop_title'] : 'En atelier Keepnew';
$wsIcon       = isset($block['workshop_icon']) && $block['workshop_icon'] !== '' ? $block['workshop_icon'] : 'factory';
$wsDesc       = isset($block['workshop_desc']) ? $block['workshop_desc'] : '';
$wsAddress    = isset($block['workshop_address']) ? $block['workshop_address'] : '';
$wsNote       = isset($block['workshop_note']) ? $block['workshop_note'] : '';

$homeCtaEyebrow = isset($block['home_cta_eyebrow']) ? $block['home_cta_eyebrow'] : '';
$homeCtaTitle   = isset($block['home_cta_title']) ? $block['home_cta_title'] : '';
$homeCtaUrl     = isset($block['home_cta_url']) ? $block['home_cta_url'] : '';

$wsCtaEyebrow   = isset($block['workshop_cta_eyebrow']) ? $block['workshop_cta_eyebrow'] : '';
$wsCtaTitle     = isset($block['workshop_cta_title']) ? $block['workshop_cta_title'] : '';
$wsCtaUrl       = isset($block['workshop_cta_url']) ? $block['workshop_cta_url'] : '';

$ctaCardClass   = $isDark ? 'kn-cta-card kn-cta-card--glass' : 'kn-cta-card kn-cta-card--navy';
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php endif; ?>

    <?php if (!empty($block['h2'])): ?>
    <h2 class="kn-section__title" style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
    <?php endif; ?>

    <?php if (!empty($block['intro'])): ?>
    <p class="kn-dva__intro" style="color:<?php echo $introColor; ?>;"><?php echo nl2br(htmlspecialchars($block['intro'])); ?></p>
    <?php endif; ?>

    <?php if (!empty($steps)): ?>
    <div class="kn-dva__steps">
      <?php foreach ($steps as $step):
        $icon  = isset($step['icon']) ? $step['icon'] : '';
        $label = isset($step['label']) ? $step['label'] : '';
      ?>
      <span class="kn-pill kn-dva__step">
        <?php if ($icon !== ''): ?><span class="kn-dva__step-icon"><?php echo knIcon($icon, array('size' => 18, 'fallback' => 'sparkle')); ?></span><?php endif; ?>
        <span><?php echo htmlspecialchars($label); ?></span>
      </span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="kn-dva__grid">
      <article class="kn-dva-card">
        <header class="kn-dva-card__head">
          <span class="kn-dva-card__icon"><?php echo knIcon($homeIcon, array('size' => 26, 'fallback' => 'sparkle')); ?></span>
          <h3 class="kn-dva-card__title"><?php echo htmlspecialchars($homeTitle); ?></h3>
        </header>
        <?php if ($homeDesc !== ''): ?>
        <p class="kn-dva-card__desc"><?php echo nl2br(htmlspecialchars($homeDesc)); ?></p>
        <?php endif; ?>
        <?php if (!empty($homeItems)): ?>
        <ul class="kn-dva-card__list">
          <?php foreach ($homeItems as $it):
            $itIcon = isset($it['icon']) ? $it['icon'] : '';
            $itLabel = isset($it['label']) ? $it['label'] : '';
          ?>
          <li class="kn-dva-card__item">
            <span class="kn-dva-card__bullet"><?php echo knIcon($itIcon !== '' ? $itIcon : 'check', array('size' => 16, 'fallback' => 'sparkle')); ?></span>
            <span><?php echo htmlspecialchars($itLabel); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if ($homeNote !== ''): ?>
        <p class="kn-dva-card__note"><?php echo htmlspecialchars($homeNote); ?></p>
        <?php endif; ?>
        <?php if ($homeCtaTitle !== ''): ?>
        <a href="<?php echo htmlspecialchars($homeCtaUrl !== '' ? $homeCtaUrl : '#'); ?>" class="<?php echo $ctaCardClass; ?> kn-dva-card__cta">
          <div class="kn-cta-card__content">
            <?php if ($homeCtaEyebrow !== ''): ?>
            <span class="kn-eyebrow"><?php echo htmlspecialchars($homeCtaEyebrow); ?></span>
            <?php endif; ?>
            <p class="kn-cta-card__title"><?php echo htmlspecialchars($homeCtaTitle); ?></p>
          </div>
          <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
        </a>
        <?php endif; ?>
      </article>

      <article class="kn-dva-card">
        <header class="kn-dva-card__head">
          <span class="kn-dva-card__icon"><?php echo knIcon($wsIcon, array('size' => 26, 'fallback' => 'sparkle')); ?></span>
          <h3 class="kn-dva-card__title"><?php echo htmlspecialchars($wsTitle); ?></h3>
        </header>
        <?php if ($wsDesc !== ''): ?>
        <p class="kn-dva-card__desc"><?php echo nl2br(htmlspecialchars($wsDesc)); ?></p>
        <?php endif; ?>
        <?php if ($wsAddress !== ''): ?>
        <p class="kn-dva-card__address"><?php echo knIcon('pin', array('size' => 16, 'class' => 'kn-icon--inline')); ?><?php echo htmlspecialchars($wsAddress); ?></p>
        <?php endif; ?>
        <?php if (!empty($workshopItems)): ?>
        <ul class="kn-dva-card__list">
          <?php foreach ($workshopItems as $it):
            $itIcon = isset($it['icon']) ? $it['icon'] : '';
            $itLabel = isset($it['label']) ? $it['label'] : '';
          ?>
          <li class="kn-dva-card__item">
            <span class="kn-dva-card__bullet"><?php echo knIcon($itIcon !== '' ? $itIcon : 'check', array('size' => 16, 'fallback' => 'sparkle')); ?></span>
            <span><?php echo htmlspecialchars($itLabel); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if ($wsNote !== ''): ?>
        <p class="kn-dva-card__note"><?php echo htmlspecialchars($wsNote); ?></p>
        <?php endif; ?>
        <?php if ($wsCtaTitle !== ''): ?>
        <a href="<?php echo htmlspecialchars($wsCtaUrl !== '' ? $wsCtaUrl : '#'); ?>" class="<?php echo $ctaCardClass; ?> kn-dva-card__cta">
          <div class="kn-cta-card__content">
            <?php if ($wsCtaEyebrow !== ''): ?>
            <span class="kn-eyebrow"><?php echo htmlspecialchars($wsCtaEyebrow); ?></span>
            <?php endif; ?>
            <p class="kn-cta-card__title"><?php echo htmlspecialchars($wsCtaTitle); ?></p>
          </div>
          <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
        </a>
        <?php endif; ?>
      </article>
    </div>
  </div>
</section>
