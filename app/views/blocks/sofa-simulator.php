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

  /* ── Floor-plan SVG preview ─────────────────────────────── */
  function renderSofa(shapeKey, n, mer) {
    if (!svgEl) return;

    var VW = 300, VH = 200;
    var BR = 13;   // backrest bar thickness
    var SD = 46;   // seat depth (front to back)
    var AW = 13;   // armrest width
    var RX = 6;    // corner radius
    var DIV_W = 1.5;

    var seatFill = isDark ? '#2a2742' : '#ede8df';
    var armFill  = isDark ? '#1e1b30' : '#d8d0c6';
    var backFill = isDark ? '#ffd700' : '#0050ff';
    var divCol   = isDark ? 'rgba(255,255,255,.12)' : 'rgba(0,0,0,.1)';
    var stk      = isDark ? 'rgba(255,255,255,.18)' : 'rgba(0,0,0,.13)';
    var merFill  = isDark ? '#2a1e3d' : '#ede9f8';
    var merBack  = isDark ? '#c4b0ff' : '#9b8aff';
    var merArm   = isDark ? '#211730' : '#dbd6f0';

    var body = '';

    function r(x,y,w,h,fill,rx,sw) {
      body += '<rect x="'+x+'" y="'+y+'" width="'+w+'" height="'+h+'"'
            + ' fill="'+fill+'" rx="'+(rx||0)+'"'
            + (sw ? ' stroke="'+stk+'" stroke-width="'+sw+'"' : '')+'/>';
    }
    function vl(x,y1,y2) { body += '<line x1="'+x+'" y1="'+y1+'" x2="'+x+'" y2="'+y2+'" stroke="'+divCol+'" stroke-width="'+DIV_W+'"/>'; }
    function hl(x1,x2,y)  { body += '<line x1="'+x1+'" y1="'+y+'" x2="'+x2+'" y2="'+y+'" stroke="'+divCol+'" stroke-width="'+DIV_W+'"/>'; }

    var isFauteuil = (shapeKey === 'fauteuil');
    var actualN    = isFauteuil ? 1 : n;
    var actualMer  = isFauteuil ? 0 : mer;

    if (shapeKey === 'droit' || isFauteuil) {
      var sw = isFauteuil ? 80 : Math.min(68, Math.max(38, (VW - AW*2 - 30) / actualN));
      var totalW = AW + actualN*sw + AW;
      var merH   = actualMer > 0 ? (BR + SD) : 0;
      var totalH = BR + SD + merH;
      var ox = (VW - totalW) / 2;
      var oy = (VH - totalH) / 2;
      // Main seat body
      r(ox+AW, oy, actualN*sw, BR+SD, seatFill, RX, 1.5);
      // Backrest bar
      r(ox+AW, oy, actualN*sw, BR, backFill, RX);
      // Left armrest
      r(ox, oy+BR, AW, SD, armFill, RX, 1.5);
      // Right armrest
      r(ox+AW+actualN*sw, oy+BR, AW, SD, armFill, RX, 1.5);
      // Seat dividers
      for (var i=1; i<actualN; i++) vl(ox+AW+i*sw, oy+BR, oy+BR+SD);
      // Méridienne: extends downward from right end
      if (actualMer > 0) {
        var mx = ox+AW+(actualN-1)*sw;
        var my = oy+BR+SD;
        r(mx, my, sw, actualMer*(BR+SD), merFill, RX, 1.5);
        r(mx+sw-BR, my, BR, actualMer*(BR+SD), merBack, RX);
        r(ox+AW+actualN*sw, my, AW, Math.min(actualMer*(BR+SD), SD), merArm, RX, 1.5);
        for (var i=1; i<actualMer; i++) hl(mx, mx+sw, my+i*(BR+SD));
      }

    } else if (shapeKey === 'angle') {
      var hN = Math.max(2, Math.ceil(actualN*0.6));
      var vN = Math.max(1, actualN - hN);
      var sw = Math.min(62, Math.max(36, (VW - AW*2 - 20) / (hN+0.5)));
      var hW = hN*sw;
      var vH = vN*sw;
      var totalW = AW + hW;
      var totalH = BR + SD + vH + AW;
      var ox = (VW - totalW - AW) / 2;
      var oy = (VH - totalH) / 2;
      // Horizontal arm (backrest top), left armrest
      r(ox+AW, oy, hW, BR+SD, seatFill, RX, 1.5);
      r(ox+AW, oy, hW, BR, backFill, RX);
      r(ox, oy+BR, AW, SD, armFill, RX, 1.5);
      for (var i=1; i<hN; i++) vl(ox+AW+i*sw, oy+BR, oy+BR+SD);
      // Vertical arm (backrest right), starts exactly where horizontal arm ends
      var vx = ox+AW+(hN-1)*sw;
      var vy = oy+BR+SD;
      r(vx, vy, sw+AW, vH, seatFill, RX, 1.5);
      r(vx+sw, vy, AW, vH, armFill, RX, 1.5);
      r(vx+sw-BR, vy, BR, vH, backFill, RX);
      for (var i=1; i<vN; i++) hl(vx, vx+sw, vy+i*sw);
      // Bottom armrest cap on vertical arm
      r(vx, vy+vH, sw, AW, armFill, RX, 1.5);

    } else if (shapeKey === 'u') {
      var sideN = Math.max(1, Math.floor((actualN-2)/2));
      var cN    = Math.max(2, actualN - 2*sideN);
      var sw    = Math.min(54, Math.max(32, (VW - 40) / (cN+2)));
      var armW  = sw + AW;
      var totalW = armW + cN*sw + armW;
      var totalH = sideN*sw + (BR+SD) + AW;
      var ox = (VW - totalW) / 2;
      var oy = (VH - totalH) / 2;
      // Left arm (backrest left)
      r(ox, oy, armW, totalH, seatFill, RX, 1.5);
      r(ox, oy, AW, totalH, armFill, RX, 1.5);
      r(ox+AW, oy, BR, totalH-AW, backFill, RX);
      for (var i=1; i<sideN; i++) hl(ox+AW, ox+armW, oy+i*sw);
      // Right arm (backrest right)
      var rx2 = ox+armW+cN*sw;
      r(rx2, oy, armW, totalH, seatFill, RX, 1.5);
      r(rx2+sw, oy, AW, totalH, armFill, RX, 1.5);
      r(rx2+sw-BR, oy, BR, totalH-AW, backFill, RX);
      for (var i=1; i<sideN; i++) hl(rx2, rx2+sw, oy+i*sw);
      // Bottom section (backrest bottom), fills the gap between arms
      var by = oy+sideN*sw;
      r(ox+armW, by, cN*sw, BR+SD, seatFill, 0, 1.5);
      r(ox+armW, by+SD, cN*sw, BR, backFill);
      for (var i=1; i<cN; i++) vl(ox+armW+i*sw, by, by+SD+BR);
      // Bottom armrest caps
      r(ox+AW, by+SD, sw, AW, armFill, RX, 1.5);
      r(rx2, by+SD, sw, AW, armFill, RX, 1.5);
    }

    svgEl.setAttribute('viewBox', '0 0 '+VW+' '+VH);
    svgEl.innerHTML = body;
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
