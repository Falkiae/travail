<?php
require_once __DIR__ . '/_block_helpers.php';

static $knFcCount = 0;
$knFcCount++;
$uid    = 'kn-fc-' . $knFcCount;
$calcId = $knFcCount === 1 ? 'calculateur' : $uid;
$formId = $knFcCount === 1 ? 'rdv' : $uid . '-rdv';

$t = function ($key, $default) use ($block) {
    return isset($block[$key]) && trim((string) $block[$key]) !== '' ? (string) $block[$key] : $default;
};
$num = function ($v, $default) {
    $v = str_replace(',', '.', trim((string) $v));
    return is_numeric($v) ? (float) $v : $default;
};

$defCats = array(
    array('label' => 'Citadine',   'sub' => 'Polo, Clio, 208…',      'icon' => 'voiture', 'price' => '79'),
    array('label' => 'Berline',    'sub' => 'Golf, Passat, 308…',    'icon' => 'voiture', 'price' => '87'),
    array('label' => 'SUV / Break','sub' => 'Tiguan, Outlander…',    'icon' => 'voiture', 'price' => '91'),
    array('label' => 'Utilitaire / Camionnette', 'sub' => 'Van, camionnette…', 'icon' => 'truck', 'price' => '100'),
);
$cats = array();
foreach ((isset($block['cats']) && is_array($block['cats']) ? $block['cats'] : $defCats) as $c) {
    if (!is_array($c) || trim(isset($c['label']) ? (string) $c['label'] : '') === '') continue;
    $cats[] = array('label' => (string) $c['label'], 'sub' => isset($c['sub']) ? (string) $c['sub'] : '', 'icon' => !empty($c['icon']) ? (string) $c['icon'] : 'voiture', 'price' => $num(isset($c['price']) ? $c['price'] : '', 0.0));
}
if (!$cats) return;

$defFreqs = array(array('label' => '1 fois par mois', 'per' => '1'), array('label' => '2 fois par mois', 'per' => '2'), array('label' => '1 fois par semaine (×4/mois)', 'per' => '4'));
$freqs = array();
foreach ((isset($block['freqs']) && is_array($block['freqs']) ? $block['freqs'] : $defFreqs) as $f) {
    if (!is_array($f) || trim(isset($f['label']) ? (string) $f['label'] : '') === '') continue;
    $freqs[] = array('label' => (string) $f['label'], 'per' => max(1, $num(isset($f['per']) ? $f['per'] : '', 1.0)));
}
if (!$freqs) $freqs = array(array('label' => '1 fois par mois', 'per' => 1.0));

$defEngs = array(
    array('label' => 'Sans engagement', 'months' => '0',  'discount' => '0'),
    array('label' => '6 mois',          'months' => '6',  'discount' => '10'),
    array('label' => '12 mois',         'months' => '12', 'discount' => '12'),
    array('label' => '2 ans',           'months' => '24', 'discount' => '15'),
);
$engs = array();
foreach ((isset($block['engs']) && is_array($block['engs']) ? $block['engs'] : $defEngs) as $e) {
    if (!is_array($e) || trim(isset($e['label']) ? (string) $e['label'] : '') === '') continue;
    $engs[] = array('label' => (string) $e['label'], 'months' => (int) $num(isset($e['months']) ? $e['months'] : '', 0), 'discount' => $num(isset($e['discount']) ? $e['discount'] : '', 0.0));
}
if (!$engs) $engs = array(array('label' => 'Sans engagement', 'months' => 0, 'discount' => 0.0));
$engDefault = (int) $num(isset($block['eng_default']) ? $block['eng_default'] : '', 1);
if ($engDefault < 0 || $engDefault >= count($engs)) $engDefault = 0;

$fleetMin  = (int) $num(isset($block['fleet_threshold']) ? $block['fleet_threshold'] : '', 5);
$fleetDisc = $num(isset($block['fleet_discount']) ? $block['fleet_discount'] : '', 5.0);

