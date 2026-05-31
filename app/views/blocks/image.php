<?php
$bg = isset($block['bg']) ? $block['bg'] : 'white';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'white');
$src = isset($block['src']) ? $block['src'] : (isset($block['path']) ? $block['path'] : '');
?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container" style="text-align:center;">
    <?php if (!empty($src)): ?>
    <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars(isset($block['alt']) ? $block['alt'] : ''); ?>" loading="lazy" style="max-width:100%;border-radius:var(--r-card);">
    <?php if (!empty($block['caption'])): ?>
    <p style="color:var(--kn-muted);font-size:.875rem;margin-top:.5rem;"><?php echo htmlspecialchars($block['caption']); ?></p>
    <?php endif; ?>
    <?php else: ?>
    <div class="kn-placeholder" style="min-height:240px;">Image</div>
    <?php endif; ?>
  </div>
</section>
