<?php
require_once __DIR__ . '/_block_helpers.php';
$items = isset($block['items']) && is_array($block['items']) ? $block['items'] : array(
  array('icon' => 'sofa',     'title' => 'Canapé & fauteuil', 'desc' => 'Aspiration profonde, vapeur, anti-odeurs.', 'url' => '/services/canape'),
  array('icon' => 'bed',      'title' => 'Matelas', 'desc' => 'Nettoyage en profondeur, désinfection UV.', 'url' => '/services/matelas'),
  array('icon' => 'car',      'title' => 'Voiture', 'desc' => 'Intérieur complet, cuir, moquette, vitrerie.', 'url' => '/services/voiture'),
  array('icon' => 'home',     'title' => 'Terrasse', 'desc' => 'Carrelage, pierre naturelle, bois composite.', 'url' => '/services/terrasse'),
  array('icon' => 'sparkles', 'title' => 'Polissage', 'desc' => 'Céramique, lustrage carrosserie, protection.', 'url' => '/services/polissage'),
);
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php else: ?>
    <span class="kn-eyebrow">NOS SERVICES</span>
    <?php endif; ?>
    <?php $svcH2 = !empty($block['h2']) ? $block['h2'] : (!empty($block['title']) ? $block['title'] : ''); ?>
    <?php if ($svcH2): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($svcH2); ?></h2>
    <?php else: ?>
    <h2 class="kn-section__title">On s'occupe de <span class="kn-tape">tout</span>.</h2>
    <?php endif; ?>
    <div class="kn-services__grid">
      <?php foreach ($items as $svc): ?>
      <a class="kn-card kn-service-card" href="<?php echo htmlspecialchars(isset($svc['url']) ? $svc['url'] : '#'); ?>">
        <div class="kn-service-card__icon"><?php echo knIcon(isset($svc['icon']) ? $svc['icon'] : 'sparkles', array('size' => 28, 'fallback' => 'sparkle')); ?></div>
        <h3><?php echo htmlspecialchars(isset($svc['title']) ? $svc['title'] : (isset($svc['name']) ? $svc['name'] : '')); ?></h3>
        <p><?php echo htmlspecialchars(isset($svc['desc']) ? $svc['desc'] : ''); ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
