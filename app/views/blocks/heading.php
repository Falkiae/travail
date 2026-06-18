<?php
require_once __DIR__ . '/_block_helpers.php';
$level = isset($block['level']) ? $block['level'] : 'h2';
if (!in_array($level, array('h2','h3','h4'), true)) { $level = 'h2'; }
$text = isset($block['text']) ? $block['text'] : '';
$body = isset($block['body']) ? $block['body'] : '';
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php endif; ?>
    <?php if ($text): ?>
    <<?php echo $level; ?> class="kn-section__title"><?php echo $text; ?></<?php echo $level; ?>>
    <?php endif; ?>
    <?php if ($body): ?>
    <p class="kn-heading__body"><?php echo nl2br(htmlspecialchars($body)); ?></p>
    <?php endif; ?>
  </div>
</section>
