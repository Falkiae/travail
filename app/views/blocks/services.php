<?php
$bg = isset($block['bg']) ? $block['bg'] : 'white';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'white');
$items = isset($block['items']) && is_array($block['items']) ? $block['items'] : array(
  array('icon' => '🛋️', 'title' => 'Canapé & fauteuil', 'desc' => 'Aspiration profonde, vapeur, anti-odeurs.', 'url' => '/services/canape'),
  array('icon' => '🛏️', 'title' => 'Matelas', 'desc' => 'Nettoyage en profondeur, désinfection UV.', 'url' => '/services/matelas'),
  array('icon' => '🚗', 'title' => 'Voiture', 'desc' => 'Intérieur complet, cuir, moquette, vitrerie.', 'url' => '/services/voiture'),
  array('icon' => '🏠', 'title' => 'Terrasse', 'desc' => 'Carrelage, pierre naturelle, bois composite.', 'url' => '/services/terrasse'),
  array('icon' => '✨', 'title' => 'Polissage', 'desc' => 'Céramique, lustrage carrosserie, protection.', 'url' => '/services/polissage'),
);
?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php else: ?>
    <span class="kn-eyebrow">NOS SERVICES</span>
    <?php endif; ?>
    <?php if (!empty($block['title'])): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($block['title']); ?></h2>
    <?php else: ?>
    <h2 class="kn-section__title">On s'occupe de <span class="kn-tape">tout</span>.</h2>
    <?php endif; ?>
    <div class="kn-services__grid">
      <?php foreach ($items as $svc): ?>
      <a class="kn-card kn-service-card" href="<?php echo htmlspecialchars(isset($svc['url']) ? $svc['url'] : '#'); ?>">
        <div class="kn-service-card__icon"><?php echo isset($svc['icon']) ? $svc['icon'] : '✦'; ?></div>
        <h3><?php echo htmlspecialchars(isset($svc['title']) ? $svc['title'] : (isset($svc['name']) ? $svc['name'] : '')); ?></h3>
        <p><?php echo htmlspecialchars(isset($svc['desc']) ? $svc['desc'] : ''); ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
