<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'cream';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass  = blockClasses($block, 'cream', 'kn-sofa-sim');
$eyebrowColor  = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
$headingColor  = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';
$introColor    = $isDark ? 'rgba(255,255,255,.8)' : 'var(--kn-muted)';

// Load sofa config from settings
try {
    $__db   = \App\Core\Database::getInstance();
    $__stmt = $__db->prepare("SELECT `value` FROM kn_settings WHERE `key` = 'sofa_config' LIMIT 1");
    $__stmt->execute();
    $cfgRow     = $__stmt->fetch(\PDO::FETCH_ASSOC);
    $sofaConfig = ($cfgRow && $cfgRow['value']) ? json_decode($cfgRow['value'], true) : null;
} catch (\Exception $e) {
    $sofaConfig = null;
}

if (!is_array($sofaConfig) || empty($sofaConfig['shapes'])) {
    $sofaConfig = [
        'shapes' => [
            ['key' => 'droit',    'label' => 'Droit',     'base_price' => 89,  'base_seats' => 2, 'cta_url' => '#'],
            ['key' => 'angle',    'label' => 'Angle / L', 'base_price' => 129, 'base_seats' => 3, 'cta_url' => '#'],
            ['key' => 'u',        'label' => 'En U',      'base_price' => 169, 'base_seats' => 5, 'cta_url' => '#'],
            ['key' => 'fauteuil', 'label' => 'Fauteuil',  'base_price' => 49,  'base_seats' => 1, 'cta_url' => '#'],
        ],
        'price_per_extra_seat' => 20,
        'price_per_meridienne' => 35,
        'cta_text'             => 'Prendre RDV',
    ];
}

$shapes             = $sofaConfig['shapes'];
$pricePerExtraSeat  = isset($sofaConfig['price_per_extra_seat']) ? (int)$sofaConfig['price_per_extra_seat'] : 20;
$pricePerMeridienne = isset($sofaConfig['price_per_meridienne']) ? (int)$sofaConfig['price_per_meridienne'] : 35;
$ctaText            = isset($sofaConfig['cta_text']) ? $sofaConfig['cta_text'] : 'Prendre RDV';

$firstShape     = $shapes[0];
$firstBasePrice = $firstShape['base_price'];
$firstBaseSeats = $firstShape['base_seats'];

