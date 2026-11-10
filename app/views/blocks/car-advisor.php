<?php
require_once __DIR__ . '/_block_helpers.php';
$booking_url = isset($booking_url) ? $booking_url : '#';

static $knCaCount = 0;
$knCaCount++;
$uid = 'kn-ca-' . $knCaCount;

$t = function ($key, $default) use ($block) {
    return isset($block[$key]) && trim((string) $block[$key]) !== '' ? (string) $block[$key] : $default;
};
$num = function ($v) {
    $v = str_replace(array(',', '€', ' '), array('.', '', ''), trim((string) $v));
    return is_numeric($v) ? (float) $v : null;
};

$sizes  = array('citadine', 'berline', 'suv', 'utilitaire');
$places = array('domicile', 'atelier');

$defFormulas = array(
    array('key' => 'shampoing', 'name' => 'Shampoing des sièges', 'badge' => 'NOUVEAU',
          'tagline' => 'Des sièges propres et sans taches, sans payer un nettoyage complet.',
          'points' => 'Aspiration des sièges;Shampoing des sièges en profondeur;Parfum longue durée',
          'duration' => '≈ 1 h', 'price_citadine_domicile' => '80'),
    array('key' => 'ie', 'name' => 'Intérieur & Extérieur', 'badge' => 'LE PLUS DEMANDÉ',
          'tagline' => "L'entretien complet pour une voiture propre dedans comme dehors.",
          'points' => "Dépoussiérage et aspiration complète de l'habitacle;Plastiques intérieurs et vitres sans traces;Prélavage mousse active et lavage manuel;Jantes, passages de roues et brillant pneus;Protection hydrophobe;Parfum d'ambiance",
          'duration' => '≈ 1 h 30', 'price_citadine_domicile' => '95'),
    array('key' => 'ran_int', 'name' => 'Remise à neuf intérieur', 'badge' => '',
          'tagline' => 'Un habitacle repris en profondeur, comme au premier jour.',
          'points' => "Tout l'intérieur de la formule Intérieur & Extérieur;Shampoing des sièges et des carpettes;Nettoyage des cuirs;Vitres, pare-brise et volant dégraissé;Parfum longue durée",
          'duration' => "Selon l'état", 'price_citadine_domicile' => '180'),
    array('key' => 'ran_full', 'name' => 'Remise à neuf complète', 'badge' => 'IDÉAL FIN DE LEASING',
          'tagline' => 'La formule la plus complète, pour une voiture comme neuve.',
          'points' => "Toute la remise à neuf intérieure;Extérieur complet : prélavage, lavage manuel, jantes;Plastiques extérieurs traités;Protection hydrophobe",
          'duration' => "Jusqu'à ≈ 4 h", 'price_citadine_domicile' => '240'),
);
$defExtras = array(
    array('label' => 'Option ozone', 'desc' => 'Supprime les odeurs tenaces à la source (tabac, animaux, humidité).', 'icon' => 'wind', 'price' => '', 'atelier_only' => '', 'url' => '', 'preselect_if' => ''),
    array('label' => 'Shampoing des carpettes', 'desc' => 'Pour retrouver des tapis de sol sans traces.', 'icon' => 'droplets', 'price' => '', 'atelier_only' => '', 'url' => '', 'preselect_if' => ''),
    array('label' => "Poils d'animaux", 'desc' => 'Retrait minutieux des poils incrustés dans les textiles.', 'icon' => 'heart', 'price' => '', 'atelier_only' => '', 'url' => '', 'preselect_if' => 'enfants'),
    array('label' => 'Polissage / céramique', 'desc' => 'Correction des micro-rayures et protection longue durée, en atelier à Visé.', 'icon' => 'sparkles', 'price' => '', 'atelier_only' => '1', 'url' => '/polissage-voiture-liege', 'preselect_if' => ''),
);

