<?php
require_once __DIR__ . '/_block_helpers.php';
require_once __DIR__ . '/_car_advisor_config.php';
$booking_url = isset($booking_url) ? $booking_url : '#';

static $knCaCount = 0;
$knCaCount++;
$uid = 'kn-ca-' . $knCaCount;

$t = function ($key, $default) use ($block) {
    return isset($block[$key]) && trim((string) $block[$key]) !== '' ? (string) $block[$key] : $default;
};

// Configuration globale (Admin › Simulateur auto) ; repli : anciennes données du bloc
$CA = knCaLoad($block);
if (!$CA['formulas']) return;

$svgCar = function ($roof) {
    return '<svg viewBox="0 0 64 32" width="56" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round" aria-hidden="true">' . $roof . '<circle cx="18" cy="25" r="4.5"/><circle cx="48" cy="25" r="4.5"/></svg>';
};
$vehicles = array(
    array('key' => 'citadine',   'label' => 'Citadine',                 'sub' => 'Polo, Clio, 208…',      'svg' => $svgCar('<path d="M6 25H3v-6l5-2 6-7h20l8 7 9 2v6h-3M13.5 25h30"/>')),
    array('key' => 'berline',    'label' => 'Berline / Break',          'sub' => 'Golf, Série 3, Passat…', 'svg' => $svgCar('<path d="M6 25H2v-5l7-3 8-7h18l10 7 12 2v6h-3M13.5 25h30"/>')),
    array('key' => 'suv',        'label' => 'SUV / 4×4',                'sub' => '3008, Tiguan, X1…',      'svg' => $svgCar('<path d="M6 25H3v-8l4-2 5-8h26l8 8 9 1v9h-3M13.5 25h30"/>')),
    array('key' => 'utilitaire', 'label' => 'Utilitaire / Camionnette', 'sub' => 'Kangoo, Transit…',       'svg' => $svgCar('<path d="M6 25H3V6h34l7 8 13 2v9h-4M13.5 25h30M37 6v8h7"/>')),
);
$stateIcons = array('propre' => 'sparkles', 'quotidien' => 'car', 'enfants' => 'heart', 'marque' => 'droplets');
$goalIcons  = array('entretien' => 'calendar', 'sieges' => 'armchair', 'interieur' => 'spray', 'neuf' => 'star');
$states = array();
foreach ($CA['states'] as $s) { $s['icon'] = knIcon($stateIcons[$s['key']], array('size' => 22)); $states[] = $s; }
$goals = array();
foreach ($CA['goals'] as $g) { $g['icon'] = knIcon($goalIcons[$g['key']], array('size' => 22)); $goals[] = $g; }
$concerns = array();
foreach ($CA['concerns'] as $c) { $c['iconHtml'] = knIcon($c['icon'], array('size' => 20, 'fallback' => 'check')); $concerns[] = $c; }
$extras = array();
foreach ($CA['extras'] as $e) { $e['iconHtml'] = knIcon($e['icon'], array('size' => 20, 'fallback' => 'sparkles')); $extras[] = $e; }
$places = array(
    array('key' => 'domicile', 'label' => 'À domicile ou au travail', 'sub' => 'Nous venons avec tout le matériel', 'icon' => knIcon('home', array('size' => 22))),
    array('key' => 'atelier',  'label' => 'À notre atelier de Visé',  'sub' => "Tarif avantageux, espace d'attente", 'icon' => knIcon('factory', array('size' => 22))),
);

