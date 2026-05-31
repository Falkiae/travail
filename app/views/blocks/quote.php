<?php
$bg = isset($block['bg']) ? $block['bg'] : 'rose';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'rose');
?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container">
    <div class="kn-quote">
      <p class="kn-quote__text">&ldquo;<?php echo htmlspecialchars(isset($block['text']) ? $block['text'] : ''); ?>&rdquo;</p>
      <?php if (!empty($block['author'])): ?>
      <p class="kn-quote__author"><?php echo htmlspecialchars($block['author']); ?></p>
      <?php if (!empty($block['role'])): ?>
      <p class="kn-quote__role"><?php echo htmlspecialchars($block['role']); ?></p>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