$configJson = json_encode($sofaConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

$svgShapes = [
    'droit' => '<svg viewBox="0 0 88 54" fill="currentColor" aria-hidden="true" class="kn-sofa-svg__icon">
  <rect x="6" y="2" width="76" height="16" rx="5"/>
  <rect x="0" y="14" width="12" height="28" rx="5" opacity=".75"/>
  <rect x="76" y="14" width="12" height="28" rx="5" opacity=".75"/>
  <rect x="10" y="16" width="68" height="24" rx="4" opacity=".55"/>
  <rect x="14" y="40" width="7" height="12" rx="3" opacity=".4"/>
  <rect x="67" y="40" width="7" height="12" rx="3" opacity=".4"/>
</svg>',
    'angle' => '<svg viewBox="0 0 88 88" fill="currentColor" aria-hidden="true" class="kn-sofa-svg__icon">
  <rect x="32" y="2" width="54" height="14" rx="5"/>
  <rect x="32" y="14" width="54" height="32" rx="4" opacity=".55"/>
  <rect x="76" y="14" width="10" height="32" rx="5" opacity=".75"/>
  <rect x="2" y="32" width="14" height="54" rx="5"/>
  <rect x="14" y="32" width="32" height="54" rx="4" opacity=".55"/>
  <rect x="14" y="76" width="32" height="10" rx="5" opacity=".75"/>
  <rect x="14" y="14" width="20" height="20" rx="3" opacity=".35"/>
</svg>',
    'u' => '<svg viewBox="0 0 88 62" fill="currentColor" aria-hidden="true" class="kn-sofa-svg__icon">
  <rect x="2" y="2" width="14" height="58" rx="5"/>
  <rect x="14" y="2" width="20" height="58" rx="4" opacity=".55"/>
  <rect x="72" y="2" width="14" height="58" rx="5"/>
  <rect x="54" y="2" width="20" height="58" rx="4" opacity=".55"/>
  <rect x="30" y="44" width="28" height="14" rx="5"/>
  <rect x="30" y="30" width="28" height="16" rx="4" opacity=".55"/>
</svg>',
    'fauteuil' => '<svg viewBox="0 0 56 54" fill="currentColor" aria-hidden="true" class="kn-sofa-svg__icon">
  <rect x="4" y="2" width="48" height="16" rx="5"/>
  <rect x="0" y="14" width="10" height="28" rx="5" opacity=".75"/>
  <rect x="46" y="14" width="10" height="28" rx="5" opacity=".75"/>
  <rect x="8" y="16" width="40" height="24" rx="4" opacity=".55"/>
  <rect x="10" y="40" width="7" height="12" rx="3" opacity=".4"/>
  <rect x="39" y="40" width="7" height="12" rx="3" opacity=".4"/>
</svg>',
];

$blockId = 'sofa-sim-' . substr(md5(serialize($block)), 0, 8);
?>
<section class="<?php echo $sectionClass; ?>" id="<?php echo $blockId; ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;"><?php echo htmlspecialchars($block['eyebrow']); ?></span>
    <?php endif; ?>

    <?php if (!empty($block['h2'])): ?>
    <h2 class="kn-section__title" style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
    <?php endif; ?>

    <?php if (!empty($block['intro'])): ?>
    <p class="kn-sofa-intro" style="color:<?php echo $introColor; ?>;"><?php echo nl2br(htmlspecialchars($block['intro'])); ?></p>
    <?php endif; ?>

    <div class="kn-sofa-widget<?php echo $isDark ? ' kn-sofa-widget--dark' : ''; ?>">

      <!-- Step 1: Shape -->
      <div class="kn-sofa-step">
        <p class="kn-sofa-step__label"><?php echo $isDark ? '<span style="color:var(--kn-yellow)">①</span>' : '<span style="color:var(--kn-blue)">①</span>'; ?> Choisissez la forme</p>
        <div class="kn-sofa-shapes" role="group" aria-label="Forme du canapé">
          <?php foreach ($shapes as $i => $shape):
            $shapeKey = htmlspecialchars($shape['key']);
            $shapeLabel = htmlspecialchars($shape['label']);
            $svgIcon = isset($svgShapes[$shape['key']]) ? $svgShapes[$shape['key']] : $svgShapes['droit'];
          ?>
          <button type="button"
            class="kn-sofa-shape-card<?php echo $i === 0 ? ' is-selected' : ''; ?>"
            data-shape="<?php echo $shapeKey; ?>"
            aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>">
            <span class="kn-sofa-svg kn-sofa-svg--<?php echo $shapeKey; ?>">
              <?php echo $svgIcon; ?>
            </span>
            <span class="kn-sofa-shape-card__label"><?php echo $shapeLabel; ?></span>
          </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Step 2: Seats -->
      <div class="kn-sofa-step">
        <p class="kn-sofa-step__label"><?php echo $isDark ? '<span style="color:var(--kn-yellow)">②</span>' : '<span style="color:var(--kn-blue)">②</span>'; ?> Nombre de places</p>
        <div class="kn-sofa-stepper" role="group" aria-label="Nombre de places">
          <button type="button" class="kn-sofa-stepper__btn kn-sofa-stepper__dec" aria-label="Moins de places">−</button>
          <span class="kn-sofa-stepper__val" aria-live="polite"><?php echo $firstBaseSeats; ?></span>
          <button type="button" class="kn-sofa-stepper__btn kn-sofa-stepper__inc" aria-label="Plus de places">+</button>
        </div>
      </div>

      <!-- Step 3: Méridiennes -->
      <div class="kn-sofa-step">
        <p class="kn-sofa-step__label"><?php echo $isDark ? '<span style="color:var(--kn-yellow)">③</span>' : '<span style="color:var(--kn-blue)">③</span>'; ?> Méridienne(s)</p>
        <div class="kn-sofa-meridienne" role="group" aria-label="Nombre de méridiennes">
          <button type="button" class="kn-sofa-mer-btn is-selected" data-mer="0" aria-pressed="true">0</button>
          <button type="button" class="kn-sofa-mer-btn" data-mer="1" aria-pressed="false">1</button>
          <button type="button" class="kn-sofa-mer-btn" data-mer="2" aria-pressed="false">2</button>
        </div>
      </div>

      <!-- Result -->
      <div class="kn-sofa-result" aria-live="polite">
        <div class="kn-sofa-result__price-wrap">
          <span class="kn-sofa-result__label">Estimation</span>
          <span class="kn-sofa-result__price"><?php echo $firstBasePrice; ?> €</span>
          <span class="kn-sofa-result__note">* Prix estimatif, peut varier selon l'état et les dimensions réels.</span>
        </div>
        <a href="<?php echo htmlspecialchars($firstShape['cta_url'] ?: '#'); ?>"
           class="kn-cta-card kn-cta-card--blue kn-sofa-result__cta"
           id="<?php echo $blockId; ?>-cta">
          <div class="kn-cta-card__content">
            <p class="kn-cta-card__title"><?php echo htmlspecialchars($ctaText); ?></p>
          </div>
          <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
        </a>
      </div>

    </div>
  </div>
</section>

<script>
(function() {
  var cfg    = <?php echo $configJson; ?>;
  var root   = document.getElementById(<?php echo json_encode($blockId); ?>);
  if (!root) return;

  var shapeCards = root.querySelectorAll('.kn-sofa-shape-card');
  var stepperVal = root.querySelector('.kn-sofa-stepper__val');
  var stepperDec = root.querySelector('.kn-sofa-stepper__dec');
  var stepperInc = root.querySelector('.kn-sofa-stepper__inc');
  var merBtns    = root.querySelectorAll('.kn-sofa-mer-btn');
  var priceEl    = root.querySelector('.kn-sofa-result__price');
  var ctaEl      = root.getElementById ? null : document.getElementById(<?php echo json_encode($blockId . '-cta'); ?>);

  if (!ctaEl) ctaEl = document.getElementById(<?php echo json_encode($blockId . '-cta'); ?>);

  var selectedShape = cfg.shapes[0];
  var seats         = selectedShape ? selectedShape.base_seats : 2;
  var meridians     = 0;

  var MIN_SEATS = 1;
  var MAX_SEATS = 10;

  function getShape(key) {
    for (var i = 0; i < cfg.shapes.length; i++) {
      if (cfg.shapes[i].key === key) return cfg.shapes[i];
    }
    return cfg.shapes[0];
  }

  function calcPrice() {
    if (!selectedShape) return 0;
    var extra = Math.max(0, seats - selectedShape.base_seats);
    return selectedShape.base_price
      + extra * cfg.price_per_extra_seat
      + meridians * cfg.price_per_meridienne;
  }

  function updateUI() {
    var price = calcPrice();
    priceEl.textContent = price + ' €';

    stepperDec.disabled = seats <= MIN_SEATS;
    stepperInc.disabled = seats >= MAX_SEATS;

    if (selectedShape && ctaEl) {
      var url = selectedShape.cta_url || '#';
      var sep = url.indexOf('?') === -1 ? '?' : '&';
      ctaEl.href = url + sep + 'places=' + seats + '&meridienne=' + meridians;
    }
  }

  // Shape selection
  shapeCards.forEach(function(card) {
    card.addEventListener('click', function() {
      shapeCards.forEach(function(c) { c.classList.remove('is-selected'); c.setAttribute('aria-pressed','false'); });
      card.classList.add('is-selected');
      card.setAttribute('aria-pressed','true');
      selectedShape = getShape(card.dataset.shape);
      seats = Math.max(MIN_SEATS, Math.min(MAX_SEATS, selectedShape.base_seats));
      stepperVal.textContent = seats;
      updateUI();
    });
  });

  // Seat stepper
  stepperDec.addEventListener('click', function() {
    if (seats > MIN_SEATS) { seats--; stepperVal.textContent = seats; updateUI(); }
  });
  stepperInc.addEventListener('click', function() {
    if (seats < MAX_SEATS) { seats++; stepperVal.textContent = seats; updateUI(); }
  });

  // Méridienne toggle
  merBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      merBtns.forEach(function(b) { b.classList.remove('is-selected'); b.setAttribute('aria-pressed','false'); });
      btn.classList.add('is-selected');
      btn.setAttribute('aria-pressed','true');
      meridians = parseInt(btn.dataset.mer, 10) || 0;
      updateUI();
    });
  });

  updateUI();
})();
</script>
