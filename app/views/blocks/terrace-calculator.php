<?php
require_once __DIR__ . '/_block_helpers.php';

static $knTcCount = 0;
$knTcCount++;
$uid      = 'kn-tc-' . $knTcCount;
$calcId   = $knTcCount === 1 ? 'calculateur' : $uid;
$formId   = $knTcCount === 1 ? 'rdv' : $uid . '-rdv';

$t = function ($key, $default) use ($block) {
    return isset($block[$key]) && trim((string) $block[$key]) !== '' ? (string) $block[$key] : $default;
};
$num = function ($v, $default) {
    $v = str_replace(',', '.', trim((string) $v));
    return is_numeric($v) ? (float) $v : $default;
};
$fmt = function ($n) { return rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ','); };

$minPrice  = $num(isset($block['min_price']) ? $block['min_price'] : '', 95.0);
$minM2     = $num(isset($block['min_m2']) ? $block['min_m2'] : '', 15.0);
$sliderMax = (int) $num(isset($block['slider_max']) ? $block['slider_max'] : '', 200);
if ($sliderMax < 20) $sliderMax = 200;

$defaultSurfaces = array(
    array('label' => 'Carrelage / Grès cérame', 'detail' => 'Surface lisse',     'icon' => 'terrasse', 'rate' => '6'),
    array('label' => 'Béton brut & Dalle',      'detail' => 'Terrasse, allée',   'icon' => 'terrasse', 'rate' => '7'),
    array('label' => 'Pavés & Autobloquants',   'detail' => 'Joints inclus',     'icon' => 'terrasse', 'rate' => '7'),
    array('label' => 'Bois / Résine',           'detail' => 'Nettoyage manuel et basse pression', 'icon' => 'terrasse', 'rate' => '9'),
);
$surfaces = array();
$rawS = isset($block['surfaces']) && is_array($block['surfaces']) ? $block['surfaces'] : $defaultSurfaces;
foreach ($rawS as $s) {
    if (!is_array($s) || trim(isset($s['label']) ? (string) $s['label'] : '') === '') continue;
    $surfaces[] = array(
        'label'  => (string) $s['label'],
        'detail' => isset($s['detail']) ? (string) $s['detail'] : '',
        'icon'   => !empty($s['icon']) ? (string) $s['icon'] : 'terrasse',
        'rate'   => $num(isset($s['rate']) ? $s['rate'] : '', 0.0),
    );
}
if (!$surfaces) return;

$defaultOptions = array(array('label' => 'Anti-mousse longue durée', 'desc' => 'Protection 12–18 mois contre le retour des mousses et algues', 'rate' => '1'));
$options = array();
$rawO = isset($block['options']) && is_array($block['options']) ? $block['options'] : $defaultOptions;
foreach ($rawO as $o) {
    if (!is_array($o) || trim(isset($o['label']) ? (string) $o['label'] : '') === '') continue;
    $options[] = array('label' => (string) $o['label'], 'desc' => isset($o['desc']) ? (string) $o['desc'] : '', 'rate' => $num(isset($o['rate']) ? $o['rate'] : '', 0.0));
}

$eyebrow   = $t('eyebrow', 'CALCULATEUR GRATUIT');
$h2        = $t('h2', 'Combien coûte le nettoyage de ma terrasse ?');
$intro     = $t('intro', 'Obtenez une estimation en 30 secondes — sans engagement. Devis confirmé sous 24h.');
$s1        = $t('step1_title', 'Quel est le type de revêtement ?');
$s2        = $t('step2_title', 'Quelle est la superficie de votre terrasse ?');
$s3        = $t('step3_title', 'Options supplémentaires');
$resLabel  = $t('result_label', 'Estimation de votre devis');
$ctaLabel  = $t('cta_label', 'Demander un RDV avec ce devis');
$note      = $t('note', '* Prix indicatifs. Le devis définitif est confirmé sous 24h après votre demande de RDV. Tarif minimum ' . $fmt($minPrice) . ' € appliqué pour les petites surfaces (moins de ' . $fmt($minM2) . ' m²).');
$badgesRaw = $t('badges', "Déplacement offert\nPaiement après prestation\nRéponse sous 24h");
$badges    = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $badgesRaw)), 'strlen'));

