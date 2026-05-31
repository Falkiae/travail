<?php
$bg = isset($block['bg']) ? $block['bg'] : 'white';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'white');
$style = isset($block['style']) ? $block['style'] : 'primary';
$btnClass = ($style === 'outline') ? 'kn-btn kn-btn--outline kn-btn--lg' : 'kn-btn kn-btn--lg';
?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container" style="text-align:center;">
    <a href="<?php echo htmlspecialchars(isset($block['url']) ? $block['url'] : '#'); ?>" class="<?php echo $btnClass; ?>">
      <?php echo htmlspecialchars(isset($block['label']) ? $block['label'] : 'En savoir plus'); ?>
    </a>
  </div>
</section>
