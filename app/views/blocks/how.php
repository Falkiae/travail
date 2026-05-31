<?php
require_once __DIR__ . '/_block_helpers.php';
$steps = isset($block['steps']) && is_array($block['steps']) ? $block['steps'] : array(
  array('num' => '01', 'title' => 'Réservez en ligne', 'desc' => 'Choisissez votre service et votre créneau en moins de 2 minutes.'),
  array('num' => '02', 'title' => 'On vient chez vous', 'desc' => 'Nos techniciens arrivent équipés. Vous n\'avez rien à préparer.'),
  array('num' => '03', 'title' => 'Résultat garanti', 'desc' => 'Votre bien est comme neuf. Sinon on revient — sans frais.'),
);
?>
<section class="<?php echo blockClasses($block, 'alt'); ?>">
  <div class="container">
    <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'COMMENT ÇA MARCHE'); ?></span>
    <?php if (!empty($block['title'])): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($block['title']); ?></h2>
    <?php else: ?>
    <h2 class="kn-section__title">Trois étapes, <span class="kn-highlight">zéro stress</span>.</h2>
    <?php endif; ?>
    <div class="kn-how__steps">
      <?php foreach ($steps as $step): ?>
      <div class="kn-how__step">
        <div class="kn-how__num"><?php echo htmlspecialchars(isset($step['num']) ? $step['num'] : (isset($step['title']) ? '' : '')); ?></div>
        <div>
          <h3><?php echo htmlspecialchars(isset($step['title']) ? $step['title'] : ''); ?></h3>
          <p><?php echo htmlspecialchars(isset($step['desc']) ? $step['desc'] : (isset($step['body']) ? $step['body'] : '')); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
