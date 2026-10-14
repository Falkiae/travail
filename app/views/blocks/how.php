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
    <?php $howH2 = !empty($block['h2']) ? $block['h2'] : (!empty($block['title']) ? $block['title'] : ''); ?>
    <?php if ($howH2): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($howH2); ?></h2>
    <?php else: ?>
    <h2 class="kn-section__title">Trois étapes, <span class="kn-highlight">zéro stress</span>.</h2>
    <?php endif; ?>
    <div class="kn-how__steps">
      <?php foreach ($steps as $i => $step):
        $stepNum  = !empty($step['num']) ? $step['num'] : sprintf('%02d', $i + 1);
        $hasPhoto = !empty($step['image_url']);
      ?>
      <div class="kn-how__step">
        <div class="kn-how__body">
          <?php if (!$hasPhoto): ?>
          <span class="kn-how__num kn-how__num--solo" aria-hidden="true"><?php echo htmlspecialchars($stepNum); ?></span>
          <?php endif; ?>
          <h3><?php echo htmlspecialchars(isset($step['title']) ? $step['title'] : ''); ?></h3>
          <p><?php echo htmlspecialchars(isset($step['desc']) ? $step['desc'] : (isset($step['body']) ? $step['body'] : '')); ?></p>
          <?php if ($hasPhoto): ?>
          <div class="kn-how__visual">
            <div class="kn-how__photo">
              <?php echo knImage($step['image_url'], htmlspecialchars(isset($step['title']) ? $step['title'] : ''), array('imgSizes' => '220px', 'style' => 'width:100%;height:100%;object-fit:cover')); ?>
            </div>
            <span class="kn-how__num" aria-hidden="true"><?php echo htmlspecialchars($stepNum); ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
