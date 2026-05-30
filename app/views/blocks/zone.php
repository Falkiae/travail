<?php
$pills = array();
if (isset($block['pills']) && is_array($block['pills'])) {
    $pills = $block['pills'];
} elseif (isset($block['pills']) && is_string($block['pills'])) {
    $d = json_decode($block['pills'], true);
    if (is_array($d)) $pills = $d;
}
?>
<section class="kn-zone kn-section" aria-labelledby="zone-heading">
    <div class="container">
        <header class="kn-section__header">
            <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'OÙ ON INTERVIENT'); ?></span>
            <h2 id="zone-heading"><?php echo htmlspecialchars(isset($block['h2']) ? $block['h2'] : 'Nettoyage à domicile à Liège, Namur, Bruxelles et Luxembourg'); ?></h2>
        </header>
        <?php if (!empty($block['body'])): ?>
        <p class="kn-zone__desc"><?php echo htmlspecialchars($block['body']); ?></p>
        <?php endif; ?>
        <?php if (!empty($pills)): ?>
        <div class="kn-zone__pills">
            <?php foreach ($pills as $pill): ?>
            <span class="kn-pill"><?php echo htmlspecialchars(isset($pill['label']) ? $pill['label'] : ''); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
