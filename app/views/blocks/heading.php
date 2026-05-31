<?php
require_once __DIR__ . '/_block_helpers.php';
$style = isset($block['style']) ? $block['style'] : 'plain';
$styleClass = '';
switch ($style) {
    case 'tape':
        $styleClass = 'kn-tape';
        break;
    case 'highlight':
        $styleClass = 'kn-highlight';
        break;
    case 'marker':
        $styleClass = 'kn-marker';
        break;
    default:
        $styleClass = '';
        break;
}
$level = isset($block['level']) ? $block['level'] : 'h2';
if (!in_array($level, array('h2','h3','h4'), true)) { $level = 'h2'; }
$text = isset($block['text']) ? htmlspecialchars($block['text']) : '';
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php endif; ?>
    <<?php echo $level; ?> class="kn-section__title<?php echo $level === 'h2' ? '' : ''; ?>">
      <?php if ($styleClass): ?>
      <span class="<?php echo $styleClass; ?>"><?php echo $text; ?></span>
      <?php else: ?>
      <?php echo $text; ?>
      <?php endif; ?>
    </<?php echo $level; ?>>
  </div>
</section>
