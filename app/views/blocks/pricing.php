<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'white';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass  = blockClasses($block, 'white', 'kn-pricing');
$eyebrowColor  = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
$headingColor  = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';

$defaultItems = [
    ['image_url' => '', 'label' => '1 à 3 places', 'price' => '99€', 'cta_url' => '#', 'cta_text' => 'Réserver'],
    ['image_url' => '', 'label' => '4 à 5 places', 'price' => '140€', 'cta_url' => '#', 'cta_text' => 'Réserver'],
    ['image_url' => '', 'label' => '6 à 8 places', 'price' => '170€', 'cta_url' => '#', 'cta_text' => 'Réserver'],
    ['image_url' => '', 'label' => '8 places et +', 'price' => '200€', 'cta_url' => '#', 'cta_text' => 'Réserver'],
];
$items = isset($block['items']) && is_array($block['items']) && count($block['items']) ? $block['items'] : $defaultItems;
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;"><?php echo $block['eyebrow']; ?></span>
    <?php endif; ?>
    <?php if (!empty($block['h2'])): ?>
    <h2 class="kn-section__title" style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
    <?php endif; ?>
    <?php if (!empty($block['intro'])): ?>
    <p class="kn-section__intro" style="<?php echo $isDark ? 'color:rgba(255,255,255,.75);' : ''; ?>"><?php echo nl2br(htmlspecialchars($block['intro'])); ?></p>
    <?php endif; ?>
    <div class="kn-pricing__grid">
      <?php foreach ($items as $item): ?>
      <div class="kn-pricing-card <?php echo $isDark ? 'kn-pricing-card--dark' : ''; ?>">
        <?php if (!empty($item['image_url'])): ?>
        <div class="kn-pricing-card__img">
          <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars(isset($item['label']) ? $item['label'] : ''); ?>" loading="lazy">
        </div>
        <?php endif; ?>
        <div class="kn-pricing-card__body">
          <p class="kn-pricing-card__label"><?php echo htmlspecialchars(isset($item['label']) ? $item['label'] : ''); ?></p>
          <p class="kn-pricing-card__price"><?php echo htmlspecialchars(isset($item['price']) ? $item['price'] : ''); ?></p>
          <?php if (!empty($item['cta_url'])): ?>
          <a href="<?php echo htmlspecialchars($item['cta_url']); ?>" class="kn-btn">
            <?php echo htmlspecialchars(isset($item['cta_text']) && $item['cta_text'] ? $item['cta_text'] : 'Réserver'); ?>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