$cfg = array(
    'booking'  => $booking_url,
    'formulas' => $CA['formulas'],
    'extras'   => $extras,
    'vehicles' => $vehicles,
    'states'   => $states,
    'concerns' => $concerns,
    'goals'    => $goals,
    'places'   => $places,
    'matrix'   => $CA['matrix'],
    'txt'      => $CA['txt'],
    'icons'    => array('check' => knIcon('check', array('size' => 16)), 'sparkles' => knIcon('sparkles', array('size' => 18)), 'clock' => knIcon('clock', array('size' => 15)), 'calendar' => knIcon('calendar', array('size' => 18))),
);
$eyebrow = $t('eyebrow', 'SIMULATEUR');
$h2      = $t('h2', 'Trouvez le nettoyage idéal pour votre voiture');
$intro   = $t('intro', 'Répondez à quelques questions : nous vous recommandons la formule adaptée et vous voyez le prix tout de suite.');
$caAnchor = blockAnchor($block);
?>
<section class="<?php echo blockClasses($block, 'cream', 'kn-ca'); ?>"<?php echo $caAnchor !== '' ? $caAnchor : ' id="' . ($knCaCount === 1 ? 'simulateur' : $uid) . '"'; ?> data-ca-root>
  <div class="container">
    <header class="kn-ca__head">
      <span class="kn-eyebrow"><?php echo htmlspecialchars($eyebrow); ?></span>
      <h2 class="kn-section__title"><?php echo htmlspecialchars($h2); ?></h2>
      <p class="kn-ca__intro"><?php echo htmlspecialchars($intro); ?></p>
    </header>
    <div class="kn-ca__layout">
      <div class="kn-ca__chat">
        <div class="kn-ca__top">
          <div class="kn-ca__progress" role="progressbar" aria-valuemin="0" aria-valuemax="7" aria-valuenow="0" aria-label="Progression du simulateur"><span data-ca-bar></span></div>
          <div class="kn-ca__nav">
            <button type="button" class="kn-ca__navbtn" data-ca-back hidden>← Retour</button>
            <span class="kn-ca__step" data-ca-step>Étape 1 / 7</span>
            <button type="button" class="kn-ca__navbtn" data-ca-restart hidden>Recommencer</button>
          </div>
        </div>
        <div class="kn-ca__log" data-ca-log aria-live="polite">
          <noscript><p class="kn-ca__bubble">Activez JavaScript pour utiliser le simulateur, ou <a href="<?php echo htmlspecialchars($booking_url); ?>">réservez directement en ligne</a>.</p></noscript>
        </div>
      </div>
      <aside class="kn-ca__summary" aria-label="Votre sélection">
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
  var NSTEPS = 7;
  function fresh() { return { step: 0, vehicle: null, state: null, concerns: [], goal: null, base: null, formula: null, alt: null, extras: [], extrasInit: false, place: null }; }
  var S = fresh(), hist = [];

  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function eur(n) { return (Math.round(n * 100) / 100).toLocaleString('fr-BE', { minimumFractionDigits: n % 1 ? 2 : 0, maximumFractionDigits: 2 }) + ' €'; }
  function find(list, k) { for (var i = 0; i < list.length; i++) if (list[i].key === k) return list[i]; return null; }
  function idxOf(key) { for (var i = 0; i < C.formulas.length; i++) if (C.formulas[i].key === key) return i; return -1; }
  function track(ev, data) { window.dataLayer = window.dataLayer || []; var o = { event: ev }; for (var k in data) o[k] = data[k]; window.dataLayer.push(o); }
  function lcFirst(s) { return s ? s.charAt(0).toLowerCase() + s.slice(1) : s; }
  function joinFr(a) { return a.length < 2 ? (a[0] || '') : a.slice(0, -1).join(', ') + ' et ' + a[a.length - 1]; }

  // ── Recommandation ─────────────────────────────────────────
  // 1) formule de base = matrice objectif × état
  // 2) besoins = ce que couvre la base + ce que demandent les soucis cochés
  // 3) formule retenue = la première, à partir du niveau de la base (ordre de l'admin), qui couvre tous les besoins
  function recommend() {
    var baseKey = C.matrix[S.goal] && C.matrix[S.goal][S.state];
    var base = idxOf(baseKey); if (base < 0) base = 0;
    var need = {};
    (C.formulas[base].covers || []).forEach(function (t) { need[t] = 1; });
    S.concerns.forEach(function (k) { var c = find(C.concerns, k); if (c) c.needs.forEach(function (t) { need[t] = 1; }); });
    var tags = Object.keys(need), pick = C.formulas.length - 1;
    for (var i = base; i < C.formulas.length; i++) {
      var cov = C.formulas[i].covers || [];
      if (tags.every(function (t) { return cov.indexOf(t) >= 0; })) { pick = i; break; }
    }
    S.base = base; S.formula = pick;
    if (pick !== base) S.alt = { i: base, kind: 'eco' };
    else if (pick + 1 < C.formulas.length) S.alt = { i: pick + 1, kind: 'plus' };
    else if (pick > 0) S.alt = { i: pick - 1, kind: 'eco' };
    else S.alt = null;
  }
  function why() {
    var st = find(C.states, S.state), g = find(C.goals, S.goal);
    var parts = S.concerns.map(function (k) { var c = find(C.concerns, k); return c ? lcFirst(c.label) : ''; }).filter(Boolean);
    var s = 'Vu l\'état de votre véhicule (' + (st ? lcFirst(st.label) : '') + ')';
    if (parts.length) s += ', ' + joinFr(parts) + ' à traiter';
    if (g) s += ' et votre objectif (' + lcFirst(g.label) + ')';
    return s + ', c\'est la formule la plus adaptée.';
  }
  function extraIncluded(e) { var f = C.formulas[S.formula]; return f && e.included_in && e.included_in.indexOf(f.key) >= 0; }
  function suggestedExtras() {
    var out = [];
    S.concerns.forEach(function (k) { var c = find(C.concerns, k); if (c) c.extras.forEach(function (ek) { var i = -1; C.extras.forEach(function (e, j) { if (e.key === ek) i = j; }); if (i >= 0 && !extraIncluded(C.extras[i]) && out.indexOf(i) < 0) out.push(i); }); });
    return out;
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
    var f = C.formulas[S.formula], base = formulaPrice(f), total = base, lines = [];
    lines.push([f.name, base == null ? 'Sur devis' : eur(base)]);
    S.extras.forEach(function (i) {
      var e = C.extras[i];
      if (e.price == null) lines.push([e.label, 'Sur devis']);
      else { lines.push([e.label, '+ ' + eur(e.price)]); if (total != null) total += e.price; }
    });
    return { total: total, quote: base == null, lines: lines, formula: f };
  }

  // ── Résumé ─────────────────────────────────────────────────
  function summary() {
    var dl = root.querySelector('[data-ca-sumlist]'), rows = [];
    var v = find(C.vehicles, S.vehicle); if (v) rows.push(['Véhicule', v.label]);
    var st = find(C.states, S.state); if (st) rows.push(['État', st.label]);
    if (S.concerns.length) rows.push(['À traiter', S.concerns.map(function (k) { return (find(C.concerns, k) || {}).label; }).join(', ')]);
    if (S.formula != null && S.step >= 4) rows.push(['Formule', C.formulas[S.formula].name]);
    if (S.extras.length) rows.push(['Suppléments', S.extras.map(function (i) { return C.extras[i].label; }).join(', ')]);
    var p = find(C.places, S.place); if (p) rows.push(['Lieu', p.label]);
    dl.innerHTML = rows.length ? rows.map(function (r) { return '<div><dt>' + esc(r[0]) + '</dt><dd>' + esc(r[1]) + '</dd></div>'; }).join('') : '<div class="kn-ca__sum-empty">Vos choix apparaîtront ici.</div>';
    var r = S.step >= 4 ? compute() : null, tot = root.querySelector('[data-ca-total]');
    if (r && r.quote) { tot.textContent = 'Sur devis'; tot.dataset.v = ''; } else countTo(tot, r ? r.total : null);
    root.querySelector('[data-ca-bar]').style.width = (S.step / NSTEPS * 100) + '%';
    root.querySelector('.kn-ca__progress').setAttribute('aria-valuenow', S.step);
    root.querySelector('[data-ca-step]').textContent = S.step >= NSTEPS ? 'Récapitulatif' : 'Étape ' + (S.step + 1) + ' / ' + NSTEPS;
    root.querySelector('[data-ca-back]').hidden = hist.length === 0;
    root.querySelector('[data-ca-restart]').hidden = S.step === 0;
    root.classList.toggle('is-final', S.step >= NSTEPS);
  }
  function countTo(el, to) {
    if (to == null) { el.textContent = '—'; el.dataset.v = ''; return; }
    var from = parseFloat(el.dataset.v || '0') || 0; el.dataset.v = to;
    if (reduce || from === to) { el.textContent = 'dès ' + eur(to); return; }
    var t0 = null, d = 600;
    function f(ts) { if (!t0) t0 = ts; var k = Math.min(1, (ts - t0) / d), e = 1 - Math.pow(1 - k, 3); el.textContent = 'dès ' + eur(Math.round(from + (to - from) * e)); if (k < 1) requestAnimationFrame(f); else el.textContent = 'dès ' + eur(to); }
    requestAnimationFrame(f);
  }

  // ── Conversation ───────────────────────────────────────────
  function botBubble(html, cb) {
    var w = document.createElement('div'); w.className = 'kn-ca__msg kn-ca__msg--bot';
    w.innerHTML = '<span class="kn-ca__avatar" aria-hidden="true">' + esc((C.txt.bot || 'K').charAt(0)) + '</span><div class="kn-ca__bubble kn-ca__typing"><i></i><i></i><i></i></div>';
    log.appendChild(w); scrollIn(w);
    setTimeout(function () { var b = w.querySelector('.kn-ca__bubble'); b.classList.remove('kn-ca__typing'); b.innerHTML = html; if (cb) cb(w); }, reduce ? 0 : 650);
  }
  function userBubble(html) { var w = document.createElement('div'); w.className = 'kn-ca__msg kn-ca__msg--user'; w.innerHTML = '<div class="kn-ca__bubble">' + html + '</div>'; log.appendChild(w); }
  function scrollIn(el) { var r = el.getBoundingClientRect(); if (r.bottom > window.innerHeight - 40 || r.top < 60) el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'nearest' }); }
  function panel(html) {
    var p = document.createElement('div'); p.className = 'kn-ca__panel'; p.innerHTML = html; log.appendChild(p);
    setTimeout(function () { scrollIn(p); var first = p.querySelector('button, a'); if (first && !reduce) first.focus({ preventScroll: true }); }, 30);
    return p;
  }
  function choices(list, onPick, cols) {
    var p = panel('<div class="kn-ca__choices' + (cols ? ' kn-ca__choices--' + cols : '') + '" role="radiogroup">' + list.map(function (o, i) {
      return '<button type="button" role="radio" aria-checked="false" class="kn-ca__choice" data-i="' + i + '" style="--d:' + (i * 70) + 'ms"><span class="kn-ca__choice-ico">' + (o.svg || o.icon || '') + '</span><span class="kn-ca__choice-txt"><strong>' + esc(o.label) + '</strong>' + (o.sub ? '<small>' + esc(o.sub) + '</small>' : '') + '</span></button>';
    }).join('') + '</div>');
    p.querySelectorAll('.kn-ca__choice').forEach(function (b) { b.addEventListener('click', function () {
      b.setAttribute('aria-checked', 'true'); b.classList.add('is-picked'); p.classList.add('is-done');
      setTimeout(function () { p.remove(); onPick(list[+b.dataset.i]); }, reduce ? 0 : 220);
    }); });
  }
  function push() { hist.push(JSON.parse(JSON.stringify({ S: S, html: log.innerHTML }))); }
  function go(step) { S.step = step; summary(); STEPS[step](); }

  var STEPS = [
    function () { botBubble(esc(C.txt.q1), function () { choices(C.vehicles, function (o) { push(); S.vehicle = o.key; userBubble(esc(o.label)); track('simulateur_auto_etape', { etape: 1, valeur: o.key }); go(1); }, 2); }); },
    function () { botBubble(esc(C.txt.q2), function () { choices(C.states, function (o) { push(); S.state = o.key; userBubble(esc(o.label)); track('simulateur_auto_etape', { etape: 2, valeur: o.key }); go(2); }, 2); }); },
    function () { botBubble(esc(C.txt.q3), renderConcerns); },
    function () { botBubble(esc(C.txt.q4), function () { choices(C.goals, function (o) { push(); S.goal = o.key; userBubble(esc(o.label)); track('simulateur_auto_etape', { etape: 4, valeur: o.key }); recommend(); S.extrasInit = false; go(4); }, 2); }); },
    function () { botBubble(C.icons.sparkles + ' ' + esc(C.txt.q5), renderReco); },
    function () {
      if (!S.extrasInit) { S.extras = suggestedExtras(); S.extrasInit = true; }
      summary(); botBubble(esc(C.txt.q6), renderExtras);
    },
    function () {
      if (hasAtelierExtra()) { S.place = 'atelier'; botBubble('Le polissage et la céramique se font dans notre <strong>atelier de Visé</strong> : je l\'ai sélectionné pour vous.', function () { go(7); }); return; }
      botBubble(esc(C.txt.q7), function () { choices(C.places.map(function (pl) {
        var price = formulaPrice(C.formulas[S.formula], pl.key);
        return { key: pl.key, label: pl.label, icon: pl.icon, sub: pl.sub + (price != null ? ' · dès ' + eur(price) : '') };
      }), function (o) { push(); S.place = o.key; userBubble(esc(o.label)); track('simulateur_auto_etape', { etape: 7, valeur: o.key }); go(7); }); });
    },
    function () { botBubble(esc(C.txt.q8), renderResult); }
  ];

  function renderConcerns() {
    var sel = S.concerns.slice();
    var p = panel('<div class="kn-ca__extras kn-ca__concerns">' + C.concerns.map(function (c, i) {
      var on = sel.indexOf(c.key) >= 0;
      return '<button type="button" role="checkbox" aria-checked="' + on + '" class="kn-ca__extra kn-ca__concern' + (on ? ' is-on' : '') + '" data-c="' + esc(c.key) + '" style="--d:' + (i * 60) + 'ms"><span class="kn-ca__choice-ico">' + c.iconHtml + '</span><span class="kn-ca__choice-txt"><strong>' + esc(c.label) + '</strong></span><span class="kn-ca__tick" aria-hidden="true">' + C.icons.check + '</span></button>';
    }).join('') + '</div><div class="kn-ca__actions"><button type="button" class="kn-btn kn-ca__primary" data-next>Continuer</button><button type="button" class="kn-ca__link" data-none>Rien de particulier</button></div>');
    p.querySelectorAll('[data-c]').forEach(function (b) { b.addEventListener('click', function () {
      var k = b.dataset.c, j = sel.indexOf(k);
      if (j >= 0) sel.splice(j, 1); else sel.push(k);
      b.classList.toggle('is-on', j < 0); b.setAttribute('aria-checked', j < 0 ? 'true' : 'false');
    }); });
    function done(none) {
      p.remove(); push(); S.concerns = none ? [] : C.concerns.filter(function (c) { return sel.indexOf(c.key) >= 0; }).map(function (c) { return c.key; });
      userBubble(S.concerns.length ? S.concerns.map(function (k) { return '<span class="kn-ca__tag">' + esc(find(C.concerns, k).label) + '</span>'; }).join('') : 'Rien de particulier');
      track('simulateur_auto_etape', { etape: 3, valeur: S.concerns.join('|') || 'aucun', soucis: S.concerns.join('|') });
      go(3);
    }
    p.querySelector('[data-next]').addEventListener('click', function () { done(false); });
    p.querySelector('[data-none]').addEventListener('click', function () { done(true); });
  }

  function points(f) { return '<ul class="kn-ca__points">' + f.points.map(function (pt, k) { return '<li style="--d:' + (k * 90 + 150) + 'ms">' + C.icons.check + '<span>' + esc(pt) + '</span></li>'; }).join('') + '</ul>'; }
  function priceTag(f) { var pr = formulaPrice(f); return pr == null ? 'Sur devis' : '<small>dès</small> ' + eur(pr); }
  function pickFormula(i, how) {
    S.formula = i; var f = C.formulas[i];
    push(); userBubble(esc(f.name)); S.extrasInit = false;
    track('simulateur_auto_etape', { etape: 5, valeur: f.key, choix: how });
    if (how === 'alternative') track('simulateur_auto_alternative', { formule: f.key, alternative_choisie: S.alt ? S.alt.kind : '' });
    go(5);
  }
  function renderReco() {
    var f = C.formulas[S.formula], a = S.alt ? C.formulas[S.alt.i] : null;
    var html = '<article class="kn-ca__reco is-rec">' + (f.badge ? '<span class="kn-ca__badge">' + esc(f.badge) + '</span>' : '')
      + '<span class="kn-ca__reco-kicker">Recommandé pour vous</span><h3 class="kn-ca__reco-name">' + esc(f.name) + '</h3>'
      + '<p class="kn-ca__why">' + esc(why()) + '</p>' + points(f)
      + '<div class="kn-ca__reco-foot"><span class="kn-ca__price">' + priceTag(f) + '</span>' + (f.duration ? '<span class="kn-ca__dur">' + C.icons.clock + esc(f.duration) + '</span>' : '') + '</div>'
      + '<button type="button" class="kn-btn kn-ca__primary kn-ca__reco-btn" data-ok>Choisir cette formule</button></article>';
    if (a) html += '<article class="kn-ca__alt"><span class="kn-ca__alt-kind">' + (S.alt.kind === 'eco' ? 'Plus économique' : 'Pour aller plus loin') + '</span>'
      + '<div class="kn-ca__alt-body"><strong>' + esc(a.name) + '</strong><small>' + esc(a.tagline) + '</small></div>'
      + '<span class="kn-ca__alt-price">' + priceTag(a) + '</span><button type="button" class="kn-ca__link" data-alt>Choisir plutôt celle-ci</button></article>';
    html += '<div class="kn-ca__actions"><button type="button" class="kn-ca__link" data-cmp aria-expanded="false">Comparer toutes les formules</button></div><div class="kn-ca__compare" data-cmpbox hidden></div>';
    var p = panel(html);
    p.querySelector('[data-ok]').addEventListener('click', function () { p.remove(); pickFormula(S.formula, 'recommandation'); });
    var ab = p.querySelector('[data-alt]'); if (ab) ab.addEventListener('click', function () { p.remove(); pickFormula(S.alt.i, 'alternative'); });
    var box = p.querySelector('[data-cmpbox]'), cmp = p.querySelector('[data-cmp]');
    cmp.addEventListener('click', function () {
      var open = box.hidden; box.hidden = !open; cmp.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open && !box.innerHTML) {
        box.innerHTML = '<div class="kn-ca__mini">' + C.formulas.map(function (o, i) {
          var pr = formulaPrice(o);
          return '<button type="button" class="kn-ca__minicard' + (i === S.formula ? ' is-on' : '') + '" data-f="' + i + '"><strong>' + esc(o.name) + (i === S.formula ? ' · recommandé' : '') + '</strong><small>' + esc(o.tagline) + '</small><span>' + (pr == null ? 'Sur devis' : 'dès ' + eur(pr)) + '</span></button>';
        }).join('') + '</div>';
        box.querySelectorAll('[data-f]').forEach(function (b) { b.addEventListener('click', function () { p.remove(); pickFormula(+b.dataset.f, 'comparatif'); }); });
      }
    });
  }
  function renderExtras() {
    var sug = suggestedExtras();
    var list = C.extras.map(function (e, i) { return i; }).filter(function (i) { return !extraIncluded(C.extras[i]); });
    if (!list.length) { push(); userBubble('Pas de supplément'); go(6); return; }
    var p = panel('<div class="kn-ca__extras">' + list.map(function (i, n) {
      var e = C.extras[i], on = S.extras.indexOf(i) >= 0, rec = sug.indexOf(i) >= 0;
      return '<button type="button" role="checkbox" aria-checked="' + on + '" class="kn-ca__extra' + (on ? ' is-on' : '') + '" data-e="' + i + '" style="--d:' + (n * 70) + 'ms">'
        + '<span class="kn-ca__choice-ico">' + e.iconHtml + '</span><span class="kn-ca__choice-txt"><strong>' + esc(e.label) + (rec ? ' <em class="kn-ca__rec">Conseillé pour vous</em>' : '') + '</strong><small>' + esc(e.desc) + (e.atelier ? ' (atelier)' : '') + '</small></span>'
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
      userBubble(esc(S.extras.length ? S.extras.map(function (i) { return C.extras[i].label; }).join(' + ') : 'Pas de supplément'));
      track('simulateur_auto_etape', { etape: 6, valeur: S.extras.map(function (i) { return C.extras[i].key; }).join('|') || 'aucun' });
      go(6);
    }
    p.querySelector('[data-next]').addEventListener('click', function () { done(false); });
    p.querySelector('[data-none]').addEventListener('click', function () { done(true); });
  }
  function renderResult() {
    var r = compute(), f = r.formula, url = f.cta_url || C.booking;
    var more = S.extras.filter(function (i) { return C.extras[i].url; }).map(function (i) { return '<a href="' + esc(C.extras[i].url) + '">En savoir plus : ' + esc(C.extras[i].label) + ' →</a>'; }).join(' ');
    var treated = S.concerns.map(function (k) { return '<li>' + esc(find(C.concerns, k).label) + '</li>'; }).join('');
    var p = panel('<div class="kn-ca__result"><span class="kn-ca__result-label">Votre estimation</span>'
      + '<div class="kn-ca__result-price" data-rp>' + (r.quote ? 'Sur devis' : '') + '</div>'
      + '<div class="kn-ca__lines">' + r.lines.map(function (l) { return '<div class="kn-ca__line"><span>' + esc(l[0]) + '</span><strong>' + esc(l[1]) + '</strong></div>'; }).join('')
      + '<div class="kn-ca__line"><span>Lieu</span><strong>' + esc((find(C.places, S.place) || {}).label || '') + '</strong></div></div>'
      + (treated ? '<p class="kn-ca__treated-title">Nous nous occupons de :</p><ul class="kn-ca__treated">' + treated + '</ul>' : '')
      + '<ul class="kn-ca__badges"><li>Paiement après la prestation</li><li>Réservation en 2 min</li><li>Matériel fourni</li></ul>'
      + '<a class="kn-btn kn-ca__cta" href="' + esc(url) + '" data-cta>' + C.icons.calendar + esc(C.txt.cta) + '</a>'
      + (more ? '<p class="kn-ca__more">' + more + '</p>' : '')
      + '<p class="kn-ca__note">' + esc(C.txt.note) + '</p>'
      + '<button type="button" class="kn-ca__link kn-ca__again" data-again>Recommencer la simulation</button></div>');
    if (!r.quote) { var el = p.querySelector('[data-rp]'); el.dataset.v = '0'; countTo(el, r.total); }
    track('simulateur_auto_resultat', { formule: f.key, vehicule: S.vehicle, etat: S.state, soucis: S.concerns.join('|'), lieu: S.place, supplements: S.extras.map(function (i) { return C.extras[i].key; }).join('|'), total: r.quote ? 'devis' : r.total });
    p.querySelector('[data-cta]').addEventListener('click', function () { track('simulateur_auto_rdv', { formule: f.key, total: r.quote ? 'devis' : r.total }); });
    p.querySelector('[data-again]').addEventListener('click', restart);
  }

  function back() {
    var h = hist.pop(); if (!h) return;
    S = h.S; log.innerHTML = h.html;
    var bots = log.querySelectorAll('.kn-ca__msg--bot'); if (bots.length) bots[bots.length - 1].remove();
    summary(); STEPS[S.step]();
  }
  function restart() { hist = []; S = fresh(); log.innerHTML = ''; go(0); root.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }); }
  root.querySelector('[data-ca-back]').addEventListener('click', back);
  root.querySelector('[data-ca-restart]').addEventListener('click', restart);
  log.innerHTML = '';
  go(0);
})();
</script>
