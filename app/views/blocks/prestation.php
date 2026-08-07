<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'cream';
$isDark = ($bg === 'blue' || $bg === 'night');

$items = isset($block['items']) && is_array($block['items']) ? $block['items'] : array(
  array('title' => 'Diagnostic de la matière',  'desc' => 'Tissu, cuir, microfibre ou velours — on identifie et on adapte le traitement.'),
  array('title' => 'Aspiration profonde',       'desc' => 'Extraction des poussières et acariens jusqu\'au rembourrage.'),
  array('title' => 'Détachage ciblé',           'desc' => 'Traitement des taches tenaces, auréoles et marques d\'usage.'),
  array('title' => 'Injection / extraction',    'desc' => 'Nettoyage en profondeur des fibres, sans détremper.'),
  array('title' => 'Traitement anti-odeurs',    'desc' => 'Neutralisation des odeurs à la source, pas de simple parfum.'),
  array('title' => 'Séchage accéléré',          'desc' => 'Votre canapé est réutilisable en quelques heures.'),
);

$cols = isset($block['columns']) ? (string) $block['columns'] : '2';
if (!in_array($cols, array('1','2','3'), true)) $cols = '2';

$metas     = isset($block['metas']) && is_array($block['metas']) ? $block['metas'] : array();
$hasVisual = !empty($block['image_url']);

$sectionClass = blockClasses($block, 'cream', 'kn-presta' . ($isDark ? ' kn-presta--dark' : ''));
$gridClass    = gridClasses($block, '2-3');
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">

    <div class="kn-presta__head">
      <?php if (!empty($block['eyebrow'])): ?>
      <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
      <?php endif; ?>

      <?php $prestaH2 = !empty($block['h2']) ? $block['h2'] : (!empty($block['title']) ? $block['title'] : ''); ?>
      <?php if ($prestaH2): ?>
      <h2 class="kn-section__title"><?php echo $prestaH2; ?></h2>
      <?php endif; ?>

      <?php if (!empty($block['intro'])): ?>
      <p class="kn-presta__intro"><?php echo $block['intro']; ?></p>
      <?php endif; ?>
    </div>

    <?php if ($metas): ?>
    <div class="kn-presta__metas">
      <?php foreach ($metas as $m): ?>
      <?php $mLabel = isset($m['label']) ? $m['label'] : ''; if ($mLabel === '') continue; ?>
      <span class="kn-presta__meta">
        <?php if (!empty($m['icon'])): ?><span class="kn-presta__meta-icon"><?php echo knIcon($m['icon'], array('size' => 16)); ?></span><?php endif; ?>
        <span class="kn-presta__meta-label"><?php echo htmlspecialchars($mLabel); ?></span>
        <?php if (!empty($m['value'])): ?><strong class="kn-presta__meta-value"><?php echo htmlspecialchars($m['value']); ?></strong><?php endif; ?>
      </span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="kn-presta__body <?php echo $hasVisual ? $gridClass : 'kn-presta__body--nofigure'; ?>">

      <?php if ($hasVisual): ?>
      <div class="kn-presta__visual">
        <?php echo knImage($block['image_url'], isset($block['image_alt']) ? $block['image_alt'] : '', array(
          'imgSizes' => '(max-width:900px) 100vw, 40vw',
          'style'    => 'width:100%;height:100%;object-fit:cover;display:block',
        )); ?>
      </div>
      <?php endif; ?>

      <div class="kn-presta__panel">
        <?php if (!empty($block['list_title'])): ?>
        <p class="kn-presta__list-title"><?php echo htmlspecialchars($block['list_title']); ?></p>
        <?php endif; ?>

        <ul class="kn-presta__list kn-presta__list--<?php echo $cols; ?>">
          <?php foreach ($items as $it): ?>
          <?php
            $itTitle = isset($it['title']) ? $it['title'] : (isset($it['label']) ? $it['label'] : '');
            if ($itTitle === '') continue;
          ?>
          <li class="kn-presta__item">
            <span class="kn-presta__check" aria-hidden="true"></span>
            <span class="kn-presta__item-text">
              <span class="kn-presta__item-title"><?php echo htmlspecialchars($itTitle); ?></span>
              <?php if (!empty($it['desc'])): ?>
              <span class="kn-presta__item-desc"><?php echo htmlspecialchars($it['desc']); ?></span>
              <?php endif; ?>
            </span>
          </li>
          <?php endforeach; ?>
        </ul>

        <?php if (!empty($block['note'])): ?>
        <p class="kn-presta__note"><?php echo htmlspecialchars($block['note']); ?></p>
        <?php endif; ?>

        <?php if (!empty($block['cta_label'])): ?>
        <a href="<?php echo htmlspecialchars(!empty($block['cta_url']) ? $block['cta_url'] : '#'); ?>" class="kn-btn kn-presta__cta">
          <?php echo htmlspecialchars($block['cta_label']); ?>
        </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
