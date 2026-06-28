<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'alt';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass = blockClasses($block, 'alt', 'kn-logos');
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
$headingColor = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';

$items = isset($block['items']) && is_array($block['items']) ? $block['items'] : [];
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;"><?php echo $block['eyebrow']; ?></span>
    <?php endif; ?>
    <?php if (!empty($block['h2'])): ?>
    <h2 class="kn-section__title" style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
    <?php endif; ?>
    <?php if ($items): ?>
    <div class="kn-logos__grid">
      <?php foreach ($items as $logo):
        $url = isset($logo['url'])       ? $logo['url']       : '';
        $img = isset($logo['image_url']) ? $logo['image_url'] : '';
        $alt = isset($logo['alt'])       ? $logo['alt']       : '';
      ?>
      <?php if ($url): ?>
      <a href="<?php echo htmlspecialchars($url); ?>" class="kn-logos__item" target="_blank" rel="noopener">
      <?php else: ?>
      <div class="kn-logos__item">
      <?php endif; ?>
        <?php if ($img): ?>
        <?php echo knImage($img, $alt, ['imgSizes' => '120px']); ?>
        <?php else: ?>
        <span class="kn-logos__placeholder"><?php echo htmlspecialchars($alt); ?></span>
        <?php endif; ?>
      <?php if ($url): ?></a><?php else: ?></div><?php endif; ?>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
