<?php
$bg = isset($block['bg']) ? $block['bg'] : 'night';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'night');
$zones = isset($block['zones']) && is_array($block['zones']) ? $block['zones'] : array('Liège','Namur','Bruxelles','Luxembourg','Visé','Herstal','Seraing','Huy');
// Also support pills array from admin (each pill has 'label' key)
if (isset($block['pills']) && is_array($block['pills']) && !isset($block['zones'])) {
    $zones = array();
    foreach ($block['pills'] as $pill) {
        $zones[] = isset($pill['label']) ? $pill['label'] : (is_string($pill) ? $pill : '');
    }
}
?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container">
    <div class="kn-zone__inner">
      <div class="kn-zone__content">
        <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'ZONE D\'INTERVENTION'); ?></span>
        <?php if (!empty($block['title'])): ?>
        <h2 class="kn-section__title"><?php echo htmlspecialchars($block['title']); ?></h2>
        <?php elseif (!empty($block['h2'])): ?>
        <h2 class="kn-section__title"><?php echo htmlspecialchars($block['h2']); ?></h2>
        <?php else: ?>
        <h2 class="kn-section__title">On vient <span class="kn-tape" style="color:var(--kn-night);">chez vous</span>.</h2>
        <?php endif; ?>
        <p><?php echo htmlspecialchars(isset($block['desc']) ? $block['desc'] : (isset($block['body']) ? $block['body'] : 'Liège, Namur, Bruxelles, Luxembourg et toutes les communes alentours. Atelier à Visé pour les dépôts.')); ?></p>
        <div class="kn-zone__pills">
          <?php foreach ($zones as $zone): ?>
          <span class="kn-pill kn-pill--white"><?php echo htmlspecialchars(is_string($zone) ? $zone : ''); ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="kn-zone__map kn-placeholder">Carte interactive</div>
    </div>
  </div>
</section>
