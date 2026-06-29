<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'white';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass = blockClasses($block, 'white', 'kn-dva');
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
$headingColor = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';
$introColor   = $isDark ? 'rgba(255,255,255,.8)' : 'var(--kn-muted)';

$steps          = isset($block['steps']) && is_array($block['steps']) ? $block['steps'] : [];
$homeItems      = isset($block['home_items']) && is_array($block['home_items']) ? $block['home_items'] : [];
$workshopItems  = isset($block['workshop_items']) && is_array($block['workshop_items']) ? $block['workshop_items'] : [];

$homeTitle    = isset($block['home_title']) ? $block['home_title'] : 'À domicile';
$homeIcon     = isset($block['home_icon']) && $block['home_icon'] !== '' ? $block['home_icon'] : '🏠';
$homeDesc     = isset($block['home_desc']) ? $block['home_desc'] : '';
$homeNote     = isset($block['home_note']) ? $block['home_note'] : '';

$wsTitle      = isset($block['workshop_title']) ? $block['workshop_title'] : 'En atelier Keepnew';
$wsIcon       = isset($block['workshop_icon']) && $block['workshop_icon'] !== '' ? $block['workshop_icon'] : '🏭';
$wsDesc       = isset($block['workshop_desc']) ? $block['workshop_desc'] : '';
$wsAddress    = isset($block['workshop_address']) ? $block['workshop_address'] : '';
$wsNote       = isset($block['workshop_note']) ? $block['workshop_note'] : '';

$ctaPrimaryLabel = isset($block['cta_primary_label']) ? $block['cta_primary_label'] : '';
$ctaPrimaryUrl   = isset($block['cta_primary_url']) ? $block['cta_primary_url'] : '';
$ctaSecondLabel  = isset($block['cta_secondary_label']) ? $block['cta_secondary_label'] : '';
$ctaSecondUrl    = isset($block['cta_secondary_url']) ? $block['cta_secondary_url'] : '';
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
        <?php if ($icon !== ''): ?><span class="kn-dva__step-icon"><?php echo htmlspecialchars($icon); ?></span><?php endif; ?>
        <span><?php echo htmlspecialchars($label); ?></span>
      </span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="kn-dva__grid">
      <article class="kn-dva-card">
        <header class="kn-dva-card__head">
          <span class="kn-dva-card__icon" aria-hidden="true"><?php echo htmlspecialchars($homeIcon); ?></span>
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
            <span class="kn-dva-card__bullet" aria-hidden="true"><?php echo $itIcon !== '' ? htmlspecialchars($itIcon) : '✓'; ?></span>
            <span><?php echo htmlspecialchars($itLabel); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if ($homeNote !== ''): ?>
        <p class="kn-dva-card__note"><?php echo htmlspecialchars($homeNote); ?></p>
        <?php endif; ?>
      </article>

      <article class="kn-dva-card">
        <header class="kn-dva-card__head">
          <span class="kn-dva-card__icon" aria-hidden="true"><?php echo htmlspecialchars($wsIcon); ?></span>
          <h3 class="kn-dva-card__title"><?php echo htmlspecialchars($wsTitle); ?></h3>
        </header>
        <?php if ($wsDesc !== ''): ?>
        <p class="kn-dva-card__desc"><?php echo nl2br(htmlspecialchars($wsDesc)); ?></p>
        <?php endif; ?>
        <?php if ($wsAddress !== ''): ?>
        <p class="kn-dva-card__address"><span aria-hidden="true">📍</span> <?php echo htmlspecialchars($wsAddress); ?></p>
        <?php endif; ?>
        <?php if (!empty($workshopItems)): ?>
        <ul class="kn-dva-card__list">
          <?php foreach ($workshopItems as $it):
            $itIcon = isset($it['icon']) ? $it['icon'] : '';
            $itLabel = isset($it['label']) ? $it['label'] : '';
          ?>
          <li class="kn-dva-card__item">
            <span class="kn-dva-card__bullet" aria-hidden="true"><?php echo $itIcon !== '' ? htmlspecialchars($itIcon) : '✓'; ?></span>
            <span><?php echo htmlspecialchars($itLabel); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if ($wsNote !== ''): ?>
        <p class="kn-dva-card__note"><?php echo htmlspecialchars($wsNote); ?></p>
        <?php endif; ?>
      </article>
    </div>

    <?php if ($ctaPrimaryLabel !== '' || $ctaSecondLabel !== ''): ?>
    <div class="kn-dva__ctas">
      <?php if ($ctaPrimaryLabel !== ''): ?>
      <a href="<?php echo htmlspecialchars($ctaPrimaryUrl !== '' ? $ctaPrimaryUrl : '#'); ?>" class="kn-btn"><?php echo htmlspecialchars($ctaPrimaryLabel); ?></a>
      <?php endif; ?>
      <?php if ($ctaSecondLabel !== ''): ?>
      <a href="<?php echo htmlspecialchars($ctaSecondUrl !== '' ? $ctaSecondUrl : '#'); ?>" class="kn-btn kn-btn--outline"><?php echo htmlspecialchars($ctaSecondLabel); ?></a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
