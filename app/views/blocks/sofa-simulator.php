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

  /* ── Cushion-grid SVG preview (inspired by reference) ───── */
  function renderSofa(shapeKey, n, mer) {
    if (!svgEl) return;

    var VW = 300, VH = 200;
    var BAR  = 5;    // backrest bar thickness
    var BOFF = 4;    // protrusion outside seat
    var RX   = 8;    // seat corner radius

    var seatFill   = isDark ? '#2d1f35' : '#F4E3EA';
    var seatStroke = isDark ? '#3d2f45' : '#E3C4D2';
    var backFill   = isDark ? '#ffd700' : '#586FF3';
    var merFill    = isDark ? '#1a2535' : '#dde6f8';
    var merStroke  = isDark ? '#2a3545' : '#b8c8e8';
    var merBack    = isDark ? '#7799ff' : '#3355cc';

    // Build cell list: [{r, c, back, mer}]
    function cells(shape, sn, mn) {
      var out = [];
      if (shape === 'fauteuil') {
        out.push({r:0,c:0,back:'top',mer:false});
        return out;
      }
      if (shape === 'droit') {
        for (var c=0;c<sn;c++) out.push({r:0,c:c,back:'top',mer:false});
        for (var r=1;r<=mn;r++) out.push({r:r,c:sn-1,back:'right',mer:true});
        return out;
      }
      if (shape === 'angle') {
        // ~34% vertical return from right corner (same ratio as reference)
        var v = Math.min(Math.max(Math.round(sn*0.34),1), sn-1);
        var h = sn - v;
        for (var c=0;c<h;c++) out.push({r:0,c:c,back:'top',mer:false});
        for (var r=1;r<=v;r++) out.push({r:r,c:h-1,back:'right',mer:false});
        for (var r=v+1;r<=v+mn;r++) out.push({r:r,c:h-1,back:'right',mer:true});
        return out;
      }
      if (shape === 'u') {
        var arm = Math.min(Math.max(Math.round((sn-1)/3),1),3);
        arm = Math.min(arm, Math.floor((sn-1)/2));
        arm = Math.max(arm,1);
        var base = Math.max(1, sn - 2*arm);
        for (var c=0;c<base;c++) out.push({r:0,c:c,back:'top',mer:false});
        for (var r=1;r<=arm;r++) out.push({r:r,c:0,back:'left',mer:false});
        for (var r=1;r<=arm;r++) out.push({r:r,c:base-1,back:'right',mer:false});
        return out;
      }
      return out;
    }

    var isFauteuil = (shapeKey === 'fauteuil');
    var list = cells(shapeKey, isFauteuil ? 1 : n, isFauteuil ? 0 : mer);

    // Bounding box
    var maxR=0, maxC=0;
    for (var i=0;i<list.length;i++) {
      if (list[i].r>maxR) maxR=list[i].r;
      if (list[i].c>maxC) maxC=list[i].c;
    }
    var rows=maxR+1, cols=maxC+1;

    // Adaptive seat size
    var maxDim = Math.max(rows, cols);
    var CELL = Math.max(30, Math.min(54, Math.floor(Math.min(VW-BOFF*4, VH-BOFF*4) / maxDim) - 2));
    var GAP  = Math.max(3, Math.floor(CELL*0.1));
    var SW   = CELL - GAP;

    var totalW = cols*CELL + GAP;
    var totalH = rows*CELL + GAP;
    var ox = Math.max(BOFF+2, Math.floor((VW - totalW) / 2));
    var oy = Math.max(BOFF+2, Math.floor((VH - totalH) / 2));

    var body = '';
    for (var i=0;i<list.length;i++) {
      var cell = list[i];
      var x = ox + GAP + cell.c*CELL;
      var y = oy + GAP + cell.r*CELL;
      var sf = cell.mer ? merFill : seatFill;
      var ss = cell.mer ? merStroke : seatStroke;
      var bf = cell.mer ? merBack : backFill;

      // Seat cushion
      body += '<rect x="'+x+'" y="'+y+'" width="'+SW+'" height="'+SW+'"'
            + ' fill="'+sf+'" rx="'+RX+'" stroke="'+ss+'" stroke-width="1.5"/>';

      // Backrest bar protruding outside
      var bx=0,by=0,bw=0,bh=0;
      if (cell.back==='top')    { bx=x+4; by=y-BOFF; bw=SW-8; bh=BAR; }
      if (cell.back==='bottom') { bx=x+4; by=y+SW+BOFF-BAR; bw=SW-8; bh=BAR; }
      if (cell.back==='left')   { bx=x-BOFF; by=y+4; bw=BAR; bh=SW-8; }
      if (cell.back==='right')  { bx=x+SW+BOFF-BAR; by=y+4; bw=BAR; bh=SW-8; }
      if (cell.back && !cell.mer) {
        body += '<rect x="'+bx+'" y="'+by+'" width="'+bw+'" height="'+bh+'"'
              + ' fill="'+bf+'" rx="2"/>';
      }
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
