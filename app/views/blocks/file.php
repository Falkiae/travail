<?php
require_once __DIR__ . '/_block_helpers.php';
$src = isset($block['url']) ? $block['url'] : (isset($block['file_url']) ? $block['file_url'] : (isset($block['path']) ? $block['path'] : ''));
?>
<?php if (!empty($src)): ?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container">
    <a href="<?php echo htmlspecialchars($src); ?>" class="kn-btn kn-btn--outline" download>
      &#8595; <?php echo htmlspecialchars(isset($block['label']) ? $block['label'] : 'Télécharger'); ?>
    </a>
  </div>
</section>
<?php endif; ?>
