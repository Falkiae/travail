<?php
require_once __DIR__ . '/_block_helpers.php';
$sectionClass = blockClasses($block, 'white', 'kn-two-col');
$gridClass    = gridClasses($block, '1-1');
$bg = isset($block['bg']) ? $block['bg'] : 'white';
$isDark = ($bg === 'blue' || $bg === 'night');
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php endif; ?>
    <?php $twoH2 = !empty($block['h2']) ? $block['h2'] : (!empty($block['title']) ? $block['title'] : ''); ?>
    <?php if ($twoH2): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($twoH2); ?></h2>
    <?php endif; ?>
    <div class="<?php echo $gridClass; ?>">
      <div class="kn-two-col__content">
        <?php if (!empty($block['left_title'])): ?>
        <h3><?php echo htmlspecialchars($block['left_title']); ?></h3>
        <?php endif; ?>
        <?php if (!empty($block['left_body'])): ?>
        <p><?php echo htmlspecialchars($block['left_body']); ?></p>
        <?php endif; ?>
        <?php if (!empty($block['left_cta_label']) && !empty($block['left_cta_url'])): ?>
        <a href="<?php echo htmlspecialchars($block['left_cta_url']); ?>" class="kn-btn" style="margin-top:1rem;"><?php echo htmlspecialchars($block['left_cta_label']); ?></a>
        <?php endif; ?>
      </div>
      <div class="kn-two-col__visual">
        <?php if (!empty($block['image_url'])): ?>
        <img src="<?php echo htmlspecialchars($block['image_url']); ?>" alt="<?php echo htmlspecialchars(isset($block['image_alt']) ? $block['image_alt'] : ''); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;border-radius:var(--r-card);">
        <?php else: ?>
        <div class="kn-placeholder" style="min-height:300px;">Image</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
