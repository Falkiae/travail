<?php
require_once __DIR__ . '/_block_helpers.php';
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container kn-prose">
    <?php echo isset($block['text']) ? $block['text'] : (isset($block['html']) ? $block['html'] : ''); ?>
  </div>
</section>