$formulas = array();
foreach ((isset($block['formulas']) && is_array($block['formulas']) ? $block['formulas'] : $defFormulas) as $f) {
    if (!is_array($f) || trim(isset($f['name']) ? (string) $f['name'] : '') === '') continue;
    $prices = array();
    foreach ($sizes as $s) foreach ($places as $p) {
        $k = 'price_' . $s . '_' . $p;
        $prices[$s . '_' . $p] = $num(isset($f[$k]) ? $f[$k] : '');
    }
    $pts = array_values(array_filter(array_map('trim', preg_split('/;|\r\n|\r|\n/', isset($f['points']) ? (string) $f['points'] : ''))));
    $formulas[] = array(
        'key' => isset($f['key']) ? preg_replace('/[^a-z0-9_]/', '', strtolower((string) $f['key'])) : '',
        'name' => (string) $f['name'], 'badge' => isset($f['badge']) ? (string) $f['badge'] : '',
        'tagline' => isset($f['tagline']) ? (string) $f['tagline'] : '', 'points' => $pts,
        'duration' => isset($f['duration']) ? (string) $f['duration'] : '',
        'url' => !empty($f['cta_url']) ? (string) $f['cta_url'] : '', 'prices' => $prices,
    );
}
if (!$formulas) return;

$extras = array();
foreach ((isset($block['extras']) && is_array($block['extras']) ? $block['extras'] : $defExtras) as $e) {
    if (!is_array($e) || trim(isset($e['label']) ? (string) $e['label'] : '') === '') continue;
    $extras[] = array(
        'label' => (string) $e['label'], 'desc' => isset($e['desc']) ? (string) $e['desc'] : '',
        'icon' => knIcon(!empty($e['icon']) ? (string) $e['icon'] : 'sparkles', array('size' => 20, 'fallback' => 'sparkles')),
        'price' => $num(isset($e['price']) ? $e['price'] : ''),
        'atelier' => !empty($e['atelier_only']) && (string) $e['atelier_only'] !== '0',
        'url' => isset($e['url']) ? (string) $e['url'] : '',
        'pre' => isset($e['preselect_if']) ? trim((string) $e['preselect_if']) : '',
    );
}

$svgCar = function ($w, $roof) {
    return '<svg viewBox="0 0 64 32" width="' . $w . '" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round" aria-hidden="true">' . $roof . '<circle cx="18" cy="25" r="4.5"/><circle cx="48" cy="25" r="4.5"/></svg>';
};
$vehicles = array(
    array('key' => 'citadine',   'label' => 'Citadine',                 'sub' => 'Polo, Clio, 208…',      'svg' => $svgCar(56, '<path d="M6 25H3v-6l5-2 6-7h20l8 7 9 2v6h-3M13.5 25h30"/>')),
    array('key' => 'berline',    'label' => 'Berline / Break',          'sub' => 'Golf, Série 3, Passat…', 'svg' => $svgCar(56, '<path d="M6 25H2v-5l7-3 8-7h18l10 7 12 2v6h-3M13.5 25h30"/>')),
    array('key' => 'suv',        'label' => 'SUV / 4×4',                'sub' => '3008, Tiguan, X1…',      'svg' => $svgCar(56, '<path d="M6 25H3v-8l4-2 5-8h26l8 8 9 1v9h-3M13.5 25h30"/>')),
    array('key' => 'utilitaire', 'label' => 'Utilitaire / Camionnette', 'sub' => 'Kangoo, Transit…',       'svg' => $svgCar(56, '<path d="M6 25H3V6h34l7 8 13 2v9h-4M13.5 25h30M37 6v8h7"/>')),
);
$statesOpt = array(
    array('key' => 'propre',   'label' => 'Plutôt propre',      'sub' => 'Un coup de frais suffit', 'icon' => knIcon('sparkles', array('size' => 22))),
    array('key' => 'quotidien','label' => 'Usage quotidien',    'sub' => 'Poussière, traces, miettes', 'icon' => knIcon('car', array('size' => 22))),
    array('key' => 'enfants',  'label' => 'Enfants / animaux',  'sub' => 'Taches, poils, odeurs', 'icon' => knIcon('heart', array('size' => 22))),
    array('key' => 'marque',   'label' => 'Très marqué',        'sub' => "Ça fait longtemps…", 'icon' => knIcon('droplets', array('size' => 22))),
);
$goals = array(
    array('key' => 'entretien', 'label' => 'Un entretien régulier', 'sub' => 'Garder une voiture propre', 'icon' => knIcon('calendar', array('size' => 22))),
    array('key' => 'sieges',    'label' => 'Des sièges propres',    'sub' => 'Taches sur les assises', 'icon' => knIcon('armchair', array('size' => 22))),
    array('key' => 'interieur', 'label' => 'Un grand nettoyage intérieur', 'sub' => "Tout l'habitacle en profondeur", 'icon' => knIcon('spray', array('size' => 22))),
    array('key' => 'neuf',      'label' => 'Une voiture comme neuve', 'sub' => 'Revente ou fin de leasing', 'icon' => knIcon('star', array('size' => 22))),
);
$placesOpt = array(
    array('key' => 'domicile', 'label' => 'À domicile ou au travail', 'sub' => 'Nous venons avec tout le matériel', 'icon' => knIcon('home', array('size' => 22))),
    array('key' => 'atelier',  'label' => 'À notre atelier de Visé',  'sub' => 'Tarif avantageux, espace d\'attente', 'icon' => knIcon('factory', array('size' => 22))),
);

