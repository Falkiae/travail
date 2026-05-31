<?php
$bg = isset($block['bg']) ? $block['bg'] : 'white';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'white');
$reverse = !empty($block['reverse']);
?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container">
    <div class="kn-two-col__inner<?php echo $reverse ? ' kn-two-col__inner--reverse' : ''; ?>">
      <div class="kn-two-col__content">
        <?php if (!empty($block['eyebrow'])): ?>
        <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
        <?php else: ?>
        <span class="kn-eyebrow">DEUX FAÇONS DE TRAVAILLER</span>
        <?php endif; ?>
        <?php if (!empty($block['title'])): ?>
        <h2 class="kn-section__title"><?php echo htmlspecialchars($block['title']); ?></h2>
        <?php elseif (!empty($block['h2'])): ?>
        <h2 class="kn-section__title"><?php echo htmlspecialchars($block['h2']); ?></h2>
        <?php else: ?>
        <h2 class="kn-section__title">On vient chez vous — ou vous venez chez nous.</h2>
        <?php endif; ?>
        <?php if (!empty($block['left_title'])): ?>
        <p class="kn-two-col__block-title"><?php echo htmlspecialchars($block['left_title']); ?></p>
        <?php endif; ?>
        <?php if (!empty($block['left_body'])): ?>
        <p><?php echo htmlspecialchars($block['left_body']); ?></p>
        <?php endif; ?>
      </div>
      <div class="kn-two-col__visual">
        <?php if (!empty($block['image_url'])): ?>
        <img src="<?php echo htmlspecialchars($block['image_url']); ?>" alt="<?php echo htmlspecialchars(isset($block['image_alt']) ? $block['image_alt'] : ''); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        <?php else: ?>
        <span><?php echo htmlspecialchars(isset($block['right_title']) ? $block['right_title'] : 'Photo'); ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
