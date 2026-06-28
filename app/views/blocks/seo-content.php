<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'white';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass = blockClasses($block, 'white', 'kn-seo-content');
$headingColor = $isDark ? 'var(--kn-white)' : 'var(--kn-night)';
$bodyColor    = $isDark ? 'rgba(255,255,255,.85)' : '';

$layout   = isset($block['layout']) ? $block['layout'] : 'text-only';
$imageUrl = isset($block['image_url']) ? $block['image_url'] : '';
$imageAlt = isset($block['image_alt']) ? $block['image_alt'] : '';

$hasImage = $imageUrl && $layout !== 'text-only';
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">
    <?php if ($hasImage): ?>
    <div class="kn-grid kn-grid--<?php echo $layout === 'image-left' ? '1-2' : '2-1'; ?>">
      <?php if ($layout === 'image-left'): ?>
      <div class="kn-seo-content__visual">
        <?php echo knImage($imageUrl, $imageAlt, ['imgSizes' => '(max-width:768px) 100vw, 50vw']); ?>
      </div>
      <?php endif; ?>
      <div class="kn-seo-content__text">
        <?php if (!empty($block['h2'])): ?>
        <h2 style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
        <?php endif; ?>
        <?php if (!empty($block['body'])): ?>
        <div class="kn-prose" style="<?php echo $bodyColor ? 'color:' . $bodyColor . ';' : ''; ?>"><?php echo $block['body']; ?></div>
        <?php endif; ?>
      </div>
      <?php if ($layout === 'image-right'): ?>
      <div class="kn-seo-content__visual">
        <?php echo knImage($imageUrl, $imageAlt, ['imgSizes' => '(max-width:768px) 100vw, 50vw']); ?>
      </div>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="kn-seo-content__text kn-seo-content__text--full">
      <?php if (!empty($block['h2'])): ?>
      <h2 style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
      <?php endif; ?>
      <?php if (!empty($block['body'])): ?>
      <div class="kn-prose" style="<?php echo $bodyColor ? 'color:' . $bodyColor . ';' : ''; ?>"><?php echo $block['body']; ?></div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