$formH2    = $t('form_h2', 'Votre devis est prêt. Demandez votre RDV !');
$formIntro = $t('form_intro', 'Remplissez le formulaire ci-dessous. Nous vous recontactons sous 24h pour fixer la date de l\'intervention.');
$formTitle = $t('form_title', 'Fixons un RDV — nous vous rappelons sous 24h');
$submitLbl = $t('submit_label', 'Envoyer ma demande de RDV');
$formNote  = $t('form_note', 'Gratuit · Sans engagement · Paiement uniquement après la prestation');
$consent   = $t('consent_text', 'J\'accepte que mes données soient utilisées pour répondre à ma demande.');
$okTitle   = $t('success_title', 'Demande envoyée avec succès !');
$okText    = $t('success_text', 'Merci {nom} — nous avons bien reçu votre demande. Notre équipe vous contactera au {tel} sous 24h pour fixer la date de l\'intervention.');
$privUrl   = $t('privacy_url', isset($contact_channels['privacy_url']) ? (string) $contact_channels['privacy_url'] : '');
$privLabel = $t('privacy_label', 'Politique de confidentialité');
$formEyebrow = $t('form_eyebrow', 'VOTRE DEVIS EST PRÊT');
$chanTitle   = $t('channels_title', 'Ou contactez-nous directement');
$waText      = $t('whatsapp_text', 'Bonjour, je souhaite un devis pour le nettoyage de ma terrasse.');
$formBg      = isset($block['form_bg']) && in_array($block['form_bg'], array('white','alt','cream','rose','blue','night'), true) ? $block['form_bg'] : 'white';
$reassure    = isset($block['reassure']) && is_array($block['reassure']) ? $block['reassure'] : array(
    array('icon' => 'clock',        'text' => 'Réponse sous 24 h ouvrées'),
    array('icon' => 'shield-check', 'text' => 'Paiement uniquement après la prestation'),
    array('icon' => 'lock',         'text' => 'Devis gratuit et sans engagement'),
);
$chData      = knContactChannels($block, isset($contact_channels) && is_array($contact_channels) ? $contact_channels : array(), $waText);
$channels    = $chData['channels'];
$socials     = $chData['socials'];
$socialLabels = array('facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'twitter' => 'X (Twitter)');

$defM2   = 20.0;
$first   = $surfaces[0];
$initial = max($minPrice, $first['rate'] * $defM2);

$stamp   = \App\Controllers\ContactController::stamp();
$token   = \App\Controllers\ContactController::token();
$pageUrl = isset($_SERVER['REQUEST_URI']) ? (string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';

$cfg = array(
    'surfaces' => $surfaces,
    'options'  => $options,
    'minPrice' => $minPrice,
    'minM2'    => $minM2,
    'sliderMax'=> $sliderMax,
    'okText'   => $okText,
);

// Balisage Google « Service », dérivé de la configuration du calculateur
$schemaOn = !isset($block['schema_enabled']) || !empty($block['schema_enabled']);
$lowRate  = null;
foreach ($surfaces as $s) { if ($s['rate'] > 0 && ($lowRate === null || $s['rate'] < $lowRate)) $lowRate = $s['rate']; }
$rdvBlock = $block; $rdvBlock['bg'] = $formBg;
?>
<section class="<?php echo blockClasses($block, 'cream', 'kn-tc'); ?>" id="<?php echo $calcId; ?>" data-tc-root>
  <div class="container kn-tc__inner">
    <header class="kn-tc__head">
      <span class="kn-eyebrow"><?php echo htmlspecialchars($eyebrow); ?></span>
      <h2 class="kn-section__title"><?php echo htmlspecialchars($h2); ?></h2>
      <p class="kn-tc__intro"><?php echo htmlspecialchars($intro); ?></p>
    </header>

    <div class="kn-tc__card">
      <div class="kn-tc__step"><span class="kn-tc__num">1</span><h3><?php echo htmlspecialchars($s1); ?></h3></div>
      <div class="kn-tc__surfaces" role="radiogroup" aria-label="<?php echo htmlspecialchars($s1); ?>">
        <?php foreach ($surfaces as $i => $s): ?>
        <button type="button" class="kn-tc__pill<?php echo $i === 0 ? ' is-selected' : ''; ?>" role="radio" aria-checked="<?php echo $i === 0 ? 'true' : 'false'; ?>" data-surface="<?php echo $i; ?>">
          <span class="kn-tc__pill-ico" aria-hidden="true"><?php echo knIcon($s['icon'], array('size' => 22, 'fallback' => 'sparkles')); ?></span>
          <span class="kn-tc__pill-txt"><strong><?php echo htmlspecialchars($s['label']); ?></strong><?php if ($s['detail'] !== ''): ?><small><?php echo htmlspecialchars($s['detail']); ?></small><?php endif; ?></span>
          <span class="kn-tc__pill-price"><?php echo $fmt($s['rate']); ?> €/m²</span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="kn-tc__card">
      <div class="kn-tc__step"><span class="kn-tc__num">2</span><h3><?php echo htmlspecialchars($s2); ?></h3></div>
      <div class="kn-tc__tabs" role="tablist">
        <button type="button" class="kn-tc__tab is-active" data-mode="dim" role="tab" aria-selected="true">Je connais les dimensions</button>
        <button type="button" class="kn-tc__tab" data-mode="m2" role="tab" aria-selected="false">J'entre directement les m²</button>
      </div>

      <div data-pane="dim">
        <div class="kn-tc__dims">
          <label><span class="kn-tc__lab">Longueur (m)</span><input type="number" inputmode="decimal" value="5" min="0.5" max="100" step="0.5" data-len></label>
          <span class="kn-tc__x" aria-hidden="true">×</span>
          <label><span class="kn-tc__lab">Largeur (m)</span><input type="number" inputmode="decimal" value="4" min="0.5" max="100" step="0.5" data-wid></label>
          <span class="kn-tc__x" aria-hidden="true">=</span>
          <div class="kn-tc__area"><span class="kn-tc__lab">Superficie</span><strong data-area>20 m²</strong></div>
        </div>
      </div>
      <div data-pane="m2" hidden>
        <label class="kn-tc__direct"><span class="kn-tc__lab">Surface totale (m²)</span><input type="number" inputmode="decimal" value="20" min="1" max="500" step="1" data-direct></label>
      </div>

      <div class="kn-tc__slider">
        <span class="kn-tc__lab">Ou ajustez avec le curseur</span>
        <div class="kn-tc__slider-row">
          <input type="range" min="5" max="<?php echo $sliderMax; ?>" step="5" value="20" data-slider aria-label="Superficie en m²">
          <strong data-slider-val>20 m²</strong>
        </div>
      </div>
      <p class="kn-tc__alert" data-alert hidden>Surface inférieure à <?php echo $fmt($minM2); ?> m² : un tarif minimum de <?php echo $fmt($minPrice); ?> € s'applique.</p>
    </div>

    <?php if ($options): ?>
    <div class="kn-tc__card">
      <div class="kn-tc__step"><span class="kn-tc__num">3</span><h3><?php echo htmlspecialchars($s3); ?> <small>(facultatif)</small></h3></div>
      <div class="kn-tc__options">
        <?php foreach ($options as $i => $o): ?>
        <button type="button" class="kn-tc__option" role="checkbox" aria-checked="false" data-option="<?php echo $i; ?>">
          <span class="kn-tc__check" aria-hidden="true"><?php echo knIcon('check', array('size' => 14)); ?></span>
          <span class="kn-tc__opt-txt"><strong><?php echo htmlspecialchars($o['label']); ?></strong><?php if ($o['desc'] !== ''): ?><small><?php echo htmlspecialchars($o['desc']); ?></small><?php endif; ?></span>
          <span class="kn-tc__opt-price">+<?php echo $fmt($o['rate']); ?> €/m²</span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="kn-tc__result" aria-live="polite">
      <span class="kn-tc__result-label"><?php echo htmlspecialchars($resLabel); ?></span>
      <div class="kn-tc__price"><span data-total><?php echo (int) round($initial); ?></span><small>€</small></div>
      <p class="kn-tc__ttc" data-ttc>TTC · pour 20 m² de revêtement</p>
      <div class="kn-tc__lines" data-lines></div>
      <?php if ($badges): ?>
      <ul class="kn-tc__badges">
        <?php foreach ($badges as $b): ?><li><?php echo htmlspecialchars($b); ?></li><?php endforeach; ?>
      </ul>
      <?php endif; ?>
      <a href="#<?php echo $formId; ?>" class="kn-btn kn-tc__cta"><?php echo knIcon('calendar', array('size' => 16)); ?><?php echo htmlspecialchars($ctaLabel); ?></a>
    </div>
    <p class="kn-tc__note"><?php echo htmlspecialchars($note); ?></p>
  </div>
</section>

<section class="<?php echo blockClasses($rdvBlock, 'white', 'kn-contact kn-tc-rdv'); ?>" id="<?php echo $formId; ?>" data-tc-form-root>
  <div class="container">
    <div class="kn-contact__grid">

      <div class="kn-contact__info">
        <span class="kn-eyebrow"><?php echo htmlspecialchars($formEyebrow); ?></span>
        <h2 class="kn-section__title"><?php echo htmlspecialchars($formH2); ?></h2>
        <p class="kn-contact__intro"><?php echo htmlspecialchars($formIntro); ?></p>

        <div class="kn-tc-rdv__resume" data-resume>
          <div><span class="kn-tc__lab">Votre estimation</span><strong data-resume-price>—</strong><small data-resume-detail></small></div>
          <span class="kn-tc-rdv__resume-opt" data-resume-opts></span>
        </div>

        <?php if ($reassure): ?>
        <ul class="kn-contact__reassure">
          <?php foreach ($reassure as $r):
            $txt = is_array($r) ? (isset($r['text']) ? (string) $r['text'] : '') : (string) $r;
            if (trim($txt) === '') continue;
            $ico = is_array($r) && !empty($r['icon']) ? $r['icon'] : 'check';
          ?>
          <li><span class="kn-contact__reassure-ico" aria-hidden="true"><?php echo knIcon($ico, array('size' => 18, 'fallback' => 'check')); ?></span><span><?php echo htmlspecialchars($txt); ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php if ($channels): ?>
        <div class="kn-contact__channels">
          <p class="kn-contact__channels-title"><?php echo htmlspecialchars($chanTitle); ?></p>
          <div class="kn-contact__chan-list">
            <?php foreach ($channels as $c): ?>
            <a class="kn-contact__chan kn-contact__chan--<?php echo $c['cls']; ?>" href="<?php echo htmlspecialchars($c['href']); ?>"<?php echo $c['ext'] ? ' target="_blank" rel="noopener"' : ''; ?>>
              <?php echo knIcon($c['icon'], array('size' => 20)); ?><span><?php echo htmlspecialchars($c['label']); ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($socials): ?>
        <div class="kn-contact__socials" aria-label="Nos réseaux sociaux">
          <?php foreach ($socials as $net => $url): if (!isset($socialLabels[$net])) continue; ?>
          <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo htmlspecialchars($socialLabels[$net]); ?>" title="<?php echo htmlspecialchars($socialLabels[$net]); ?>"><?php echo knIcon($net, array('size' => 20)); ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="kn-contact__card">
        <div class="kn-contact__success kn-tc-rdv__success" tabindex="-1" role="status" hidden>
          <span class="kn-contact__success-ico" aria-hidden="true"><?php echo knIcon('check', array('size' => 28)); ?></span>
          <h3><?php echo htmlspecialchars($okTitle); ?></h3>
          <p data-success-text></p>
        </div>

        <form class="kn-contact__form kn-tc-rdv__form" method="post" action="/contact/send" novalidate data-tc-form>
          <h3 class="kn-contact__form-title"><?php echo htmlspecialchars($formTitle); ?></h3>
          <input type="hidden" name="_token" value="<?php echo htmlspecialchars($token); ?>">
          <input type="hidden" name="ts" value="<?php echo htmlspecialchars($stamp['ts']); ?>">
          <input type="hidden" name="sig" value="<?php echo htmlspecialchars($stamp['sig']); ?>">
          <input type="hidden" name="page" value="<?php echo htmlspecialchars($pageUrl); ?>">
          <div class="kn-contact__trap" aria-hidden="true"><label>Ne pas remplir<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

          <div class="kn-contact__row">
            <div class="kn-contact__field"><label for="<?php echo $uid; ?>-name">Nom &amp; Prénom</label><input type="text" id="<?php echo $uid; ?>-name" name="name" autocomplete="name" required maxlength="100"><p class="kn-contact__error" data-error-for="name"></p></div>
            <div class="kn-contact__field"><label for="<?php echo $uid; ?>-tel">Téléphone</label><input type="tel" id="<?php echo $uid; ?>-tel" name="phone" autocomplete="tel" inputmode="tel" required maxlength="30"><p class="kn-contact__error" data-error-for="phone"></p></div>
          </div>
          <div class="kn-contact__row">
            <div class="kn-contact__field"><label for="<?php echo $uid; ?>-email">E-mail</label><input type="email" id="<?php echo $uid; ?>-email" name="email" autocomplete="email" inputmode="email" required maxlength="190"><p class="kn-contact__error" data-error-for="email"></p></div>
            <div class="kn-contact__field"><label for="<?php echo $uid; ?>-commune">Commune</label><input type="text" id="<?php echo $uid; ?>-commune" name="commune" placeholder="Ex : Visé, Liège…" required maxlength="120"><p class="kn-contact__error" data-error-for="commune"></p></div>
          </div>
          <div class="kn-contact__field"><label for="<?php echo $uid; ?>-msg">Informations complémentaires <small>(facultatif)</small></label><textarea id="<?php echo $uid; ?>-msg" name="extra" rows="4" maxlength="1200" placeholder="État de la terrasse, présence de mousse importante, options souhaitées, disponibilités…"></textarea></div>

          <div class="kn-contact__field kn-contact__field--check">
            <label class="kn-contact__consent"><input type="checkbox" name="consent" value="1" required><span><?php echo htmlspecialchars($consent); ?><?php if ($privUrl !== ''): ?> <a href="<?php echo htmlspecialchars($privUrl); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($privLabel); ?></a><?php endif; ?></span></label>
            <p class="kn-contact__error" data-error-for="consent"></p>
          </div>

          <p class="kn-contact__status" role="alert" aria-live="polite" hidden></p>
          <button type="submit" class="kn-btn kn-contact__submit"><?php echo knIcon('send', array('size' => 16)); ?><span><?php echo htmlspecialchars($submitLbl); ?></span></button>
          <p class="kn-contact__note"><?php echo knIcon('lock', array('size' => 14)); ?><span><?php echo htmlspecialchars($formNote); ?></span></p>
        </form>
      </div>

    </div>
  </div>
</section>
<?php if ($schemaOn && $lowRate !== null):
    $service = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => $t('service_name', 'Nettoyage de terrasse haute pression'),
        'provider'    => array('@type' => 'LocalBusiness', 'name' => 'Keepnew'),
        'areaServed'  => $t('area_served', 'Liège, Namur, Belgique'),
        'description' => $t('service_description', 'Nettoyage haute pression de terrasses : carrelage, béton, pavés, bois et résine. À partir de ' . $fmt($lowRate) . ' €/m², tarif minimum ' . $fmt($minPrice) . ' €. Devis gratuit en ligne.'),
        'offers'      => array(
            '@type'         => 'Offer',
            'priceCurrency' => 'EUR',
            'price'         => number_format($lowRate, 2, '.', ''),
            'priceSpecification' => array('@type' => 'UnitPriceSpecification', 'price' => number_format($lowRate, 2, '.', ''), 'priceCurrency' => 'EUR', 'unitText' => 'm²'),
        ),
    );
?>
<script type="application/ld+json"><?php echo json_encode($service, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>
<?php endif; ?>
<script>
(function () {
  var calc = document.getElementById('<?php echo $calcId; ?>');
  var formRoot = document.getElementById('<?php echo $formId; ?>');
  if (!calc || !formRoot || calc.dataset.init) return;
  calc.dataset.init = '1';
  var CFG = <?php echo json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?>;
  var $ = function (s, r) { return (r || calc).querySelector(s); };
  var state = { s: 0, m2: 20, opts: {} };

  function fmt(n) { return (Math.round(n * 100) / 100).toLocaleString('fr-BE', { maximumFractionDigits: 2 }); }
  function compute() {
    var surf = CFG.surfaces[state.s], base = state.m2 * surf.rate, lines = [], total = base;
    lines.push(['HP · ' + surf.label + ' · ' + fmt(state.m2) + ' m² × ' + fmt(surf.rate) + ' €', base]);
    Object.keys(state.opts).forEach(function (i) {
      var o = CFG.options[i], amt = state.m2 * o.rate; total += amt;
      lines.push([o.label + ' · ' + fmt(state.m2) + ' m² × ' + fmt(o.rate) + ' €', amt, true]);
    });
    var isMin = state.m2 > 0 && total < CFG.minPrice;
    if (isMin) { total = CFG.minPrice; lines.push(['Tarif minimum appliqué', CFG.minPrice, true, true]); }
    return { surf: surf, base: base, lines: lines, total: total, isMin: isMin };
  }

  function render() {
    var r = compute();
    $('[data-total]').textContent = Math.round(r.total);
    $('[data-ttc]').textContent = r.isMin ? 'Tarif minimum appliqué · surface ' + fmt(state.m2) + ' m²' : 'TTC · pour ' + fmt(state.m2) + ' m² de revêtement';
    var box = $('[data-lines]'); box.innerHTML = '';
    r.lines.forEach(function (l) {
      var row = document.createElement('div'); row.className = 'kn-tc__line' + (l[3] ? ' is-min' : '');
      var a = document.createElement('span'); a.textContent = l[0];
      var b = document.createElement('strong'); b.textContent = (l[2] && !l[3] ? '+' : '') + Math.round(l[1]) + ' €';
      row.appendChild(a); row.appendChild(b); box.appendChild(row);
    });
    var tot = document.createElement('div'); tot.className = 'kn-tc__line is-total';
    tot.innerHTML = '<span>Total estimé</span>'; var tb = document.createElement('strong'); tb.textContent = Math.round(r.total) + ' €'; tot.appendChild(tb); box.appendChild(tot);
    $('[data-alert]').hidden = !(state.m2 > 0 && state.m2 < CFG.minM2);
    var names = Object.keys(state.opts).map(function (i) { return CFG.options[i].label; });
    var f = function (s) { return formRoot.querySelector(s); };
    f('[data-resume-price]').textContent = Math.round(r.total) + ' €';
    f('[data-resume-detail]').textContent = fmt(state.m2) + ' m² · ' + r.surf.label;
    f('[data-resume-opts]').textContent = names.length ? names.join(', ') : 'Nettoyage seul';
    window.__tcState = { r: r, names: names, m2: state.m2 };
  }

  // 1. revêtement
  calc.querySelectorAll('[data-surface]').forEach(function (b) {
    b.addEventListener('click', function () {
      state.s = parseInt(b.dataset.surface, 10);
      calc.querySelectorAll('[data-surface]').forEach(function (x) { var on = x === b; x.classList.toggle('is-selected', on); x.setAttribute('aria-checked', on ? 'true' : 'false'); });
      render();
    });
  });

  // 2. superficie (dimensions / m² directs / curseur synchronisés)
  var len = $('[data-len]'), wid = $('[data-wid]'), area = $('[data-area]'), direct = $('[data-direct]'), slider = $('[data-slider]'), sval = $('[data-slider-val]');
  function setM2(v, from) {
    v = Math.max(0, Math.round(v * 10) / 10); state.m2 = v;
    area.textContent = fmt(v) + ' m²'; sval.textContent = fmt(v) + ' m²';
    slider.value = Math.min(v, CFG.sliderMax);
    if (from !== 'direct') direct.value = v;
    render();
  }
  function fromDims() { setM2((parseFloat(len.value) || 0) * (parseFloat(wid.value) || 0), 'dim'); }
  len.addEventListener('input', fromDims); wid.addEventListener('input', fromDims);
  direct.addEventListener('input', function () { setM2(parseFloat(direct.value) || 0, 'direct'); });
  slider.addEventListener('input', function () {
    var v = parseInt(slider.value, 10), l = parseFloat(len.value) || 1;
    wid.value = Math.round((v / l) * 10) / 10; setM2(v, 'slider');
  });
  calc.querySelectorAll('[data-mode]').forEach(function (t) {
    t.addEventListener('click', function () {
      calc.querySelectorAll('[data-mode]').forEach(function (x) { var on = x === t; x.classList.toggle('is-active', on); x.setAttribute('aria-selected', on ? 'true' : 'false'); });
      var dim = t.dataset.mode === 'dim';
      calc.querySelector('[data-pane="dim"]').hidden = !dim; calc.querySelector('[data-pane="m2"]').hidden = dim;
      if (dim) fromDims(); else setM2(parseFloat(direct.value) || state.m2, 'direct');
    });
  });

  // 3. options
  calc.querySelectorAll('[data-option]').forEach(function (b) {
    b.addEventListener('click', function () {
      var i = b.dataset.option, on = !state.opts[i];
      if (on) state.opts[i] = true; else delete state.opts[i];
      b.classList.toggle('is-selected', on); b.setAttribute('aria-checked', on ? 'true' : 'false');
      render();
    });
  });

  // Formulaire de RDV (envoi sécurisé via /contact/send)
  var form = formRoot.querySelector('[data-tc-form]'), okBox = formRoot.querySelector('.kn-tc-rdv__success'), status = formRoot.querySelector('.kn-contact__status'), btn = formRoot.querySelector('.kn-contact__submit');
  function clearErr() { form.querySelectorAll('[data-error-for]').forEach(function (p) { p.textContent = ''; }); form.querySelectorAll('[aria-invalid]').forEach(function (i) { i.removeAttribute('aria-invalid'); }); status.hidden = true; status.textContent = ''; }
  function setErr(n, m) { var p = form.querySelector('[data-error-for="' + n + '"]'); if (p) p.textContent = m; var f = form.elements[n]; if (f) f.setAttribute('aria-invalid', 'true'); }
  function validate() {
    var bad = null, v = function (n, m) { if (!bad) bad = form.elements[n]; setErr(n, m); };
    if (form.elements.name.value.trim().length < 3) v('name', 'Veuillez entrer votre nom (3 caractères minimum).');
    if (!/^[+]?[\d\s\-.()]{7,20}$/.test(form.elements.phone.value.trim())) v('phone', 'Numéro invalide (format belge ou international).');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.elements.email.value.trim())) v('email', 'Adresse e-mail invalide.');
    if (form.elements.commune.value.trim().length < 2) v('commune', 'Veuillez indiquer votre commune.');
    if (!form.elements.consent.checked) v('consent', 'Merci de cocher la case pour que nous puissions vous répondre.');
    if (bad) { bad.focus(); return false; } return true;
  }
  form.addEventListener('submit', function (ev) {
    ev.preventDefault(); clearErr(); if (!validate()) return;
    var st = window.__tcState, r = st.r, opts = st.names.length ? st.names.join(', ') : 'Aucune';
    var extra = form.elements.extra.value.trim();
    var msg = 'Demande de RDV — nettoyage de terrasse\n'
      + 'Commune : ' + form.elements.commune.value.trim() + '\n'
      + 'Revêtement : ' + r.surf.label + ' (' + fmt(r.surf.rate) + ' €/m²)\n'
      + 'Superficie : ' + fmt(st.m2) + ' m²\n'
      + 'Options : ' + opts + '\n'
      + 'Total estimatif : ' + Math.round(r.total) + ' €' + (r.isMin ? ' (tarif minimum appliqué)' : '') + '\n'
      + (extra ? '\nInformations complémentaires :\n' + extra : '');
    var fd = new FormData(form); fd.delete('commune'); fd.delete('extra');
    fd.set('subject', 'Demande de RDV terrasse'); fd.set('message', msg);
    btn.disabled = true; btn.classList.add('is-loading');
    fetch(form.action, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
      .then(function (res) { return res.json().then(function (j) { return { status: res.status, json: j }; }, function () { return { status: res.status, json: {} }; }); })
      .then(function (res) {
        if (res.json && res.json.ok) {
          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({ event: 'demande_terrasse', nom_prenom: form.elements.name.value.trim(), email: form.elements.email.value.trim(), telephone: form.elements.phone.value.trim(), commune: form.elements.commune.value.trim(), type_revetement: r.surf.label, superficie: fmt(st.m2) + ' m²', options: opts, total_estimatif: Math.round(r.total) + ' €' });
          okBox.querySelector('[data-success-text]').textContent = CFG.okText.replace('{nom}', form.elements.name.value.trim()).replace('{tel}', form.elements.phone.value.trim());
          form.hidden = true; formRoot.querySelector('[data-resume]').hidden = true; okBox.hidden = false; okBox.focus();
          okBox.scrollIntoView({ behavior: 'smooth', block: 'center' }); return;
        }
        var errs = (res.json && res.json.errors) || {};
        Object.keys(errs).forEach(function (k) { setErr(k === 'message' ? 'name' : k, errs[k]); });
        status.textContent = (res.json && res.json.error) || 'Une erreur est survenue. Réessayez ou appelez-nous.'; status.hidden = false;
      }).catch(function () { status.textContent = 'Connexion impossible pour le moment. Réessayez ou appelez-nous.'; status.hidden = false; })
      .then(function () { btn.disabled = false; btn.classList.remove('is-loading'); });
  });

  render();
})();
</script>
