<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'alt';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass = blockClasses($block, 'alt', 'kn-before-after');
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
$headingColor = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';

$defaultItems = [
    ['image_before_url' => '', 'image_after_url' => '', 'caption' => 'Avant / Après'],
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
    <div class="kn-before-after__grid">
      <?php foreach ($items as $item):
        $before = isset($item['image_before_url']) ? $item['image_before_url'] : '';
        $after  = isset($item['image_after_url'])  ? $item['image_after_url']  : '';
        $cap    = isset($item['caption'])           ? $item['caption']           : '';
      ?>
      <div class="kn-ba-pair">
        <div class="kn-ba-pair__images">
          <div class="kn-ba-pair__col">
            <span class="kn-ba-pair__badge">Avant</span>
            <?php if ($before): ?>
            <img src="<?php echo htmlspecialchars($before); ?>" alt="Avant" loading="lazy">
            <?php else: ?>
            <div class="kn-placeholder">Avant</div>
            <?php endif; ?>
          </div>
          <div class="kn-ba-pair__col">
            <span class="kn-ba-pair__badge kn-ba-pair__badge--after">Apres</span>
            <?php if ($after): ?>
            <img src="<?php echo htmlspecialchars($after); ?>" alt="Apres" loading="lazy">
            <?php else: ?>
            <div class="kn-placeholder">Apres</div>
            <?php endif; ?>
          </div>
        </div>
        <?php if ($cap): ?>
        <p class="kn-ba-pair__caption"><?php echo htmlspecialchars($cap); ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
