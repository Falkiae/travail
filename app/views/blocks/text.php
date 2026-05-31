<?php
$bg = isset($block['bg']) ? $block['bg'] : 'white';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'white');
?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container kn-prose">
    <?php echo isset($block['text']) ? $block['text'] : (isset($block['html']) ? $block['html'] : ''); ?>
  </div>
</section>
