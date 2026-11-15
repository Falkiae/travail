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
$intIcons = array('propre' => 'sparkles', 'taches' => 'armchair', 'sale' => 'spray', 'encrasse' => 'droplets');
$extIcons = array('non' => 'home', 'lavage' => 'car', 'neuf' => 'star');
$interior = array();
foreach ($CA['interior'] as $s) { $s['icon'] = knIcon($intIcons[$s['key']], array('size' => 22)); $interior[] = $s; }
$exterior = array();
foreach ($CA['exterior'] as $s) { $s['icon'] = knIcon($extIcons[$s['key']], array('size' => 22)); $exterior[] = $s; }
$extras = array();
foreach ($CA['extras'] as $e) { $e['iconHtml'] = knIcon($e['icon'], array('size' => 18, 'fallback' => 'sparkles')); $extras[] = $e; }

$cfg = array(
    'booking'  => $booking_url,
    'formulas' => $CA['formulas'],
    'extras'   => $extras,
    'vehicles' => $vehicles,
    'interior' => $interior,
    'exterior' => $exterior,
    'grid'     => $CA['grid'],
    'txt'      => $CA['txt'],
    'icons'    => array('check' => knIcon('check', array('size' => 16)), 'sparkles' => knIcon('sparkles', array('size' => 18)), 'clock' => knIcon('clock', array('size' => 15)), 'calendar' => knIcon('calendar', array('size' => 18)), 'home' => knIcon('home', array('size' => 16)), 'factory' => knIcon('factory', array('size' => 16))),
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
    <div class="kn-ca__resume" data-ca-resume hidden>
      <span class="kn-ca__avatar" aria-hidden="true"><?php echo htmlspecialchars(mb_substr($CA['txt']['bot'], 0, 1)); ?></span>
      <div class="kn-ca__resume-txt"><strong>Votre simulation est en cours</strong><span data-ca-resume-sum></span></div>
      <div class="kn-ca__resume-actions">
        <button type="button" class="kn-btn" data-ca-resume-open>Reprendre</button>
        <button type="button" class="kn-ca__link" data-ca-resume-restart>Recommencer</button>
      </div>
    </div>
    <div class="kn-ca__layout" data-ca-layout aria-labelledby="<?php echo $uid; ?>-title">
      <div class="kn-ca__chat">
        <div class="kn-ca__top">
          <div class="kn-ca__bar">
            <button type="button" class="kn-ca__iconbtn" data-ca-back hidden aria-label="Question précédente">←</button>
            <span class="kn-ca__avatar kn-ca__avatar--head" aria-hidden="true"><?php echo htmlspecialchars(mb_substr($CA['txt']['bot'], 0, 1)); ?></span>
            <div class="kn-ca__who">
              <strong id="<?php echo $uid; ?>-title"><?php echo htmlspecialchars($CA['txt']['bot']); ?></strong>
              <span class="kn-ca__status"><i aria-hidden="true"></i>En ligne · <span data-ca-step>Question 1 / 3</span></span>
            </div>
            <button type="button" class="kn-ca__navbtn kn-ca__restart" data-ca-restart hidden>Recommencer</button>
          </div>
          <div class="kn-ca__progress" role="progressbar" aria-valuemin="0" aria-valuemax="3" aria-valuenow="0" aria-label="Progression du simulateur"><span data-ca-bar></span></div>
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
      <button type="button" class="kn-ca__iconbtn kn-ca__close" data-ca-close aria-label="Fermer le simulateur">×</button>
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
  var NQ = 3;
  function fresh() { return { step: 0, vehicle: null, interior: null, exterior: null, rec: null, alt: null, formula: null, extras: [], place: 'domicile' }; }
  var S = fresh(), hist = [], resultPanel = null;

  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function eur(n) { return (Math.round(n * 100) / 100).toLocaleString('fr-BE', { minimumFractionDigits: n % 1 ? 2 : 0, maximumFractionDigits: 2 }) + ' €'; }
  function find(list, k) { for (var i = 0; i < list.length; i++) if (list[i].key === k) return list[i]; return null; }
  function track(ev, data) { window.dataLayer = window.dataLayer || []; var o = { event: ev }; for (var k in data) o[k] = data[k]; window.dataLayer.push(o); }
  function F() { return find(C.formulas, S.formula); }

  function price(f, place) {
    if (!f) return null;
    var v = S.vehicle || 'citadine', p = place || S.place, x = f.prices[v + '_' + p];
    if (x == null && p === 'atelier') x = f.prices[v + '_domicile'];
    return x;
  }
  function available() {
    var f = F();
    return C.extras.filter(function (e) {
      if (f && e.included_in.indexOf(f.key) >= 0) return false;
      if (e.needs_exterior && S.exterior === 'non') return false;
      return true;
    });
  }
  function forcedAtelier() { return S.extras.some(function (k) { var e = find(C.extras, k); return e && e.atelier; }); }
  function pruneExtras() { var ok = available().map(function (e) { return e.key; }); S.extras = S.extras.filter(function (k) { return ok.indexOf(k) >= 0; }); if (forcedAtelier()) S.place = 'atelier'; }
  function compute() {
    var f = F(); if (!f) return null;
    var base = price(f), total = base, lines = [[f.name, base == null ? 'Sur devis' : eur(base)]];
    S.extras.forEach(function (k) { var e = find(C.extras, k); if (!e) return; if (e.price == null) lines.push([e.label, 'Sur devis']); else { lines.push([e.label, '+ ' + eur(e.price)]); if (total != null) total += e.price; } });
    return { total: total, quote: base == null, lines: lines };
  }

  // ── Résumé et progression ──────────────────────────────────
  function summary() {
    var dl = root.querySelector('[data-ca-sumlist]'), rows = [];
    var v = find(C.vehicles, S.vehicle); if (v) rows.push(['Véhicule', v.label]);
    var i = find(C.interior, S.interior); if (i) rows.push(['Intérieur', i.label]);
    var x = find(C.exterior, S.exterior); if (x) rows.push(['Extérieur', x.label]);
    if (F()) rows.push(['Formule', F().name]);
    if (S.extras.length) rows.push(['Options', S.extras.map(function (k) { return (find(C.extras, k) || {}).label; }).join(', ')]);
    if (F()) rows.push(['Lieu', S.place === 'atelier' ? 'Atelier de Visé' : 'À domicile']);
    dl.innerHTML = rows.length ? rows.map(function (r) { return '<div><dt>' + esc(r[0]) + '</dt><dd>' + esc(r[1]) + '</dd></div>'; }).join('') : '<div class="kn-ca__sum-empty">Vos choix apparaîtront ici.</div>';
    var r = compute(), tot = root.querySelector('[data-ca-total]');
    if (r && r.quote) { tot.textContent = 'Sur devis'; tot.dataset.v = ''; } else countTo(tot, r ? r.total : null);
    root.querySelector('[data-ca-bar]').style.width = (Math.min(S.step, NQ) / NQ * 100) + '%';
    root.querySelector('.kn-ca__progress').setAttribute('aria-valuenow', Math.min(S.step, NQ));
    root.querySelector('[data-ca-step]').textContent = S.step >= NQ ? 'Votre recommandation' : 'Question ' + (S.step + 1) + ' / ' + NQ;
    root.querySelector('[data-ca-back]').hidden = hist.length === 0;
    root.querySelector('[data-ca-restart]').hidden = S.step === 0;
    root.classList.toggle('is-final', S.step >= NQ);
  }
  function countTo(el, to) {
    if (to == null) { el.textContent = '—'; el.dataset.v = ''; return; }
    var from = parseFloat(el.dataset.v || '0') || 0; el.dataset.v = to;
    if (reduce || from === to) { el.textContent = 'dès ' + eur(to); return; }
    var t0 = null, d = 550;
    function f(ts) { if (!t0) t0 = ts; var k = Math.min(1, (ts - t0) / d), e = 1 - Math.pow(1 - k, 3); el.textContent = 'dès ' + eur(Math.round(from + (to - from) * e)); if (k < 1) requestAnimationFrame(f); else el.textContent = 'dès ' + eur(to); }
    requestAnimationFrame(f);
  }

  // ── Conversation ───────────────────────────────────────────
  function typingDelay(html, extra) {
    if (reduce) return 0;
    var len = String(html).replace(/<[^>]+>/g, '').length;
    return Math.min(1600, 1100 + len * 4) + (extra || 0);
  }
  function botBubble(html, cb, extra) {
    var w = document.createElement('div'); w.className = 'kn-ca__msg kn-ca__msg--bot';
    w.innerHTML = '<span class="kn-ca__avatar" aria-hidden="true">' + esc((C.txt.bot || 'K').charAt(0)) + '</span><div class="kn-ca__bubble kn-ca__typing"><i></i><i></i><i></i></div>';
    log.appendChild(w); scrollIn(w);
    setTimeout(function () { var b = w.querySelector('.kn-ca__bubble'); b.classList.remove('kn-ca__typing'); b.innerHTML = html; if (cb) cb(w); }, typingDelay(html, extra));
  }
  function userBubble(html) { var w = document.createElement('div'); w.className = 'kn-ca__msg kn-ca__msg--user'; w.innerHTML = '<div class="kn-ca__bubble">' + html + '</div>'; log.appendChild(w); }
  function scrollIn(el) {
    var beh = reduce ? 'auto' : 'smooth';
    if (isOpen) {
      var top = el.offsetTop - 12, room = log.clientHeight;
      var target = el.offsetHeight > room - 24 ? top : el.offsetTop + el.offsetHeight - room + 24;
      if (target > log.scrollTop) log.scrollTo({ top: target, behavior: beh });
      return;
    }
    var r = el.getBoundingClientRect(); if (r.bottom > window.innerHeight - 40 || r.top < 60) el.scrollIntoView({ behavior: beh, block: 'nearest' });
  }
  function panel(html, cls) {
    var p = document.createElement('div'); p.className = 'kn-ca__panel' + (cls ? ' ' + cls : ''); p.innerHTML = html; log.appendChild(p);
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
    function () { botBubble(esc(C.txt.q1), function () { choices(C.vehicles, function (o) { open(); push(); S.vehicle = o.key; userBubble(esc(o.label)); track('simulateur_auto_etape', { etape: 1, valeur: o.key }); go(1); }, 2); }); },
    function () { botBubble(esc(C.txt.q2), function () { choices(C.interior, function (o) { push(); S.interior = o.key; userBubble(esc(o.label)); track('simulateur_auto_etape', { etape: 2, valeur: o.key }); go(2); }, 2); }); },
    function () { botBubble(esc(C.txt.q3), function () { choices(C.exterior, function (o) {
      push(); S.exterior = o.key; userBubble(esc(o.label)); track('simulateur_auto_etape', { etape: 3, valeur: o.key });
      var cell = (C.grid[S.interior] || {})[S.exterior] || {};
      S.rec = cell.rec || C.formulas[0].key; S.alt = cell.alt || null; S.formula = S.rec;
      S.extras = []; S.place = 'domicile';
      go(3);
    }); }); },
    function () { botBubble(C.icons.sparkles + ' ' + esc(C.txt.q4), function () { resultPanel = panel('', 'kn-ca__panel--result'); renderResult(true); track('simulateur_auto_resultat', resultData()); }, 500); }
  ];

  function why() {
    var i = find(C.interior, S.interior), x = find(C.exterior, S.exterior);
    return ((i && i.why) || '') + (x && x.why ? ' ' + x.why : '') + '.';
  }
  function kindLabel(altKey) {
    var a = find(C.formulas, altKey), r = find(C.formulas, S.rec);
    var pa = price(a), pr = price(r);
    if (pa != null && pr != null) return pa < pr ? 'Plus économique' : 'Pour aller plus loin';
    return C.formulas.indexOf(a) < C.formulas.indexOf(r) ? 'Plus économique' : 'Pour aller plus loin';
  }
  function resultData() { var r = compute() || {}; return { formule: S.formula, recommandee: S.rec, vehicule: S.vehicle, interieur: S.interior, exterieur: S.exterior, lieu: S.place, options: S.extras.join('|'), total: r.quote ? 'devis' : r.total }; }

  function renderResult(first) {
    var f = F(), r = compute(), isRec = S.formula === S.rec;
    var otherKey = isRec ? S.alt : S.rec, other = otherKey ? find(C.formulas, otherKey) : null;
    var pr = function (x) { var v = price(x); return v == null ? 'Sur devis' : '<small>dès</small> ' + eur(v); };
    var html = '<article class="kn-ca__reco is-rec' + (first ? '' : ' is-static') + '">' + (f.badge ? '<span class="kn-ca__badge">' + esc(f.badge) + '</span>' : '')
      + '<span class="kn-ca__reco-kicker">' + (isRec ? 'Recommandé pour vous' : 'Votre choix') + '</span>'
      + '<h3 class="kn-ca__reco-name">' + esc(f.name) + '</h3>'
      + '<p class="kn-ca__why">' + esc(isRec ? why() : f.tagline) + '</p>'
      + '<ul class="kn-ca__points">' + f.points.map(function (pt, k) { return '<li style="--d:' + (first ? k * 90 + 150 : 0) + 'ms">' + C.icons.check + '<span>' + esc(pt) + '</span></li>'; }).join('') + '</ul>'
      + (f.duration ? '<div class="kn-ca__reco-foot"><span class="kn-ca__dur">' + C.icons.clock + 'Durée : ' + esc(f.duration) + '</span></div>' : '') + '</article>';
    if (other) html += '<button type="button" class="kn-ca__alt" data-swap="' + esc(other.key) + '"><span class="kn-ca__alt-kind">' + (isRec ? kindLabel(other.key) : 'Revenir à la recommandation') + '</span>'
      + '<span class="kn-ca__alt-body"><strong>' + esc(other.name) + '</strong><small>' + esc(other.tagline) + '</small></span><span class="kn-ca__alt-price">' + pr(other) + '</span><span class="kn-ca__alt-go">Choisir →</span></button>';
    var pd = price(f, 'domicile'), pa = price(f, 'atelier');
    html += '<div class="kn-ca__place" role="radiogroup" aria-label="Lieu du nettoyage">'
      + '<button type="button" role="radio" aria-checked="' + (S.place === 'domicile') + '" class="kn-ca__place-btn' + (S.place === 'domicile' ? ' is-on' : '') + '" data-place="domicile">' + C.icons.home + '<span>À domicile<small>' + (pd == null ? 'sur devis' : 'dès ' + eur(pd)) + '</small></span></button>'
      + '<button type="button" role="radio" aria-checked="' + (S.place === 'atelier') + '" class="kn-ca__place-btn' + (S.place === 'atelier' ? ' is-on' : '') + '" data-place="atelier">' + C.icons.factory + '<span>Atelier de Visé<small>' + (pa == null ? 'sur devis' : 'dès ' + eur(pa)) + '</small></span></button></div>';
    html += '<div class="kn-ca__result kn-ca__result--compact' + (first ? '' : ' is-static') + '">'
      + '<div class="kn-ca__result-row"><div><span class="kn-ca__result-label">' + esc(f.name) + ' · ' + (S.place === 'atelier' ? 'atelier de Visé' : 'à domicile') + '</span>'
      + '<div class="kn-ca__result-price" data-rp>' + (r.quote ? 'Sur devis' : '') + '</div></div>'
      + '<span class="kn-ca__result-pay">Paiement après la prestation</span></div>'
      + '<a class="kn-btn kn-ca__cta" href="' + esc(f.cta_url || C.booking) + '" data-cta>' + C.icons.calendar + esc(C.txt.cta) + '</a>'
      + '<p class="kn-ca__note">' + esc(C.txt.note) + ' <button type="button" class="kn-ca__link kn-ca__again" data-again>Recommencer</button></p></div>';
    var prevPrice = resultPanel.querySelector('[data-rp]'), prevV = prevPrice ? prevPrice.dataset.v : '0';
    resultPanel.innerHTML = html;
    if (!r.quote) { var el = resultPanel.querySelector('[data-rp]'); el.dataset.v = prevV || '0'; countTo(el, r.total); }
    summary();

    var sw = resultPanel.querySelector('[data-swap]');
    if (sw) sw.addEventListener('click', function () {
      S.formula = sw.dataset.swap; renderResult(false);
      track('simulateur_auto_alternative', { formule: S.formula, alternative_choisie: S.formula === S.rec ? 'retour' : kindLabel(S.formula) });
      var c = resultPanel.querySelector('[data-swap]') || resultPanel.querySelector('.kn-ca__reco'); if (c && !reduce) c.focus && c.focus({ preventScroll: true });
    });
    resultPanel.querySelectorAll('[data-place]').forEach(function (b) { b.addEventListener('click', function () {
      S.place = b.dataset.place; renderResult(false);
      track('simulateur_auto_lieu', { lieu: S.place });
      var nb = resultPanel.querySelector('[data-place="' + S.place + '"]'); if (nb) nb.focus({ preventScroll: true });
    }); });
    resultPanel.querySelector('[data-cta]').addEventListener('click', function () { track('simulateur_auto_rdv', resultData()); });
    resultPanel.querySelector('[data-again]').addEventListener('click', restart);
  }

  function back() {
    var h = hist.pop(); if (!h) return;
    S = h.S; log.innerHTML = h.html; resultPanel = null;
    var bots = log.querySelectorAll('.kn-ca__msg--bot'); if (bots.length) bots[bots.length - 1].remove();
    summary(); STEPS[S.step]();
  }
  function restart() {
    hist = []; S = fresh(); resultPanel = null; log.innerHTML = ''; go(0);
    if (!isOpen) root.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }); else log.scrollTop = 0;
  }

  // ── Plein écran façon messagerie ──────────────────────────
  var layout = root.querySelector('[data-ca-layout]'), resume = root.querySelector('[data-ca-resume]');
  var isOpen = false, pushed = false, ignorePop = false, lastFocus = null;
  function open() {
    if (isOpen) return;
    isOpen = true; lastFocus = document.activeElement;
    root.classList.add('is-full'); root.classList.remove('is-paused'); resume.hidden = true;
    document.documentElement.classList.add('kn-ca-lock');
    layout.setAttribute('role', 'dialog'); layout.setAttribute('aria-modal', 'true');
    try { history.pushState({ knca: '<?php echo $uid; ?>' }, ''); pushed = true; } catch (e) { pushed = false; }
    setTimeout(function () { log.scrollTop = log.scrollHeight; }, 30);
    track('simulateur_auto_ouverture', { etape: S.step });
  }
  function close(fromPop) {
    if (!isOpen) return;
    isOpen = false;
    root.classList.remove('is-full'); document.documentElement.classList.remove('kn-ca-lock');
    layout.removeAttribute('role'); layout.removeAttribute('aria-modal');
    if (S.step > 0 || S.vehicle) {
      root.classList.add('is-paused'); resume.hidden = false;
      var bits = [];
      var v = find(C.vehicles, S.vehicle); if (v) bits.push(v.label);
      if (F()) bits.push(F().name);
      var r = F() ? compute() : null; if (r) bits.push(r.quote ? 'sur devis' : 'dès ' + eur(r.total));
      root.querySelector('[data-ca-resume-sum]').textContent = bits.join(' · ') || 'Reprenez là où vous vous êtes arrêté.';
      var rb = root.querySelector('[data-ca-resume-open]'); rb.focus({ preventScroll: true });
      if (resume.getBoundingClientRect().top < 0 || resume.getBoundingClientRect().bottom > window.innerHeight) resume.scrollIntoView({ block: 'center' });
    } else if (lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
    track('simulateur_auto_fermeture', { etape: S.step });
    if (!fromPop && pushed) { pushed = false; ignorePop = true; history.back(); }
  }
  window.addEventListener('popstate', function () {
    if (ignorePop) { ignorePop = false; return; }
    if (isOpen) { pushed = false; close(true); }
  });
  document.addEventListener('keydown', function (e) {
    if (!isOpen) return;
    if (e.key === 'Escape') { e.preventDefault(); close(false); return; }
    if (e.key === 'Tab') {
      var f = Array.prototype.filter.call(layout.querySelectorAll('button:not([disabled]), a[href], input, select, textarea, [tabindex]:not([tabindex="-1"])'), function (el) { return el.offsetParent !== null; });
      if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (!layout.contains(document.activeElement)) { e.preventDefault(); first.focus(); }
      else if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  root.querySelector('[data-ca-close]').addEventListener('click', function () { close(false); });
  root.querySelector('[data-ca-resume-open]').addEventListener('click', function () { open(); var b = layout.querySelector('.kn-ca__panel button, .kn-ca__panel a'); if (b) b.focus({ preventScroll: true }); });
  root.querySelector('[data-ca-resume-restart]').addEventListener('click', function () { root.classList.remove('is-paused'); resume.hidden = true; restart(); open(); });
  root.querySelector('[data-ca-back]').addEventListener('click', back);
  root.querySelector('[data-ca-restart]').addEventListener('click', restart);
  log.innerHTML = '';
  go(0);
})();
</script>
