<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'night';
$isDark = ($bg === 'blue' || $bg === 'night');

$zones = isset($block['zones']) && is_array($block['zones']) ? $block['zones'] : array(
  array(
    'label' => 'Tableau de bord', 'x' => '30', 'y' => '32', 'side' => 'left',
    'title' => 'Tableau de bord et écrans',
    'desc'  => 'Les plastiques soft-touch marquent vite et se ternissent au soleil. On les nettoie sans laisser de film gras ni de brillance artificielle.',
    'acts'  => "Dépoussiérage des grilles et aérateurs\nNettoyage doux des plastiques soft-touch\nÉcrans et compteurs sans trace\nProtection UV mate, sans effet luisant",
  ),
  array(
    'label' => 'Volant et commandes', 'x' => '46', 'y' => '55', 'side' => 'left',
    'title' => 'Volant, levier et commandes',
    'desc'  => 'Ce sont les surfaces les plus touchées de l\'habitacle. On y retire le dépôt de sébum accumulé, puis on nourrit la matière.',
    'acts'  => "Dégraissage du cuir ou de l'alcantara\nNettoyage des boutons et molettes\nSoin nourrissant du cuir\nDésinfection des zones de contact",
  ),
  array(
    'label' => 'Sièges', 'x' => '70', 'y' => '48', 'side' => 'right',
    'title' => 'Sièges et assises',
    'desc'  => 'Tissu, cuir ou alcantara : chaque matière reçoit son protocole. On travaille en profondeur sans détremper la mousse.',
    'acts'  => "Injection-extraction sur tissu\nDétachage ciblé des auréoles\nNettoyage et nourrissage du cuir\nTraitement des coutures et passepoils",
  ),
  array(
    'label' => 'Moquettes et tapis', 'x' => '38', 'y' => '80', 'side' => 'left',
    'title' => 'Moquettes, tapis et seuils',
    'desc'  => 'Le sol concentre le sable, le sel d\'hiver et les odeurs. On l\'extrait plutôt que de le masquer.',
    'acts'  => "Aspiration profonde des fibres\nShampooing et extraction\nTraitement des taches de sel\nNettoyage des seuils de porte",
  ),
  array(
    'label' => 'Ciel de toit', 'x' => '58', 'y' => '14', 'side' => 'right',
    'title' => 'Ciel de toit et montants',
    'desc'  => 'Une zone fragile que l\'on traite à sec ou en mousse contrôlée, pour éviter tout décollement.',
    'acts'  => "Nettoyage à faible humidité\nTraitement des traces de doigts\nMontants et pare-soleil\nNeutralisation des odeurs de tabac",
  ),
);

$sectionClass = blockClasses($block, 'night', 'kn-czones' . ($isDark ? ' kn-czones--dark' : ''));
$blockId      = 'czones-' . (isset($__block_index) ? (int) $__block_index : 0);

