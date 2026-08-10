<?php
require_once __DIR__ . '/_block_helpers.php';
$items = isset($block['items']) && is_array($block['items']) ? $block['items'] : array(
  array('icon' => 'canape',    'title' => 'Canapé & fauteuil', 'desc' => 'Aspiration profonde, vapeur, anti-odeurs.', 'url' => '/services/canape'),
  array('icon' => 'matelas',   'title' => 'Matelas', 'desc' => 'Nettoyage en profondeur, désinfection UV.', 'url' => '/services/matelas'),
  array('icon' => 'voiture',   'title' => 'Voiture', 'desc' => 'Intérieur complet, cuir, moquette, vitrerie.', 'url' => '/services/voiture'),
  array('icon' => 'terrasse',  'title' => 'Terrasse', 'desc' => 'Carrelage, pierre naturelle, bois composite.', 'url' => '/services/terrasse'),
  array('icon' => 'polissage', 'title' => 'Polissage', 'desc' => 'Céramique, lustrage carrosserie, protection.', 'url' => '/services/polissage'),
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
    <?php if (!empty($block['intro'])): ?>
    <p class="kn-services__intro"><?php echo $block['intro']; ?></p>
    <?php endif; ?>
    <div class="kn-services__grid">
      <?php foreach ($items as $svc): ?>
      <?php $svcImage = isset($svc['image_url']) ? trim($svc['image_url']) : ''; ?>
      <a class="kn-service-card<?php echo $svcImage !== '' ? ' kn-service-card--photo' : ''; ?>" href="<?php echo htmlspecialchars(isset($svc['url']) ? $svc['url'] : '#'); ?>">
        <?php if ($svcImage !== ''): ?>
        <div class="kn-service-card__media">
          <?php echo knImage($svcImage, htmlspecialchars(isset($svc['title']) ? $svc['title'] : ''), array('imgSizes' => '(max-width:768px) 100vw, 380px', 'style' => 'width:100%;height:100%;object-fit:cover')); ?>
        </div>
        <?php endif; ?>
        <div class="kn-service-card__body">
          <div class="kn-service-card__icon"><?php echo knIcon(isset($svc['icon']) ? $svc['icon'] : 'sparkles', array('size' => 24, 'fallback' => 'sparkle')); ?></div>
          <h3 class="kn-service-card__title"><?php echo htmlspecialchars(isset($svc['title']) ? $svc['title'] : (isset($svc['name']) ? $svc['name'] : '')); ?></h3>
          <?php if (!empty($svc['desc'])): ?>
          <p class="kn-service-card__desc"><?php echo htmlspecialchars($svc['desc']); ?></p>
          <?php endif; ?>
          <span class="kn-service-card__cta"><?php echo htmlspecialchars(!empty($svc['cta_label']) ? $svc['cta_label'] : 'Découvrir'); ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
