<?php
require_once __DIR__ . '/_block_helpers.php';
$booking_url = isset($booking_url) ? $booking_url : '#';
$plans = isset($block['plans']) && is_array($block['plans']) ? $block['plans'] : array(
  array('duration' => 'ABONNEMENT 6 MOIS', 'name' => 'Découverte', 'discount' => '−10 %', 'sub' => 'sur chaque passage', 'points' => "Remise de 10 % sur chaque passage\nPlanification flexible\nFacture mensuelle HTVA\n- Remise flotte étendue", 'cta_label' => 'Calculer mon tarif', 'cta_url' => '#calculateur', 'featured' => ''),
  array('duration' => 'ABONNEMENT 12 MOIS', 'name' => 'Recommandé', 'discount' => '−12 %', 'sub' => 'sur chaque passage', 'points' => "Remise de 12 % sur chaque passage\nPlanification prioritaire\nFacture mensuelle HTVA\nRemise flotte : 5 % à partir de 5 véhicules", 'cta_label' => 'Calculer mon tarif', 'cta_url' => '#calculateur', 'featured' => '1', 'badge' => 'Le plus choisi'),
  array('duration' => 'ABONNEMENT 2 ANS', 'name' => 'Partenaire', 'discount' => '−15 %', 'sub' => 'sur chaque passage', 'points' => "Remise de 15 % sur chaque passage\nPlanification prioritaire\nFacture mensuelle HTVA\nRemise flotte : 5 % à partir de 5 véhicules", 'cta_label' => 'Calculer mon tarif', 'cta_url' => '#calculateur', 'featured' => ''),
);
?>
<section class="<?php echo blockClasses($block, 'alt', 'kn-plans'); ?>"<?php echo blockAnchor($block); ?>>
  <div class="container">
    <header class="kn-plans__head">
      <?php if (!empty($block['eyebrow'])): ?><span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span><?php endif; ?>
      <?php if (!empty($block['h2'])): ?><h2 class="kn-section__title"><?php echo htmlspecialchars($block['h2']); ?></h2><?php endif; ?>
      <?php if (!empty($block['intro'])): ?><p class="kn-plans__intro"><?php echo htmlspecialchars($block['intro']); ?></p><?php endif; ?>
    </header>
    <div class="kn-plans__grid">
      <?php foreach ($plans as $p):
        if (!is_array($p)) continue;
        $feat = !empty($p['featured']);
        $lines = preg_split('/\r\n|\r|\n|\|/', isset($p['points']) ? (string) $p['points'] : '');
      ?>
      <article class="kn-plans__card<?php echo $feat ? ' is-featured' : ''; ?>">
        <?php if ($feat && !empty($p['badge'])): ?><span class="kn-plans__badge"><?php echo htmlspecialchars($p['badge']); ?></span><?php endif; ?>
        <?php if (!empty($p['duration'])): ?><span class="kn-plans__dur"><?php echo htmlspecialchars($p['duration']); ?></span><?php endif; ?>
        <?php if (!empty($p['name'])): ?><h3 class="kn-plans__name"><?php echo htmlspecialchars($p['name']); ?></h3><?php endif; ?>
        <?php if (!empty($p['discount'])): ?><p class="kn-plans__disc"><?php echo htmlspecialchars($p['discount']); ?></p><?php endif; ?>
        <?php if (!empty($p['sub'])): ?><p class="kn-plans__sub"><?php echo htmlspecialchars($p['sub']); ?></p><?php endif; ?>
        <ul class="kn-plans__list">
          <?php foreach ($lines as $ln):
            $ln = trim($ln); if ($ln === '') continue;
            $off = ($ln[0] === '-');
            if ($off) $ln = trim(substr($ln, 1));
          ?>
          <li<?php echo $off ? ' class="is-off"' : ''; ?>><?php echo knIcon($off ? 'x' : 'check', array('size' => 16, 'fallback' => 'check')); ?><span><?php echo htmlspecialchars($ln); ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php if (!empty($p['cta_label'])): ?>
        <a class="kn-btn kn-plans__cta" href="<?php echo htmlspecialchars(!empty($p['cta_url']) ? $p['cta_url'] : $booking_url); ?>"><?php echo htmlspecialchars($p['cta_label']); ?></a>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