$cfg = array(
    'booking'  => $booking_url,
    'formulas' => $formulas,
    'extras'   => $extras,
    'vehicles' => $vehicles,
    'states'   => $statesOpt,
    'goals'    => $goals,
    'places'   => $placesOpt,
    'icons'    => array('check' => knIcon('check', array('size' => 16)), 'sparkles' => knIcon('sparkles', array('size' => 18)), 'clock' => knIcon('clock', array('size' => 15)), 'calendar' => knIcon('calendar', array('size' => 18)), 'arrow' => knIcon('arrow', array('size' => 18))),
    'txt'      => array(
        'q1' => $t('q_vehicle', 'Bonjour ! Pour commencer, quel véhicule souhaitez-vous faire nettoyer ?'),
        'q2' => $t('q_state', 'Très bien. Dans quel état est-il en ce moment ?'),
        'q3' => $t('q_goal', "Et qu'aimeriez-vous obtenir ?"),
        'q4' => $t('q_reco', 'Merci ! Voici ce que je vous recommande :'),
        'q5' => $t('q_extras', 'Un petit plus pour un résultat parfait ?'),
        'q6' => $t('q_place', 'Dernière question : où souhaitez-vous le nettoyage ?'),
        'q7' => $t('q_result', 'Votre nettoyage est prêt à être réservé !'),
        'cta' => $t('cta_label', 'Prendre RDV en ligne'),
        'note' => $t('note', 'Prix « à partir de », TVA comprise. Le montant exact est confirmé à la réservation selon l\'état du véhicule.'),
        'bot' => $t('bot_name', 'Keepnew'),
    ),
);
$eyebrow = $t('eyebrow', 'SIMULATEUR');
$h2      = $t('h2', 'Trouvez le nettoyage idéal pour votre voiture');
$intro   = $t('intro', 'Répondez à quelques questions : nous vous recommandons la formule adaptée et vous voyez le prix tout de suite.');
?>
<section class="<?php echo blockClasses($block, 'cream', 'kn-ca'); ?>"<?php $caAnchor = blockAnchor($block); echo $caAnchor !== '' ? $caAnchor : ' id="' . ($knCaCount === 1 ? 'simulateur' : $uid) . '"'; ?> data-ca-root>
  <div class="container">
    <header class="kn-ca__head">
      <span class="kn-eyebrow"><?php echo htmlspecialchars($eyebrow); ?></span>
      <h2 class="kn-section__title"><?php echo htmlspecialchars($h2); ?></h2>
      <p class="kn-ca__intro"><?php echo htmlspecialchars($intro); ?></p>
    </header>
    <div class="kn-ca__layout">
      <div class="kn-ca__chat">
        <div class="kn-ca__top">
          <div class="kn-ca__progress" role="progressbar" aria-valuemin="0" aria-valuemax="6" aria-valuenow="0" aria-label="Progression du simulateur"><span data-ca-bar></span></div>
          <div class="kn-ca__nav">
            <button type="button" class="kn-ca__navbtn" data-ca-back hidden>← Retour</button>
            <span class="kn-ca__step" data-ca-step>Étape 1 / 6</span>
            <button type="button" class="kn-ca__navbtn" data-ca-restart hidden>Recommencer</button>
          </div>
        </div>
        <div class="kn-ca__log" data-ca-log aria-live="polite">
          <noscript><p class="kn-ca__bubble">Activez JavaScript pour utiliser le simulateur, ou <a href="<?php echo htmlspecialchars($booking_url); ?>">réservez directement en ligne</a>.</p></noscript>
        </div>
      </div>
      <aside class="kn-ca__summary" data-ca-summary aria-label="Votre sélection">
        <p class="kn-ca__sum-title">Votre sélection</p>
        <dl class="kn-ca__sum-list" data-ca-sumlist></dl>
        <div class="kn-ca__sum-total"><span>Total estimé</span><strong data-ca-total>—</strong></div>
      </aside>
    </div>
  </div>
