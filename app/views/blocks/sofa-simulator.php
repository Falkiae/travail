<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'cream';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass = blockClasses($block, 'cream', 'kn-sofa-sim');
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
$headingColor = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';
$introColor   = $isDark ? 'rgba(255,255,255,.8)' : 'var(--kn-muted)';

// Load sofa config
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

$shapes      = $sofaConfig['shapes'];
$firstShape  = $shapes[0];
$ctaText     = isset($sofaConfig['cta_text']) ? $sofaConfig['cta_text'] : 'Prendre RDV';
$configJson  = json_encode($sofaConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
$blockId     = 'sofa-sim-' . substr(md5(serialize($block)), 0, 8);

// Stroke-based icons (top-view, line-art)
$svgIcons = [
    'droit' => '<svg viewBox="0 0 64 38" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <rect x="2" y="2" width="60" height="34" rx="7"/>
  <line x1="2" y1="13" x2="62" y2="13"/>
</svg>',
    'angle' => '<svg viewBox="0 0 52 52" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <path d="M2 2 h18 v48 M2 2 v18 h48 a2 2 0 0 1 2 2 v28"/>
  <line x1="12" y1="2" x2="12" y2="18"/>
  <line x1="2" y1="12" x2="50" y2="12"/>
</svg>',
    'u' => '<svg viewBox="0 0 60 50" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <path d="M2 2 v40 a6 6 0 0 0 6 6 h44 a6 6 0 0 0 6-6 v-40"/>
  <line x1="2" y1="12" x2="58" y2="12"/>
  <line x1="14" y1="12" x2="14" y2="48"/>
  <line x1="46" y1="12" x2="46" y2="48"/>
</svg>',
    'fauteuil' => '<svg viewBox="0 0 40 38" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <rect x="2" y="2" width="36" height="34" rx="7"/>
  <line x1="2" y1="13" x2="38" y2="13"/>
</svg>',
];
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

      <!-- Controls + Preview side-by-side -->
      <div class="kn-sofa-layout">

        <!-- Left: Steps -->
        <div class="kn-sofa-controls">

          <!-- Step 1: Shape -->
          <div class="kn-sofa-step">
            <p class="kn-sofa-step__label">
              <span class="kn-sofa-step__num">1</span>
              Choisissez la forme
            </p>
            <div class="kn-sofa-shapes" role="group" aria-label="Forme du canapé">
              <?php foreach ($shapes as $i => $shape):
                $k     = htmlspecialchars($shape['key']);
                $lbl   = htmlspecialchars($shape['label']);
                $icon  = isset($svgIcons[$shape['key']]) ? $svgIcons[$shape['key']] : $svgIcons['droit'];
              ?>
              <button type="button"
                class="kn-sofa-shape-card<?php echo $i === 0 ? ' is-selected' : ''; ?>"
                data-shape="<?php echo $k; ?>"
                aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                <span class="kn-sofa-icon"><?php echo $icon; ?></span>
                <span class="kn-sofa-shape-card__label"><?php echo $lbl; ?></span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Step 2: Seats -->
          <div class="kn-sofa-step">
            <p class="kn-sofa-step__label">
              <span class="kn-sofa-step__num">2</span>
              Nombre de places
            </p>
            <div class="kn-sofa-stepper" role="group" aria-label="Nombre de places">
              <button type="button" class="kn-sofa-stepper__btn kn-sofa-stepper__dec" aria-label="Moins">−</button>
              <div class="kn-sofa-stepper__count">
                <span class="kn-sofa-stepper__val" aria-live="polite"><?php echo (int)$firstShape['base_seats']; ?></span>
                <span class="kn-sofa-stepper__unit">places</span>
              </div>
              <button type="button" class="kn-sofa-stepper__btn kn-sofa-stepper__inc" aria-label="Plus">+</button>
            </div>
          </div>

          <!-- Step 3: Méridiennes -->
          <div class="kn-sofa-step">
            <p class="kn-sofa-step__label">
              <span class="kn-sofa-step__num">3</span>
              Méridienne(s)
            </p>
            <div class="kn-sofa-meridienne" role="group" aria-label="Méridiennes">
              <button type="button" class="kn-sofa-mer-btn is-selected" data-mer="0" aria-pressed="true">0</button>
              <button type="button" class="kn-sofa-mer-btn" data-mer="1" aria-pressed="false">1</button>
              <button type="button" class="kn-sofa-mer-btn" data-mer="2" aria-pressed="false">2</button>
            </div>
          </div>

        </div>

        <!-- Right: Live preview -->
        <div class="kn-sofa-preview" aria-hidden="true">
          <svg class="kn-sofa-preview__svg"
               id="<?php echo $blockId; ?>-svg"
               viewBox="0 0 300 200"
               preserveAspectRatio="xMidYMid meet"></svg>
        </div>

      </div><!-- /.kn-sofa-layout -->

      <!-- Result card -->
      <div class="kn-sofa-result">
        <div class="kn-sofa-result__left">
          <span class="kn-sofa-result__pill" id="<?php echo $blockId; ?>-pill"><?php echo htmlspecialchars($firstShape['label']); ?> · <?php echo (int)$firstShape['base_seats']; ?> places</span>
          <p class="kn-sofa-result__desc">Nettoyage vapeur en profondeur, à votre domicile.</p>
        </div>
        <div class="kn-sofa-result__right">
          <div class="kn-sofa-result__price-wrap">
            <span class="kn-sofa-result__price" id="<?php echo $blockId; ?>-price"><?php echo (int)$firstShape['base_price']; ?> €</span>
            <span class="kn-sofa-result__note">* estimatif</span>
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
  </div>
</section>

<script>
(function() {
  var cfg  = <?php echo $configJson; ?>;
  var BID  = <?php echo json_encode($blockId); ?>;
  var root = document.getElementById(BID);
  if (!root) return;

  var shapeCards = root.querySelectorAll('.kn-sofa-shape-card');
  var stepperVal = root.querySelector('.kn-sofa-stepper__val');
  var stepperDec = root.querySelector('.kn-sofa-stepper__dec');
  var stepperInc = root.querySelector('.kn-sofa-stepper__inc');
  var merBtns    = root.querySelectorAll('.kn-sofa-mer-btn');
  var priceEl    = document.getElementById(BID + '-price');
  var pillEl     = document.getElementById(BID + '-pill');
  var ctaEl      = document.getElementById(BID + '-cta');
  var svgEl      = document.getElementById(BID + '-svg');
  var isDark     = !!root.querySelector('.kn-sofa-widget--dark');

  var selectedShape = cfg.shapes[0];
  var seats         = selectedShape ? selectedShape.base_seats : 2;
  var meridians     = 0;
  var MIN_SEATS = 1, MAX_SEATS = 10;

  function getShape(key) {
    for (var i = 0; i < cfg.shapes.length; i++) {
      if (cfg.shapes[i].key === key) return cfg.shapes[i];
    }
    return cfg.shapes[0];
  }

  function calcPrice() {
    if (!selectedShape) return 0;
    return selectedShape.base_price
      + Math.max(0, seats - selectedShape.base_seats) * cfg.price_per_extra_seat
      + meridians * cfg.price_per_meridienne;
  }

  /* ── Cushion-based SVG preview ───────────────────────────── */
  function renderSofa(shapeKey, n, mer) {
    if (!svgEl) return;

    var CW  = 62, CH = 64;   // cushion width / height
    var BR  = 8;              // backrest bar thickness
    var GAP = 5;              // gap between cushions
    var RX  = 10;             // cushion corner radius
    var PAD = 10;             // outer padding

    // Colours
    var seatFill   = isDark ? '#2a2742' : '#f5f0eb';
    var seatStroke = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.07)';
    var brFill     = isDark ? '#ffd700' : '#0050ff';
    var merFill    = isDark ? '#322855' : '#ede9f8';
    var merBr      = isDark ? '#c4b0ff' : '#9b8aff';

    var clipIdx = 0;
    var defs = '', body = '';

    function cushion(x, y, w, h, side, isMer) {
      var cid  = 'ksc-' + (++clipIdx);
      var sf   = isMer ? merFill : seatFill;
      var bf   = isMer ? merBr   : brFill;
      defs += '<clipPath id="'+cid+'"><rect x="'+x+'" y="'+y+'" width="'+w+'" height="'+h+'" rx="'+RX+'"/></clipPath>';
      var s = '<rect x="'+x+'" y="'+y+'" width="'+w+'" height="'+h+'" fill="'+sf+'" rx="'+RX+'"'
            + ' stroke="'+seatStroke+'" stroke-width="1.5"/>';
      if (side === 'top')    s += '<rect x="'+x+'" y="'+y+'" width="'+w+'" height="'+BR+'" fill="'+bf+'" clip-path="url(#'+cid+')"/>';
      if (side === 'bottom') s += '<rect x="'+x+'" y="'+(y+h-BR)+'" width="'+w+'" height="'+BR+'" fill="'+bf+'" clip-path="url(#'+cid+')"/>';
      if (side === 'left')   s += '<rect x="'+x+'" y="'+y+'" width="'+BR+'" height="'+h+'" fill="'+bf+'" clip-path="url(#'+cid+')"/>';
      if (side === 'right')  s += '<rect x="'+(x+w-BR)+'" y="'+y+'" width="'+BR+'" height="'+h+'" fill="'+bf+'" clip-path="url(#'+cid+')"/>';
      return s;
    }

    var W, H, k = (shapeKey === 'fauteuil') ? 'droit' : shapeKey;
    var sn = (shapeKey === 'fauteuil') ? 1 : n;
    var mn = (shapeKey === 'fauteuil') ? 0 : mer;

    if (k === 'droit') {
      var sofaW = sn*(CW+GAP)-GAP;
      var merW  = mn*(CW+GAP);
      W = PAD*2 + sofaW + (mn>0 ? merW+GAP*2 : 0);
      H = PAD*2 + CH;
      for (var i=0;i<sn;i++) body += cushion(PAD+i*(CW+GAP), PAD, CW, CH, 'top', false);
      for (var i=0;i<mn;i++) body += cushion(PAD+sofaW+GAP*2+i*(CW+GAP), PAD, CW, CH, i===mn-1?'bottom':'none', true);
    }

    else if (k === 'angle') {
      var hN = Math.max(2, Math.ceil(sn*0.6));
      var vN = Math.max(1, sn - hN);
      var hW = hN*(CW+GAP)-GAP;
      var vH = vN*(CH+GAP)-GAP;
      W = PAD*2 + hW;
      H = PAD*2 + CH + GAP + vH;
      // horizontal top row (backrest top)
      for (var i=0;i<hN;i++) body += cushion(PAD+i*(CW+GAP), PAD, CW, CH, 'top', false);
      // vertical column aligned to last cushion of top row (backrest right)
      var vx = PAD+(hN-1)*(CW+GAP);
      for (var i=0;i<vN;i++) body += cushion(vx, PAD+CH+GAP+i*(CH+GAP), CW, CH, 'right', false);
      // méridienne: extends beyond horizontal row
      if (mn>0) {
        for (var i=0;i<mn;i++) body += cushion(PAD+hW+GAP*2+i*(CW+GAP), PAD, CW, CH, 'none', true);
        W = PAD*2+hW+GAP*2+mn*(CW+GAP)-GAP;
      }
    }

    else if (k === 'u') {
      var sideN = Math.max(1, Math.floor((sn-2)/2));
      var cN    = Math.max(2, sn - 2*sideN);
      var cRowW = cN*(CW+GAP)-GAP;
      var sH    = sideN*(CH+GAP)-GAP;
      W = PAD*2 + CW+GAP+cRowW+GAP+CW;
      H = PAD*2 + sH+GAP+CH;
      // left column (backrest left)
      for (var i=0;i<sideN;i++) body += cushion(PAD, PAD+i*(CH+GAP), CW, CH, 'left', false);
      // right column (backrest right)
      for (var i=0;i<sideN;i++) body += cushion(PAD+CW+GAP+cRowW+GAP, PAD+i*(CH+GAP), CW, CH, 'right', false);
      // bottom row (backrest bottom)
      for (var i=0;i<cN;i++) body += cushion(PAD+CW+GAP+i*(CW+GAP), PAD+sH+GAP, CW, CH, 'bottom', false);
    }

    svgEl.setAttribute('viewBox', '0 0 '+W+' '+H);
    svgEl.innerHTML = '<defs>'+defs+'</defs>'+body;
  }
  /* ─────────────────────────────────────────────────────────── */

  function updateUI() {
    var price = calcPrice();
    if (priceEl) priceEl.textContent = price + ' €';
    stepperDec.disabled = seats <= MIN_SEATS;
    stepperInc.disabled = seats >= MAX_SEATS;

    if (pillEl && selectedShape) {
      var merTxt = meridians > 0 ? ' + '+meridians+' méridienne'+(meridians>1?'s':'') : '';
      pillEl.textContent = selectedShape.label + ' · ' + seats + ' place' + (seats>1?'s':'') + merTxt;
    }
    if (selectedShape && ctaEl) {
      var url = selectedShape.cta_url || '#';
      var sep = url.indexOf('?') === -1 ? '?' : '&';
      ctaEl.href = url + sep + 'places=' + seats + '&meridienne=' + meridians;
    }
    renderSofa(selectedShape ? selectedShape.key : 'droit', seats, meridians);
  }

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

  stepperDec.addEventListener('click', function() { if (seats>MIN_SEATS){seats--;stepperVal.textContent=seats;updateUI();} });
  stepperInc.addEventListener('click', function() { if (seats<MAX_SEATS){seats++;stepperVal.textContent=seats;updateUI();} });

  merBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      merBtns.forEach(function(b){ b.classList.remove('is-selected'); b.setAttribute('aria-pressed','false'); });
      btn.classList.add('is-selected');
      btn.setAttribute('aria-pressed','true');
      meridians = parseInt(btn.dataset.mer,10)||0;
      updateUI();
    });
  });

  updateUI();
})();
</script>
