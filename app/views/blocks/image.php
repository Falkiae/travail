<?php
require_once __DIR__ . '/_block_helpers.php';
$src = isset($block['src']) ? $block['src'] : (isset($block['path']) ? $block['path'] : '');
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
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