</section>
<script>
(function () {
  var root = document.querySelectorAll('[data-ca-root]')[<?php echo $knCaCount - 1; ?>];
  if (!root || root.dataset.init) return; root.dataset.init = '1';
  var C = <?php echo json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var log = root.querySelector('[data-ca-log]');
  var S = { step: 0, vehicle: null, state: null, goal: null, formula: null, extras: [], place: null };
  var history = [];

  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function eur(n) { return (Math.round(n * 100) / 100).toLocaleString('fr-BE', { minimumFractionDigits: n % 1 ? 2 : 0, maximumFractionDigits: 2 }) + ' €'; }
  function find(list, k) { for (var i = 0; i < list.length; i++) if (list[i].key === k) return list[i]; return null; }
  function track(ev, data) { window.dataLayer = window.dataLayer || []; var o = { event: ev }; for (var k in data) o[k] = data[k]; window.dataLayer.push(o); }

  function recommend() {
    var g = S.goal, st = S.state, key;
    if (g === 'neuf') key = 'ran_full';
    else if (g === 'interieur') key = 'ran_int';
    else if (g === 'sieges') key = st === 'marque' ? 'ran_int' : 'shampoing';
    else key = st === 'marque' ? 'ran_int' : 'ie';
    for (var i = 0; i < C.formulas.length; i++) if (C.formulas[i].key === key) return i;
    return Math.min(1, C.formulas.length - 1);
  }
  function why() {
    var st = find(C.states, S.state), g = find(C.goals, S.goal);
    return 'Pour ' + (st ? st.label.toLowerCase() : 'votre véhicule') + ' et ' + (g ? g.label.charAt(0).toLowerCase() + g.label.slice(1) : 'votre objectif') + ', c\'est la formule la plus adaptée.';
  }
  function hasAtelierExtra() { return S.extras.some(function (i) { return C.extras[i].atelier; }); }
  function formulaPrice(f, place) {
    var v = S.vehicle || 'citadine', p = place || S.place || 'domicile';
    var x = f.prices[v + '_' + p];
    if (x == null && p === 'atelier') x = f.prices[v + '_domicile'];
    return x;
  }
  function compute() {
    if (S.formula == null) return null;
    var f = C.formulas[S.formula], base = formulaPrice(f), total = base, quote = base == null, lines = [];
    lines.push([f.name, base == null ? 'Sur devis' : eur(base)]);
    S.extras.forEach(function (i) {
      var e = C.extras[i];
      if (e.price == null) { lines.push([e.label, 'Sur devis']); }
      else { lines.push([e.label, '+ ' + eur(e.price)]); if (total != null) total += e.price; }
    });
    return { total: total, quote: quote, lines: lines, formula: f };
  }

  // ── Résumé latéral ─────────────────────────────────────────
  function summary() {
    var dl = root.querySelector('[data-ca-sumlist]'), rows = [];
    var v = find(C.vehicles, S.vehicle); if (v) rows.push(['Véhicule', v.label]);
    var st = find(C.states, S.state); if (st) rows.push(['État', st.label]);
    if (S.formula != null) rows.push(['Formule', C.formulas[S.formula].name]);
    if (S.extras.length) rows.push(['Suppléments', S.extras.map(function (i) { return C.extras[i].label; }).join(', ')]);
    var p = find(C.places, S.place); if (p) rows.push(['Lieu', p.label]);
    dl.innerHTML = rows.length ? rows.map(function (r) { return '<div><dt>' + esc(r[0]) + '</dt><dd>' + esc(r[1]) + '</dd></div>'; }).join('') : '<div class="kn-ca__sum-empty">Vos choix apparaîtront ici.</div>';
    var r = compute(), tot = root.querySelector('[data-ca-total]');
    var target = r && !r.quote ? r.total : null;
    if (r && r.quote) tot.textContent = 'Sur devis';
    else countTo(tot, target);
    root.querySelector('[data-ca-bar]').style.width = (S.step / 6 * 100) + '%';
    root.querySelector('.kn-ca__progress').setAttribute('aria-valuenow', S.step);
    root.querySelector('[data-ca-step]').textContent = S.step >= 6 ? 'Récapitulatif' : 'Étape ' + (S.step + 1) + ' / 6';
    root.querySelector('[data-ca-back]').hidden = history.length === 0;
    root.querySelector('[data-ca-restart]').hidden = S.step === 0;
    root.classList.toggle('is-final', S.step >= 6);
  }
  function countTo(el, to) {
    if (to == null) { el.textContent = '—'; el.dataset.v = ''; return; }
    var from = parseFloat(el.dataset.v || '0') || 0; el.dataset.v = to;
    if (reduce || from === to) { el.textContent = 'dès ' + eur(to); return; }
    var t0 = null, d = 600;
    function f(ts) { if (!t0) t0 = ts; var k = Math.min(1, (ts - t0) / d), e = 1 - Math.pow(1 - k, 3); el.textContent = 'dès ' + eur(Math.round(from + (to - from) * e)); if (k < 1) requestAnimationFrame(f); else el.textContent = 'dès ' + eur(to); }
    requestAnimationFrame(f);
  }

  // ── Conversation ──────────────────────────────────────────
  function botBubble(html, cb) {
    var wrap = document.createElement('div'); wrap.className = 'kn-ca__msg kn-ca__msg--bot';
    wrap.innerHTML = '<span class="kn-ca__avatar" aria-hidden="true">K</span><div class="kn-ca__bubble kn-ca__typing"><i></i><i></i><i></i></div>';
    log.appendChild(wrap); scrollIn(wrap);
    setTimeout(function () {
      var b = wrap.querySelector('.kn-ca__bubble'); b.classList.remove('kn-ca__typing'); b.innerHTML = html;
      if (cb) cb(wrap);
    }, reduce ? 0 : 650);
  }
  function userBubble(text) {
    var w = document.createElement('div'); w.className = 'kn-ca__msg kn-ca__msg--user';
    w.innerHTML = '<div class="kn-ca__bubble">' + esc(text) + '</div>'; log.appendChild(w);
  }
  function scrollIn(el) {
    var r = el.getBoundingClientRect();
    if (r.bottom > window.innerHeight - 40 || r.top < 60) el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'nearest' });
  }
  function panel(html) {
    var p = document.createElement('div'); p.className = 'kn-ca__panel'; p.innerHTML = html; log.appendChild(p);
    setTimeout(function () { scrollIn(p); var first = p.querySelector('button, a'); if (first && !reduce) first.focus({ preventScroll: true }); }, 30);
    return p;
  }
  function choices(list, onPick, opts) {
    opts = opts || {};
    var html = '<div class="kn-ca__choices' + (opts.cols ? ' kn-ca__choices--' + opts.cols : '') + '" role="radiogroup">' + list.map(function (o, i) {
      return '<button type="button" role="radio" aria-checked="false" class="kn-ca__choice" data-i="' + i + '" style="--d:' + (i * 70) + 'ms">'
        + '<span class="kn-ca__choice-ico">' + (o.svg || o.icon || '') + '</span>'
        + '<span class="kn-ca__choice-txt"><strong>' + esc(o.label) + '</strong>' + (o.sub ? '<small>' + esc(o.sub) + '</small>' : '') + '</span></button>';
    }).join('') + '</div>';
    var p = panel(html);
    p.querySelectorAll('.kn-ca__choice').forEach(function (b) {
      b.addEventListener('click', function () {
        b.setAttribute('aria-checked', 'true'); b.classList.add('is-picked');
        p.classList.add('is-done');
        setTimeout(function () { p.remove(); onPick(list[+b.dataset.i]); }, reduce ? 0 : 220);
      });
    });
  }

  function push() { history.push(JSON.parse(JSON.stringify({ S: S, html: log.innerHTML }))); }
  function go(step) { S.step = step; summary(); STEPS[step](); }

  var STEPS = [
    function () { botBubble(esc(C.txt.q1), function () { choices(C.vehicles, function (o) { push(); S.vehicle = o.key; userBubble(o.label); track('simulateur_auto_etape', { etape: 1, valeur: o.key }); go(1); }, { cols: 2 }); }); },
    function () { botBubble(esc(C.txt.q2), function () { choices(C.states, function (o) { push(); S.state = o.key; userBubble(o.label); track('simulateur_auto_etape', { etape: 2, valeur: o.key }); go(2); }, { cols: 2 }); }); },
    function () { botBubble(esc(C.txt.q3), function () { choices(C.goals, function (o) { push(); S.goal = o.key; userBubble(o.label); track('simulateur_auto_etape', { etape: 3, valeur: o.key }); S.formula = recommend(); go(3); }, { cols: 2 }); }); },
    function () { botBubble(C.icons.sparkles + ' ' + esc(C.txt.q4), function () { renderReco(); }); },
    function () {
      if (!S.extras.length) C.extras.forEach(function (e, i) { if (e.pre && e.pre === S.state && S.extras.indexOf(i) < 0) S.extras.push(i); });
      summary();
      botBubble(esc(C.txt.q5), function () { renderExtras(); });
    },
    function () {
      if (hasAtelierExtra()) {
        S.place = 'atelier';
        botBubble('Le polissage et la céramique se font dans notre <strong>atelier de Visé</strong> : je l\'ai sélectionné pour vous.', function () { go(6); });
        return;
      }
      botBubble(esc(C.txt.q6), function () { choices(C.places.map(function (pl) {
        var price = formulaPrice(C.formulas[S.formula], pl.key);
        return { key: pl.key, label: pl.label, icon: pl.icon, sub: pl.sub + (price != null ? ' · dès ' + eur(price) : '') };
      }), function (o) { push(); S.place = o.key; userBubble(o.label); track('simulateur_auto_etape', { etape: 6, valeur: o.key }); go(6); }); });
    },
    function () { botBubble(esc(C.txt.q7), function () { renderResult(); }); }
  ];

  function formulaCard(f, i, rec) {
    var price = formulaPrice(f);
    return '<article class="kn-ca__reco' + (rec ? ' is-rec' : '') + '">'
      + (f.badge ? '<span class="kn-ca__badge">' + esc(f.badge) + '</span>' : '')
      + '<h3 class="kn-ca__reco-name">' + esc(f.name) + '</h3>'
      + (rec ? '<p class="kn-ca__why">' + esc(why()) + '</p>' : (f.tagline ? '<p class="kn-ca__why">' + esc(f.tagline) + '</p>' : ''))
      + '<ul class="kn-ca__points">' + f.points.map(function (p, k) { return '<li style="--d:' + (k * 90 + 150) + 'ms">' + C.icons.check + '<span>' + esc(p) + '</span></li>'; }).join('') + '</ul>'
      + '<div class="kn-ca__reco-foot"><span class="kn-ca__price">' + (price == null ? 'Sur devis' : '<small>dès</small> ' + eur(price)) + '</span>'
      + (f.duration ? '<span class="kn-ca__dur">' + C.icons.clock + esc(f.duration) + '</span>' : '') + '</div></article>';
  }
  function renderReco() {
    var f = C.formulas[S.formula];
    var p = panel(formulaCard(f, S.formula, true)
      + '<div class="kn-ca__actions"><button type="button" class="kn-btn kn-ca__primary" data-ok>Choisir cette formule</button>'
      + '<button type="button" class="kn-ca__link" data-cmp aria-expanded="false">Comparer avec les autres formules</button></div>'
      + '<div class="kn-ca__compare" data-cmpbox hidden></div>');
    p.querySelector('[data-ok]').addEventListener('click', function () { p.remove(); push(); userBubble(f.name); track('simulateur_auto_etape', { etape: 4, valeur: f.key }); go(4); });
    var box = p.querySelector('[data-cmpbox]'), cmp = p.querySelector('[data-cmp]');
    cmp.addEventListener('click', function () {
      var open = box.hidden; box.hidden = !open; cmp.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open && !box.innerHTML) {
        box.innerHTML = '<div class="kn-ca__mini">' + C.formulas.map(function (o, i) {
          var pr = formulaPrice(o);
          return '<button type="button" class="kn-ca__minicard' + (i === S.formula ? ' is-on' : '') + '" data-f="' + i + '"><strong>' + esc(o.name) + '</strong><small>' + esc(o.tagline) + '</small><span>' + (pr == null ? 'Sur devis' : 'dès ' + eur(pr)) + '</span></button>';
        }).join('') + '</div>';
        box.querySelectorAll('[data-f]').forEach(function (b) { b.addEventListener('click', function () {
          S.formula = +b.dataset.f; var o = C.formulas[S.formula]; p.remove(); push(); userBubble(o.name); track('simulateur_auto_etape', { etape: 4, valeur: o.key, change: 1 }); go(4);
        }); });
      }
    });
  }
  function renderExtras() {
    var p = panel('<div class="kn-ca__extras">' + C.extras.map(function (e, i) {
      var on = S.extras.indexOf(i) >= 0;
      return '<button type="button" role="checkbox" aria-checked="' + on + '" class="kn-ca__extra' + (on ? ' is-on' : '') + '" data-e="' + i + '" style="--d:' + (i * 70) + 'ms">'
        + '<span class="kn-ca__choice-ico">' + e.icon + '</span><span class="kn-ca__choice-txt"><strong>' + esc(e.label) + '</strong><small>' + esc(e.desc) + (e.atelier ? ' (atelier)' : '') + '</small></span>'
        + '<span class="kn-ca__extra-price">' + (e.price == null ? 'sur devis' : '+ ' + eur(e.price)) + '</span><span class="kn-ca__tick" aria-hidden="true">' + C.icons.check + '</span></button>';
    }).join('') + '</div><div class="kn-ca__actions"><button type="button" class="kn-btn kn-ca__primary" data-next>Continuer</button><button type="button" class="kn-ca__link" data-none>Non merci</button></div>');
    p.querySelectorAll('[data-e]').forEach(function (b) { b.addEventListener('click', function () {
      var i = +b.dataset.e, k = S.extras.indexOf(i);
      if (k >= 0) S.extras.splice(k, 1); else S.extras.push(i);
      b.classList.toggle('is-on', k < 0); b.setAttribute('aria-checked', k < 0 ? 'true' : 'false'); summary();
    }); });
    function done(none) {
      if (none) S.extras = [];
      p.remove(); push();
      userBubble(S.extras.length ? S.extras.map(function (i) { return C.extras[i].label; }).join(' + ') : 'Pas de supplément');
      track('simulateur_auto_etape', { etape: 5, valeur: S.extras.map(function (i) { return C.extras[i].label; }).join('|') || 'aucun' });
      go(5);
    }
    p.querySelector('[data-next]').addEventListener('click', function () { done(false); });
    p.querySelector('[data-none]').addEventListener('click', function () { done(true); });
  }
  function renderResult() {
    var r = compute(), f = r.formula, url = f.url || C.booking;
    var atel = S.extras.filter(function (i) { return C.extras[i].url; }).map(function (i) { return '<a href="' + esc(C.extras[i].url) + '">En savoir plus : ' + esc(C.extras[i].label) + ' →</a>'; }).join(' ');
    var p = panel('<div class="kn-ca__result">'
      + '<span class="kn-ca__result-label">Votre estimation</span>'
      + '<div class="kn-ca__result-price" data-rp>' + (r.quote ? 'Sur devis' : '') + '</div>'
      + '<div class="kn-ca__lines">' + r.lines.map(function (l) { return '<div class="kn-ca__line"><span>' + esc(l[0]) + '</span><strong>' + esc(l[1]) + '</strong></div>'; }).join('')
      + '<div class="kn-ca__line"><span>Lieu</span><strong>' + esc((find(C.places, S.place) || {}).label || '') + '</strong></div></div>'
      + '<ul class="kn-ca__badges"><li>Paiement après la prestation</li><li>Réservation en 2 min</li><li>Matériel fourni</li></ul>'
      + '<a class="kn-btn kn-ca__cta" href="' + esc(url) + '" data-cta>' + C.icons.calendar + esc(C.txt.cta) + '</a>'
      + (atel ? '<p class="kn-ca__more">' + atel + '</p>' : '')
      + '<p class="kn-ca__note">' + esc(C.txt.note) + '</p>'
      + '<button type="button" class="kn-ca__link kn-ca__again" data-again>Recommencer la simulation</button></div>');
    if (!r.quote) { var el = p.querySelector('[data-rp]'); el.dataset.v = '0'; countTo(el, r.total); }
    track('simulateur_auto_resultat', { formule: f.key || f.name, vehicule: S.vehicle, lieu: S.place, supplements: S.extras.map(function (i) { return C.extras[i].label; }).join('|'), total: r.quote ? 'devis' : r.total });
    p.querySelector('[data-cta]').addEventListener('click', function () { track('simulateur_auto_rdv', { formule: f.key || f.name, total: r.quote ? 'devis' : r.total }); });
    p.querySelector('[data-again]').addEventListener('click', restart);
  }

  function back() {
    var h = history.pop(); if (!h) return;
    S = h.S; log.innerHTML = h.html;
    var bots = log.querySelectorAll('.kn-ca__msg--bot'); if (bots.length) bots[bots.length - 1].remove();
    summary(); STEPS[S.step]();
  }
  function restart() {
    history = []; S = { step: 0, vehicle: null, state: null, goal: null, formula: null, extras: [], place: null };
    log.innerHTML = ''; go(0);
    root.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
  }
  root.querySelector('[data-ca-back]').addEventListener('click', back);
  root.querySelector('[data-ca-restart]').addEventListener('click', restart);
  log.innerHTML = '';
  go(0);
})();
</script>
