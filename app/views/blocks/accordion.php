<?php
require_once __DIR__ . '/_block_helpers.php';
$items = array();
if (isset($block['items']) && is_array($block['items'])) {
    $items = $block['items'];
}
// Map old 'question'/'answer' keys to 'q'/'a'
$normalized = array();
foreach ($items as $item) {
    $q = isset($item['q']) ? $item['q'] : (isset($item['question']) ? $item['question'] : '');
    $a = isset($item['a']) ? $item['a'] : (isset($item['answer']) ? $item['answer'] : '');
    $normalized[] = array('q' => $q, 'a' => $a);
}
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php endif; ?>
    <?php if (!empty($block['h2'])): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($block['h2']); ?></h2>
    <?php elseif (!empty($block['title'])): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($block['title']); ?></h2>
    <?php endif; ?>
    <?php if (!empty($block['intro'])): ?>
    <p class="kn-section__intro"><?php echo nl2br(htmlspecialchars($block['intro'])); ?></p>
    <?php endif; ?>
    <div class="kn-accordion">
      <?php foreach ($normalized as $faq): ?>
      <details class="kn-accordion__item">
        <summary class="kn-accordion__q"><?php echo htmlspecialchars($faq['q']); ?></summary>
        <div class="kn-accordion__a"><p><?php echo htmlspecialchars($faq['a']); ?></p></div>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php if (!empty($normalized)): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    <?php
    $faq_ld = array();
    foreach ($normalized as $faq) {
        $faq_ld[] = '{"@type":"Question","name":' . json_encode($faq['q']) . ',"acceptedAnswer":{"@type":"Answer","text":' . json_encode($faq['a']) . '}}';
    }
    echo implode(",\n    ", $faq_ld);
    ?>
  ]
}
</script>
<?php endif; ?>
