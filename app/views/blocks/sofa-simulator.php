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

      <!-- Live preview -->
      <div class="kn-sofa-preview" aria-hidden="true">
        <svg class="kn-sofa-preview__svg" id="<?php echo $blockId; ?>-svg" viewBox="0 0 300 130" preserveAspectRatio="xMidYMid meet"></svg>
        <p class="kn-sofa-preview__caption" id="<?php echo $blockId; ?>-caption"></p>
      </div>

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
  var cfg   = <?php echo $configJson; ?>;
  var BID   = <?php echo json_encode($blockId); ?>;
  var root  = document.getElementById(BID);
  if (!root) return;

  var shapeCards = root.querySelectorAll('.kn-sofa-shape-card');
  var stepperVal = root.querySelector('.kn-sofa-stepper__val');
  var stepperDec = root.querySelector('.kn-sofa-stepper__dec');
  var stepperInc = root.querySelector('.kn-sofa-stepper__inc');
  var merBtns    = root.querySelectorAll('.kn-sofa-mer-btn');
  var priceEl    = root.querySelector('.kn-sofa-result__price');
  var ctaEl      = document.getElementById(BID + '-cta');
  var svgEl      = document.getElementById(BID + '-svg');
  var capEl      = document.getElementById(BID + '-caption');
  var isDark     = root.querySelector('.kn-sofa-widget--dark') !== null;

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

  /* ─── SVG sofa renderer ───────────────────────────── */
  function renderSofa(shape, n, mer) {
    if (!svgEl) return;
    var fc  = isDark ? '#ffd700'              : '#0050ff';
    var sc  = isDark ? 'rgba(255,215,0,.14)'  : 'rgba(0,80,255,.11)';
    var sc2 = isDark ? 'rgba(255,215,0,.07)'  : 'rgba(0,80,255,.06)';
    var dc  = isDark ? 'rgba(255,215,0,.45)'  : 'rgba(0,80,255,.35)';
    var pad = 6, bk = 28, arm = 24, sh = 82, R = 10;

    function rr(x,y,w,h,fill,stroke,rx) {
      rx = rx !== undefined ? rx : R;
      return '<rect x="'+(x+pad)+'" y="'+(y+pad)+'" width="'+w+'" height="'+h
        +'" fill="'+fill+'"'+(stroke?' stroke="'+stroke+'" stroke-width="2.5"':'')
        +' rx="'+rx+'"/>';
    }
    function ln(x1,y1,x2,y2) {
      return '<line x1="'+(x1+pad)+'" y1="'+(y1+pad)+'" x2="'+(x2+pad)+'" y2="'+(y2+pad)
        +'" stroke="'+dc+'" stroke-width="2" stroke-linecap="round"/>';
    }

    var k = shape === 'fauteuil' ? 'droit' : shape;
    var sn = shape === 'fauteuil' ? 1 : n;
    var mn = shape === 'fauteuil' ? 0 : mer;
    var html = '', W, H;

    if (k === 'droit') {
      var merW = mn > 0 ? 60 + (mn-1)*30 : 0;
      var sofaW = Math.max(shape==='fauteuil'?90:120, sn*42 + arm*2);
      W = sofaW + (merW>0 ? merW+8 : 0);
      H = bk + sh;
      // body
      html += rr(0,0,sofaW,H,sc,fc);
      // backrest
      html += rr(0,0,sofaW,bk,fc,'',R);
      // left arm
      html += rr(0,bk,arm,sh,fc,'',0);
      // right arm
      html += rr(sofaW-arm,bk,arm,sh,fc,'',0);
      // cushion dividers
      var inner = sofaW - arm*2;
      var cw = inner / sn;
      for (var i=1;i<sn;i++) html += ln(arm+i*cw, bk+8, arm+i*cw, H-8);
      // méridienne
      if (mn>0) {
        var mx = sofaW+8;
        html += rr(mx,bk,merW,sh,sc2,fc,0);
        html += rr(mx,H-bk,merW,bk,fc,'',0);
        if (mn>1) {
          html += rr(mx,bk+sh/2,merW,sh/2-bk/2,sc2,'',0);
          html += ln(mx,bk+sh/2,mx+merW,bk+sh/2);
        }
        W = mx + merW;
      }
    }

    else if (k === 'angle') {
      var hS = Math.max(2, Math.ceil(sn*0.6));
      var vS = Math.max(1, sn - hS);
      var hW = Math.max(110, hS*42 + arm);
      var vH2 = Math.max(90, vS*42 + arm);
      var unit = bk + sh;
      W = unit + hW; H = vH2 + unit;

      // vertical arm (left)
      html += rr(0,0,unit,vH2,sc,fc);
      html += rr(0,0,bk,vH2,fc,'',R);
      html += rr(0,vH2-arm,unit,arm,fc,'',0);
      var vc = (vH2-arm)/vS;
      for (var i=1;i<vS;i++) html += ln(bk+6,i*vc,unit-6,i*vc);

      // horizontal arm (bottom)
      html += rr(unit,vH2-unit,hW,unit,sc,fc);
      html += rr(unit,vH2-unit,hW,bk,fc,'',R);
      html += rr(unit+hW-arm,vH2-unit,arm,unit,fc,'',0);
      var hc = (hW-arm)/hS;
      for (var i=1;i<hS;i++) html += ln(unit+i*hc,vH2-unit+bk+6,unit+i*hc,H-6);

      // corner fill
      html += rr(0,vH2-unit,unit,unit,sc,'none',0);

      // méridienne
      if (mn>0) {
        var merW2 = 55+(mn-1)*28;
        html += rr(unit+hW+6,vH2-unit+bk,merW2,sh,sc2,fc,0);
        html += rr(unit+hW+6,H-bk,merW2,bk,fc,'',0);
        W = unit+hW+6+merW2;
      }
    }

    else if (k === 'u') {
      var sideS  = Math.max(1, Math.floor((sn-2)/2));
      var cS     = Math.max(2, sn - 2*sideS);
      var cW2    = Math.max(90, cS*40);
      var sH2    = Math.max(80, sideS*42 + arm);
      var unit2  = bk + sh;
      W = unit2*2 + cW2; H = sH2 + unit2;

      // left
      html += rr(0,0,unit2,sH2,sc,fc);
      html += rr(0,0,bk,sH2,fc,'',R);
      html += rr(0,sH2-arm,unit2,arm,fc,'',0);
      var ls2=(sH2-arm)/sideS;
      for (var i=1;i<sideS;i++) html += ln(bk+6,i*ls2,unit2-6,i*ls2);

      // right
      html += rr(W-unit2,0,unit2,sH2,sc,fc);
      html += rr(W-bk,0,bk,sH2,fc,'',R);
      html += rr(W-unit2,sH2-arm,unit2,arm,fc,'',0);
      for (var i=1;i<sideS;i++) html += ln(W-unit2+6,i*ls2,W-bk-6,i*ls2);

      // center bottom
      html += rr(unit2,sH2,cW2,unit2,sc,fc);
      html += rr(unit2,sH2,cW2,bk,fc,'',R);
      var cc2=cW2/cS;
      for (var i=1;i<cS;i++) html += ln(unit2+i*cc2,sH2+bk+6,unit2+i*cc2,H-6);

      // corners
      html += rr(0,sH2,unit2,unit2,sc,'none',0);
      html += rr(W-unit2,sH2,unit2,unit2,sc,'none',0);
    }

    svgEl.setAttribute('viewBox', '0 0 '+(W+pad*2)+' '+(H+pad*2));
    svgEl.innerHTML = html;

    // Caption
    if (capEl) {
      var merLabel = mn===0 ? '' : (mn===1 ? ' + 1 méridienne' : ' + 2 méridiennes');
      capEl.textContent = (selectedShape ? selectedShape.label : '') + ' — ' + sn + ' place' + (sn>1?'s':'') + merLabel;
    }
  }
  /* ─────────────────────────────────────────────────── */

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
    renderSofa(selectedShape ? selectedShape.key : 'droit', seats, meridians);
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
