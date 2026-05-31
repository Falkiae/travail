<?php
require_once __DIR__ . '/_block_helpers.php';
$style = isset($block['style']) ? $block['style'] : 'primary';
$btnClass = ($style === 'outline') ? 'kn-btn kn-btn--outline kn-btn--lg' : 'kn-btn kn-btn--lg';
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container" style="text-align:center;">
    <a href="<?php echo htmlspecialchars(isset($block['url']) ? $block['url'] : '#'); ?>" class="<?php echo $btnClass; ?>">
      <?php echo htmlspecialchars(isset($block['label']) ? $block['label'] : 'En savoir plus'); ?>
    </a>
  </div>
</section>