$eyebrow  = $t('eyebrow', 'CALCULATEUR DE PRIX');
$h2       = $t('h2', 'Estimez votre budget en temps réel');
$intro    = $t('intro', 'Configurez votre flotte et obtenez une estimation instantanée avec vos remises d\'abonnement appliquées.');
$s1       = $t('step1_title', 'Votre flotte');
$s1hint   = $t('step1_hint', 'Combinez les catégories de véhicules.');
$s2       = $t('step2_title', 'Fréquence des passages');
$s3       = $t('step3_title', 'Engagement');
$resLabel = $t('result_label', 'Votre estimation');
$fiscal   = $t('fiscal_text', 'En tant que professionnel, cette dépense est jusqu\'à 100 % déductible fiscalement.');
$disc     = $t('disclaimer', '* Estimation indicative. Devis définitif après évaluation de votre flotte.');

$formH2    = $t('form_h2', 'Recevoir mon estimation par e-mail');
$formIntro = $t('form_intro', 'Remplissez vos coordonnées pour qu\'on vous recontacte avec votre devis personnalisé.');
$formTitle = $t('form_title', 'Recevoir mon estimation');
$formEyebrow = $t('form_eyebrow', 'VOTRE DEVIS PERSONNALISÉ');
$submitLbl = $t('submit_label', 'Envoyer ma demande');
$formNote  = $t('form_note', 'Gratuit · Sans engagement · Paiement après chaque prestation');
$consent   = $t('consent_text', 'J\'accepte que mes données soient utilisées pour répondre à ma demande.');
$okTitle   = $t('success_title', 'Demande envoyée avec succès !');
$okText    = $t('success_text', 'Merci {nom}, nous avons bien reçu votre demande. Notre équipe vous contactera très prochainement au {tel} ou par e-mail.');
$privUrl   = $t('privacy_url', isset($contact_channels['privacy_url']) ? (string) $contact_channels['privacy_url'] : '');
$privLabel = $t('privacy_label', 'Politique de confidentialité');
$chanTitle = $t('channels_title', 'Ou contactez-nous directement');
$waText    = $t('whatsapp_text', 'Bonjour, je souhaite un devis pour l\'entretien régulier de ma flotte de véhicules.');
$formBg    = isset($block['form_bg']) && in_array($block['form_bg'], array('white','alt','cream','rose','blue','night'), true) ? $block['form_bg'] : 'white';
$reassure  = isset($block['reassure']) && is_array($block['reassure']) ? $block['reassure'] : array(
    array('icon' => 'clock',        'text' => 'Réponse sous 24 h ouvrées'),
    array('icon' => 'shield-check', 'text' => 'Paiement après chaque prestation'),
    array('icon' => 'lock',         'text' => 'Devis gratuit et sans engagement'),
);
$chData  = knContactChannels($block, isset($contact_channels) && is_array($contact_channels) ? $contact_channels : array(), $waText);
$channels = $chData['channels']; $socials = $chData['socials'];
$socialLabels = array('facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'twitter' => 'X (Twitter)');

$stamp   = \App\Controllers\ContactController::stamp();
$token   = \App\Controllers\ContactController::token();
$pageUrl = isset($_SERVER['REQUEST_URI']) ? (string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';

$cfg = array('cats' => $cats, 'freqs' => $freqs, 'engs' => $engs, 'engDefault' => $engDefault, 'fleetMin' => $fleetMin, 'fleetDisc' => $fleetDisc, 'okText' => $okText);
$rdvBlock = $block; $rdvBlock['bg'] = $formBg;
$fmtP = function ($n) { return rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ','); };
?>
<section class="<?php echo blockClasses($block, 'cream', 'kn-tc kn-fc'); ?>" id="<?php echo $calcId; ?>" data-fc-root>
  <div class="container kn-tc__inner">
    <header class="kn-tc__head">
      <span class="kn-eyebrow"><?php echo htmlspecialchars($eyebrow); ?></span>
      <h2 class="kn-section__title"><?php echo htmlspecialchars($h2); ?></h2>
      <p class="kn-tc__intro"><?php echo htmlspecialchars($intro); ?></p>
    </header>

    <div class="kn-tc__card">
      <div class="kn-tc__step"><span class="kn-tc__num">1</span><h3><?php echo htmlspecialchars($s1); ?> <small>(<?php echo htmlspecialchars($s1hint); ?>)</small></h3></div>
      <div class="kn-fc__rows">
        <?php foreach ($cats as $i => $c): ?>
        <div class="kn-fc__row" data-row="<?php echo $i; ?>">
          <span class="kn-tc__pill-ico" aria-hidden="true"><?php echo knIcon($c['icon'], array('size' => 22, 'fallback' => 'voiture')); ?></span>
          <span class="kn-fc__name"><strong><?php echo htmlspecialchars($c['label']); ?></strong><?php if ($c['sub'] !== ''): ?><small><?php echo htmlspecialchars($c['sub']); ?></small><?php endif; ?></span>
          <span class="kn-fc__price"><?php echo $fmtP($c['price']); ?> € HTVA</span>
          <span class="kn-fc__counter">
            <button type="button" data-cat="<?php echo $i; ?>" data-dir="-1" aria-label="Retirer un véhicule : <?php echo htmlspecialchars($c['label']); ?>">−</button>
            <input type="number" inputmode="numeric" min="0" max="50" value="0" data-qty="<?php echo $i; ?>" aria-label="Nombre de véhicules : <?php echo htmlspecialchars($c['label']); ?>">
            <button type="button" data-cat="<?php echo $i; ?>" data-dir="1" aria-label="Ajouter un véhicule : <?php echo htmlspecialchars($c['label']); ?>">+</button>
          </span>
        </div>
        <?php endforeach; ?>
      </div>
      <p class="kn-fc__summary" data-fleet-summary hidden><span data-fleet-label></span><strong data-fleet-base></strong></p>
      <?php if ($fleetDisc > 0): ?><p class="kn-tc__note kn-fc__hint">À partir de <?php echo $fleetMin; ?> véhicules au total : remise flotte supplémentaire de <?php echo $fmtP($fleetDisc); ?> %.</p><?php endif; ?>
    </div>

    <div class="kn-tc__card">
      <div class="kn-tc__step"><span class="kn-tc__num">2</span><h3><?php echo htmlspecialchars($s2); ?></h3></div>
      <div class="kn-contact__field">
        <select data-freq aria-label="<?php echo htmlspecialchars($s2); ?>">
          <?php foreach ($freqs as $i => $f): ?><option value="<?php echo $i; ?>"><?php echo htmlspecialchars($f['label']); ?></option><?php endforeach; ?>
        </select>
        <p class="kn-tc__note kn-fc__hint">Fréquence appliquée à l'ensemble de la flotte.</p>
      </div>
    </div>

    <div class="kn-tc__card">
      <div class="kn-tc__step"><span class="kn-tc__num">3</span><h3><?php echo htmlspecialchars($s3); ?></h3></div>
      <div class="kn-fc__engs" role="radiogroup" aria-label="<?php echo htmlspecialchars($s3); ?>">
        <?php foreach ($engs as $i => $e): ?>
        <button type="button" class="kn-tc__pill kn-fc__eng<?php echo $i === $engDefault ? ' is-selected' : ''; ?>" role="radio" aria-checked="<?php echo $i === $engDefault ? 'true' : 'false'; ?>" data-eng="<?php echo $i; ?>">
          <span class="kn-tc__pill-txt"><strong><?php echo htmlspecialchars($e['label']); ?></strong></span>
          <span class="kn-tc__pill-price">−<?php echo $fmtP($e['discount']); ?> %</span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="kn-tc__result" aria-live="polite">
      <span class="kn-tc__result-label"><?php echo htmlspecialchars($resLabel); ?></span>
      <div class="kn-tc__price"><span data-monthly>—</span></div>
      <p class="kn-tc__ttc">par mois · HTVA</p>
      <div class="kn-tc__lines" data-lines></div>
      <div class="kn-fc__saving" data-saving hidden><span class="kn-fc__saving-ico" aria-hidden="true"><?php echo knIcon('sparkles', array('size' => 18)); ?></span><span data-saving-text></span></div>
      <p class="kn-fc__fiscal"><?php echo knIcon('check', array('size' => 14)); ?><span><?php echo htmlspecialchars($fiscal); ?></span></p>
      <a href="#<?php echo $formId; ?>" class="kn-btn kn-tc__cta"><?php echo knIcon('send', array('size' => 16)); ?><?php echo htmlspecialchars($t('cta_label', 'Recevoir mon devis personnalisé')); ?></a>
    </div>
    <p class="kn-tc__note"><?php echo htmlspecialchars($disc); ?></p>
  </div>
</section>

<section class="<?php echo blockClasses($rdvBlock, 'white', 'kn-contact kn-tc-rdv'); ?>" id="<?php echo $formId; ?>" data-fc-form-root>
  <div class="container">
    <div class="kn-contact__grid">
      <div class="kn-contact__info">
        <span class="kn-eyebrow"><?php echo htmlspecialchars($formEyebrow); ?></span>
        <h2 class="kn-section__title"><?php echo htmlspecialchars($formH2); ?></h2>
        <p class="kn-contact__intro"><?php echo htmlspecialchars($formIntro); ?></p>

        <div class="kn-tc-rdv__resume" data-resume>
          <div><span class="kn-tc__lab">Votre estimation</span><strong data-resume-price>—</strong><small data-resume-detail>Configurez votre flotte dans le calculateur ci-dessus.</small></div>
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
            <a class="kn-contact__chan kn-contact__chan--<?php echo $c['cls']; ?>" href="<?php echo htmlspecialchars($c['href']); ?>"<?php echo $c['ext'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo knIcon($c['icon'], array('size' => 20)); ?><span><?php echo htmlspecialchars($c['label']); ?></span></a>
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

        <form class="kn-contact__form kn-tc-rdv__form" method="post" action="/contact/send" novalidate data-fc-form>
          <h3 class="kn-contact__form-title"><?php echo htmlspecialchars($formTitle); ?></h3>
          <input type="hidden" name="_token" value="<?php echo htmlspecialchars($token); ?>">
          <input type="hidden" name="ts" value="<?php echo htmlspecialchars($stamp['ts']); ?>">
          <input type="hidden" name="sig" value="<?php echo htmlspecialchars($stamp['sig']); ?>">
          <input type="hidden" name="page" value="<?php echo htmlspecialchars($pageUrl); ?>">
          <div class="kn-contact__trap" aria-hidden="true"><label>Ne pas remplir<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

          <div class="kn-contact__field"><label for="<?php echo $uid; ?>-name">Nom &amp; Prénom</label><input type="text" id="<?php echo $uid; ?>-name" name="name" autocomplete="name" required maxlength="100"><p class="kn-contact__error" data-error-for="name"></p></div>
          <div class="kn-contact__row">
            <div class="kn-contact__field"><label for="<?php echo $uid; ?>-tel">Téléphone</label><input type="tel" id="<?php echo $uid; ?>-tel" name="phone" autocomplete="tel" inputmode="tel" required maxlength="30"><p class="kn-contact__error" data-error-for="phone"></p></div>
            <div class="kn-contact__field"><label for="<?php echo $uid; ?>-email">Adresse e-mail</label><input type="email" id="<?php echo $uid; ?>-email" name="email" autocomplete="email" inputmode="email" required maxlength="190"><p class="kn-contact__error" data-error-for="email"></p></div>
          </div>

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
<script>
(function () {
  var calc = document.getElementById('<?php echo $calcId; ?>');
  var formRoot = document.getElementById('<?php echo $formId; ?>');
  if (!calc || !formRoot || calc.dataset.init) return;
  calc.dataset.init = '1';
  var CFG = <?php echo json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?>;
  var $ = function (s, r) { return (r || calc).querySelector(s); };
  var qty = CFG.cats.map(function () { return 0; }), freqIdx = 0, engIdx = CFG.engDefault;
  var last = null;

  function eur(n) { return n.toLocaleString('fr-BE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' € HTVA'; }
  function pct(n) { return (Math.round(n * 100) / 100).toLocaleString('fr-BE') + ' %'; }

  function compute() {
    var vehicles = 0, base = 0;
    qty.forEach(function (q, i) { vehicles += q; base += q * CFG.cats[i].price; });
    if (!vehicles) return null;
    var freq = CFG.freqs[freqIdx], eng = CFG.engs[engIdx];
    var fleetPct = (CFG.fleetDisc > 0 && vehicles >= CFG.fleetMin) ? CFG.fleetDisc : 0;
    var afterFleet = base * (1 - fleetPct / 100);
    var perPass = afterFleet * (1 - eng.discount / 100);
    var monthly = perPass * freq.per;
    var months = eng.months > 0 ? eng.months : 12;
    var total = monthly * months;
    var perDay = total / months / 30.44 / vehicles;
    var monthlyNoEng = afterFleet * freq.per;
    var saving = eng.discount > 0 ? (monthlyNoEng - monthly) * eng.months : 0;
    return { vehicles: vehicles, base: base, freq: freq, eng: eng, fleetPct: fleetPct, afterFleet: afterFleet, perPass: perPass, monthly: monthly, months: months, total: total, perDay: perDay, saving: saving };
  }

  function render() {
    var vehicles = qty.reduce(function (a, b) { return a + b; }, 0);
    calc.querySelectorAll('[data-row]').forEach(function (row) { row.classList.toggle('is-active', qty[row.dataset.row] > 0); });
    var sum = $('[data-fleet-summary]');
    var r = compute(); last = r;
    var f = function (s) { return formRoot.querySelector(s); };
    var lines = $('[data-lines]'); lines.innerHTML = '';
    if (!r) {
      sum.hidden = true; $('[data-monthly]').textContent = '—'; $('[data-saving]').hidden = true;
      f('[data-resume-price]').textContent = '—'; f('[data-resume-detail]').textContent = 'Configurez votre flotte dans le calculateur ci-dessus.'; f('[data-resume-opts]').textContent = '';
      return;
    }
    sum.hidden = false;
    $('[data-fleet-label]').textContent = r.vehicles + ' véhicule' + (r.vehicles > 1 ? 's' : '') + ' sélectionné' + (r.vehicles > 1 ? 's' : '');
    $('[data-fleet-base]').textContent = eur(r.base) + ' / passage';
    $('[data-monthly]').textContent = eur(r.monthly);
    var add = function (label, value, cls, strong) {
      var row = document.createElement('div'); row.className = 'kn-tc__line' + (cls ? ' ' + cls : '');
      var a = document.createElement('span'); a.textContent = label; var b = document.createElement('strong'); b.textContent = value;
      row.appendChild(a); row.appendChild(b); lines.appendChild(row);
    };
    add('Prix de base (par passage)', eur(r.base));
    if (r.fleetPct > 0) add('Remise flotte (−' + pct(r.fleetPct) + ')', '−' + eur(r.base * r.fleetPct / 100));
    if (r.eng.discount > 0) add('Remise engagement (−' + pct(r.eng.discount) + ')', '−' + eur(r.afterFleet * r.eng.discount / 100));
    add('Prix par passage', eur(r.perPass));
    add(r.eng.months > 0 ? 'Total abonnement ' + r.eng.label : 'Estimation sur 12 mois', eur(r.total), 'is-total');
    add('Par véhicule / jour', eur(r.perDay));
    var sv = $('[data-saving]');
    if (r.saving > 0.5) { sv.hidden = false; $('[data-saving-text]').textContent = 'Vous économisez ' + Math.round(r.saving).toLocaleString('fr-BE') + ' € sur ' + r.eng.label + ' par rapport au tarif sans engagement.'; } else { sv.hidden = true; }
    f('[data-resume-price]').textContent = eur(r.monthly) + ' / mois';
    f('[data-resume-detail]').textContent = r.vehicles + ' véhicule' + (r.vehicles > 1 ? 's' : '') + ' · ' + r.freq.label;
    f('[data-resume-opts]').textContent = r.eng.label;
  }

  function setQty(i, v) { v = Math.max(0, Math.min(50, parseInt(v, 10) || 0)); qty[i] = v; calc.querySelector('[data-qty="' + i + '"]').value = v; render(); }
  calc.querySelectorAll('[data-cat]').forEach(function (b) { b.addEventListener('click', function () { var i = +b.dataset.cat; setQty(i, qty[i] + (+b.dataset.dir)); }); });
  calc.querySelectorAll('[data-qty]').forEach(function (inp) { inp.addEventListener('input', function () { var i = +inp.dataset.qty; qty[i] = Math.max(0, Math.min(50, parseInt(inp.value, 10) || 0)); render(); }); });
  calc.querySelectorAll('[data-qty]').forEach(function (inp) { inp.addEventListener('change', function () { setQty(+inp.dataset.qty, inp.value); }); });
  $('[data-freq]').addEventListener('change', function (e) { freqIdx = +e.target.value; render(); });
  calc.querySelectorAll('[data-eng]').forEach(function (b) {
    b.addEventListener('click', function () {
      engIdx = +b.dataset.eng;
      calc.querySelectorAll('[data-eng]').forEach(function (x) { var on = x === b; x.classList.toggle('is-selected', on); x.setAttribute('aria-checked', on ? 'true' : 'false'); });
      render();
    });
  });

  // Formulaire (envoi sécurisé via /contact/send)
  var form = formRoot.querySelector('[data-fc-form]'), okBox = formRoot.querySelector('.kn-tc-rdv__success'), status = formRoot.querySelector('.kn-contact__status'), btn = formRoot.querySelector('.kn-contact__submit');
  function clearErr() { form.querySelectorAll('[data-error-for]').forEach(function (p) { p.textContent = ''; }); form.querySelectorAll('[aria-invalid]').forEach(function (i) { i.removeAttribute('aria-invalid'); }); status.hidden = true; status.textContent = ''; }
  function setErr(n, m) { var p = form.querySelector('[data-error-for="' + n + '"]'); if (p) p.textContent = m; var f = form.elements[n]; if (f) f.setAttribute('aria-invalid', 'true'); }
  function validate() {
    var bad = null, v = function (n, m) { if (!bad) bad = form.elements[n]; setErr(n, m); };
    if (form.elements.name.value.trim().length < 3) v('name', 'Veuillez entrer votre nom et prénom.');
    if (!/^[+]?[\d\s\-.()]{7,20}$/.test(form.elements.phone.value.trim())) v('phone', 'Numéro de téléphone invalide.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.elements.email.value.trim())) v('email', 'Adresse e-mail invalide.');
    if (!form.elements.consent.checked) v('consent', 'Merci de cocher la case pour que nous puissions vous répondre.');
    if (bad) { bad.focus(); return false; } return true;
  }
  form.addEventListener('submit', function (ev) {
    ev.preventDefault(); clearErr();
    if (!last) { status.textContent = 'Veuillez d\'abord configurer votre flotte dans le calculateur ci-dessus avant d\'envoyer votre demande.'; status.hidden = false; calc.scrollIntoView({ behavior: 'smooth', block: 'start' }); return; }
    if (!validate()) return;
    var r = last, fleet = [];
    qty.forEach(function (q, i) { if (q > 0) fleet.push(q + ' × ' + CFG.cats[i].label + ' (' + CFG.cats[i].price + ' € HTVA / unité)'); });
    var msg = 'Demande d\'abonnement Pro — nettoyage de flotte\n'
      + 'Flotte : ' + fleet.join(', ') + ' (' + r.vehicles + ' véhicule' + (r.vehicles > 1 ? 's' : '') + ')\n'
      + 'Fréquence : ' + r.freq.label + '\n'
      + 'Engagement : ' + r.eng.label + (r.eng.discount > 0 ? ' (−' + pct(r.eng.discount) + ')' : '') + '\n'
      + 'Remise flotte : ' + (r.fleetPct > 0 ? '−' + pct(r.fleetPct) : 'aucune') + '\n'
      + 'Prix par passage : ' + eur(r.perPass) + '\n'
      + 'Estimation mensuelle : ' + eur(r.monthly) + '\n'
      + (r.eng.months > 0 ? 'Total abonnement ' + r.eng.label : 'Estimation sur 12 mois') + ' : ' + eur(r.total) + '\n'
      + 'Coût par véhicule / jour : ' + eur(r.perDay) + '\n'
      + 'Économie sur l\'engagement : ' + (r.saving > 0.5 ? Math.round(r.saving) + ' € HTVA' : '—');
    var fd = new FormData(form); fd.set('subject', 'Demande d\'abonnement Pro'); fd.set('message', msg);
    btn.disabled = true; btn.classList.add('is-loading');
    fetch(form.action, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
      .then(function (res) { return res.json().then(function (j) { return { status: res.status, json: j }; }, function () { return { status: res.status, json: {} }; }); })
      .then(function (res) {
        if (res.json && res.json.ok) {
          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({ event: 'demande_abonnement', nom_prenom: form.elements.name.value.trim(), email: form.elements.email.value.trim(), telephone: form.elements.phone.value.trim(), nb_vehicules: r.vehicles, frequence: r.freq.label, engagement: r.eng.label, estimation_mensuelle: r.monthly.toFixed(2), remise_flotte: r.fleetPct, remise_engagement: r.eng.discount });
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