// Normalise et filtre les zones exploitables
$clean = array();
foreach ($zones as $z) {
    $label = isset($z['label']) ? trim($z['label']) : '';
    if ($label === '') continue;
    $x = isset($z['x']) ? (float) $z['x'] : 50;
    $y = isset($z['y']) ? (float) $z['y'] : 50;
    $clean[] = array(
        'label' => $label,
        'x'     => max(0, min(100, $x)),
        'y'     => max(0, min(100, $y)),
        'side'  => (isset($z['side']) && $z['side'] === 'right') ? 'right' : 'left',
        'title' => isset($z['title']) && $z['title'] !== '' ? $z['title'] : $label,
        'desc'  => isset($z['desc']) ? $z['desc'] : '',
        'acts'  => isset($z['acts']) ? preg_split('/\r\n|\r|\n/', trim($z['acts'])) : array(),
    );
}
?>
<section class="<?php echo $sectionClass; ?>" id="<?php echo $blockId; ?>">
  <div class="container">

    <div class="kn-czones__head">
      <?php if (!empty($block['eyebrow'])): ?>
      <span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
      <?php endif; ?>

      <?php $czH2 = !empty($block['h2']) ? $block['h2'] : (!empty($block['title']) ? $block['title'] : ''); ?>
      <?php if ($czH2): ?>
      <h2 class="kn-section__title"><?php echo $czH2; ?></h2>
      <?php endif; ?>

      <?php if (!empty($block['intro'])): ?>
      <p class="kn-czones__intro"><?php echo $block['intro']; ?></p>
      <?php endif; ?>
    </div>

    <?php if ($clean): ?>
    <div class="kn-czones__stage">

      <figure class="kn-czones__figure">
        <?php if (!empty($block['image_url'])): ?>
        <?php echo knImage($block['image_url'], isset($block['image_alt']) ? $block['image_alt'] : '', array(
          'imgSizes' => '(max-width:900px) 100vw, 1100px',
          'class'    => 'kn-czones__img',
          'style'    => 'width:100%;display:block',
        )); ?>
        <?php else: ?>
        <div class="kn-czones__img kn-czones__img--empty" aria-hidden="true"></div>
        <?php endif; ?>

        <span class="kn-czones__veil" aria-hidden="true"></span>

        <?php foreach ($clean as $i => $z): ?>
        <button type="button"
                class="kn-czones__pin kn-czones__pin--<?php echo $z['side']; ?><?php echo $i === 0 ? ' is-active' : ''; ?>"
                style="--pin-x:<?php echo $z['x']; ?>%;--pin-y:<?php echo $z['y']; ?>%"
                data-czone="<?php echo $i; ?>"
                aria-controls="<?php echo $blockId; ?>-panel"
                aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>">
          <span class="kn-czones__dot" aria-hidden="true"></span>
          <span class="kn-czones__leader" aria-hidden="true"></span>
          <span class="kn-czones__chip">
            <span class="kn-czones__chip-num"><?php echo str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT); ?></span>
            <?php echo htmlspecialchars($z['label']); ?>
          </span>
          <span class="kn-czones__pin-sr">Zone <?php echo ($i + 1); ?> — <?php echo htmlspecialchars($z['label']); ?></span>
        </button>
        <?php endforeach; ?>
      </figure>

      <nav class="kn-czones__tabs" aria-label="Zones traitées">
        <?php foreach ($clean as $i => $z): ?>
        <button type="button"
                class="kn-czones__tab<?php echo $i === 0 ? ' is-active' : ''; ?>"
                data-czone="<?php echo $i; ?>"
                aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>">
          <span class="kn-czones__tab-num"><?php echo str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT); ?></span>
          <?php echo htmlspecialchars($z['label']); ?>
        </button>
        <?php endforeach; ?>
      </nav>

      <div class="kn-czones__panel" id="<?php echo $blockId; ?>-panel" aria-live="polite">
        <?php foreach ($clean as $i => $z): ?>
        <article class="kn-czones__card<?php echo $i === 0 ? ' is-active' : ''; ?>" data-czone-panel="<?php echo $i; ?>"<?php echo $i === 0 ? '' : ' hidden'; ?>>
          <div class="kn-czones__card-head">
            <span class="kn-czones__card-num"><?php echo str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT); ?></span>
            <h3 class="kn-czones__card-title"><?php echo htmlspecialchars($z['title']); ?></h3>
          </div>
          <?php if ($z['desc'] !== ''): ?>
          <p class="kn-czones__card-desc"><?php echo htmlspecialchars($z['desc']); ?></p>
          <?php endif; ?>
          <?php if ($z['acts']): ?>
          <ul class="kn-czones__acts">
            <?php foreach ($z['acts'] as $act): ?>
            <?php $act = trim($act); if ($act === '') continue; ?>
            <li><span class="kn-czones__act-mark" aria-hidden="true">&#10022;</span><?php echo htmlspecialchars($act); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>

    </div>
    <?php endif; ?>

    <?php if (!empty($block['note']) || !empty($block['cta_label'])): ?>
    <div class="kn-czones__foot">
      <?php if (!empty($block['note'])): ?>
      <p class="kn-czones__note"><?php echo htmlspecialchars($block['note']); ?></p>
      <?php endif; ?>
      <?php if (!empty($block['cta_label'])): ?>
      <a href="<?php echo htmlspecialchars(!empty($block['cta_url']) ? $block['cta_url'] : '#'); ?>" class="kn-btn kn-czones__cta">
        <?php echo htmlspecialchars($block['cta_label']); ?>
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>
</section>

<script>
(function () {
  var root = document.getElementById('<?php echo $blockId; ?>');
  if (!root) return;

  var triggers = root.querySelectorAll('[data-czone]');
  var panels   = root.querySelectorAll('[data-czone-panel]');

  function activate(index) {
    triggers.forEach(function (t) {
      var on = t.dataset.czone === index;
      t.classList.toggle('is-active', on);
      t.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    panels.forEach(function (p) {
      var on = p.dataset.czonePanel === index;
      p.classList.toggle('is-active', on);
      if (on) { p.removeAttribute('hidden'); } else { p.setAttribute('hidden', ''); }
    });
  }

  triggers.forEach(function (t) {
    t.addEventListener('click', function () { activate(t.dataset.czone); });
    t.addEventListener('mouseenter', function () {
      if (window.matchMedia('(hover:hover) and (min-width:900px)').matches) activate(t.dataset.czone);
    });
  });
})();
</script>
