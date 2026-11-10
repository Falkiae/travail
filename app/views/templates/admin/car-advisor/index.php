<?php
$cfg     = isset($config) && is_array($config) ? $config : array();
$sizes   = knCaSizes();
$places  = knCaPlaces();
$tags    = knCaTags();
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$fmt = function ($v) { return $v === null ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$formulaKeys = array_map(function ($f) { return array($f['key'], $f['name']); }, $cfg['formulas']);
$extraKeys   = array_map(function ($e) { return array($e['key'], $e['label']); }, $cfg['extras']);

$formulaCard = function ($i, $f) use ($h, $tags) {
    ob_start(); ?>
    <div class="card ca-item" data-i="<?= $h($i) ?>">
      <div class="ca-item__bar">
        <strong class="ca-item__title" data-title><?= $h($f['name'] !== '' ? $f['name'] : 'Nouvelle formule') ?></strong>
        <span class="ca-item__tools">
          <button type="button" class="btn btn-secondary btn-sm" data-up title="Monter">↑</button>
          <button type="button" class="btn btn-secondary btn-sm" data-down title="Descendre">↓</button>
          <button type="button" class="btn btn-danger btn-sm" data-remove title="Supprimer">×</button>
        </span>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Nom</label><input type="text" name="formulas[<?= $h($i) ?>][name]" value="<?= $h($f['name']) ?>" maxlength="80" required data-name></div>
        <div class="form-group"><label>Clé <small>(identifiant interne, sans espace)</small></label><input type="text" name="formulas[<?= $h($i) ?>][key]" value="<?= $h($f['key']) ?>" maxlength="40" pattern="[a-z0-9_]*"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Badge <small>(optionnel)</small></label><input type="text" name="formulas[<?= $h($i) ?>][badge]" value="<?= $h($f['badge']) ?>" maxlength="40" placeholder="LE PLUS DEMANDÉ"></div>
        <div class="form-group"><label>Durée indicative</label><input type="text" name="formulas[<?= $h($i) ?>][duration]" value="<?= $h($f['duration']) ?>" maxlength="40" placeholder="≈ 1 h 30"></div>
      </div>
      <div class="form-group"><label>Accroche</label><input type="text" name="formulas[<?= $h($i) ?>][tagline]" value="<?= $h($f['tagline']) ?>" maxlength="200"></div>
      <div class="form-group"><label>Ce qui est compris <small>(un point par ligne)</small></label><textarea name="formulas[<?= $h($i) ?>][points]" rows="5"><?= $h(implode("\n", $f['points'])) ?></textarea></div>
      <div class="form-group"><label>Ce que couvre la formule <small>(sert à la recommandation)</small></label>
        <div class="ca-checks"><?php foreach ($tags as $tk => $tl): ?>
          <label><input type="checkbox" name="formulas[<?= $h($i) ?>][covers][]" value="<?= $h($tk) ?>"<?= in_array($tk, $f['covers'], true) ? ' checked' : '' ?>> <?= $h($tl) ?></label>
        <?php endforeach; ?></div>
      </div>
      <div class="form-group"><label>Lien de réservation <small>(vide = lien de réservation des Réglages)</small></label><input type="text" name="formulas[<?= $h($i) ?>][cta_url]" value="<?= $h($f['cta_url']) ?>" maxlength="300" placeholder="https://…"></div>
    </div>
    <?php return ob_get_clean();
};
$priceRow = function ($i, $f) use ($h, $sizes, $places, $fmt) {
    ob_start(); ?>
    <tr data-i="<?= $h($i) ?>">
      <th data-title><?= $h($f['name'] !== '' ? $f['name'] : 'Nouvelle formule') ?></th>
      <?php foreach ($sizes as $sk => $sl): foreach ($places as $pk => $pl): $k = $sk . '_' . $pk; ?>
      <td><input type="text" inputmode="decimal" name="formulas[<?= $h($i) ?>][prices][<?= $h($k) ?>]" value="<?= $h($fmt(isset($f['prices'][$k]) ? $f['prices'][$k] : null)) ?>" placeholder="<?= $pk === 'atelier' ? '= dom.' : 'devis' ?>" aria-label="<?= $h($sl . ' ' . $pl) ?>"></td>
      <?php endforeach; endforeach; ?>
    </tr>
    <?php return ob_get_clean();
};
$extraCard = function ($i, $e) use ($h, $formulaKeys, $fmt) {
    ob_start(); ?>
    <div class="card ca-item" data-i="<?= $h($i) ?>">
      <div class="ca-item__bar">
        <strong class="ca-item__title" data-title><?= $h($e['label'] !== '' ? $e['label'] : 'Nouveau supplément') ?></strong>
        <span class="ca-item__tools">
          <button type="button" class="btn btn-secondary btn-sm" data-up>↑</button>
          <button type="button" class="btn btn-secondary btn-sm" data-down>↓</button>
          <button type="button" class="btn btn-danger btn-sm" data-remove>×</button>
        </span>
      </div>
      <input type="hidden" name="extras[<?= $h($i) ?>][key]" value="<?= $h($e['key']) ?>">
      <div class="form-row">
        <div class="form-group"><label>Libellé</label><input type="text" name="extras[<?= $h($i) ?>][label]" value="<?= $h($e['label']) ?>" maxlength="80" required data-name></div>
        <div class="form-group"><label>Prix € <small>(vide = « sur devis »)</small></label><input type="text" inputmode="decimal" name="extras[<?= $h($i) ?>][price]" value="<?= $h($fmt($e['price'])) ?>" placeholder="sur devis"></div>
      </div>
      <div class="form-group"><label>Description</label><input type="text" name="extras[<?= $h($i) ?>][desc]" value="<?= $h($e['desc']) ?>" maxlength="200"></div>
      <div class="form-row">
        <div class="form-group"><label>Icône</label><input type="text" list="kn-icon-list" name="extras[<?= $h($i) ?>][icon]" value="<?= $h($e['icon']) ?>" placeholder="sparkles"></div>
        <div class="form-group"><label>Lien « en savoir plus » <small>(optionnel)</small></label><input type="text" name="extras[<?= $h($i) ?>][url]" value="<?= $h($e['url']) ?>" maxlength="300"></div>
      </div>
      <div class="form-group ca-checks">
        <label><input type="checkbox" name="extras[<?= $h($i) ?>][atelier]" value="1"<?= $e['atelier'] ? ' checked' : '' ?>> Uniquement à l'atelier de Visé <small>(force le lieu)</small></label>
      </div>
      <div class="form-group"><label>Déjà inclus dans <small>(le supplément n'est alors pas proposé)</small></label>
        <div class="ca-checks"><?php foreach ($formulaKeys as $fk): ?>
          <label><input type="checkbox" name="extras[<?= $h($i) ?>][included_in][]" value="<?= $h($fk[0]) ?>"<?= in_array($fk[0], $e['included_in'], true) ? ' checked' : '' ?>> <?= $h($fk[1]) ?></label>
        <?php endforeach; ?></div>
      </div>
    </div>
    <?php return ob_get_clean();
};
$concernCard = function ($i, $c) use ($h, $tags, $extraKeys) {
    ob_start(); ?>
    <div class="card ca-item" data-i="<?= $h($i) ?>">
      <div class="ca-item__bar">
        <strong class="ca-item__title" data-title><?= $h($c['label'] !== '' ? $c['label'] : 'Nouveau souci') ?></strong>
        <span class="ca-item__tools">
          <button type="button" class="btn btn-secondary btn-sm" data-up>↑</button>
          <button type="button" class="btn btn-secondary btn-sm" data-down>↓</button>
          <button type="button" class="btn btn-danger btn-sm" data-remove>×</button>
        </span>
      </div>
      <input type="hidden" name="concerns[<?= $h($i) ?>][key]" value="<?= $h($c['key']) ?>">
      <div class="form-row">
        <div class="form-group"><label>Libellé</label><input type="text" name="concerns[<?= $h($i) ?>][label]" value="<?= $h($c['label']) ?>" maxlength="80" required data-name></div>
        <div class="form-group"><label>Icône</label><input type="text" list="kn-icon-list" name="concerns[<?= $h($i) ?>][icon]" value="<?= $h($c['icon']) ?>" placeholder="check"></div>
      </div>
      <div class="form-group"><label>Nécessite <small>(oriente vers une formule qui couvre ces besoins)</small></label>
        <div class="ca-checks"><?php foreach ($tags as $tk => $tl): ?>
          <label><input type="checkbox" name="concerns[<?= $h($i) ?>][needs][]" value="<?= $h($tk) ?>"<?= in_array($tk, $c['needs'], true) ? ' checked' : '' ?>> <?= $h($tl) ?></label>
        <?php endforeach; ?></div>
      </div>
      <div class="form-group"><label>Suppléments conseillés <small>(précochés pour le client)</small></label>
        <div class="ca-checks"><?php foreach ($extraKeys as $ek): ?>
          <label><input type="checkbox" name="concerns[<?= $h($i) ?>][extras][]" value="<?= $h($ek[0]) ?>"<?= in_array($ek[0], $c['extras'], true) ? ' checked' : '' ?>> <?= $h($ek[1]) ?></label>
        <?php endforeach; ?></div>
      </div>
    </div>
    <?php return ob_get_clean();
};
$emptyF = array('key' => '', 'name' => '', 'badge' => '', 'tagline' => '', 'points' => array(), 'duration' => '', 'cta_url' => '', 'covers' => array(), 'prices' => array());
$emptyE = array('key' => '', 'label' => '', 'desc' => '', 'icon' => 'sparkles', 'price' => null, 'atelier' => false, 'url' => '', 'included_in' => array());
$emptyC = array('key' => '', 'label' => '', 'icon' => 'check', 'needs' => array(), 'extras' => array());
$txtLabels = array(
    'bot' => "Nom de l'assistant", 'q1' => 'Question 1 — véhicule', 'q2' => 'Question 2 — état', 'q3' => 'Question 3 — soucis',
    'q4' => 'Question 4 — objectif', 'q5' => 'Message de recommandation', 'q6' => 'Question 6 — suppléments',
    'q7' => 'Question 7 — lieu', 'q8' => 'Message final', 'cta' => 'Texte du bouton de réservation', 'note' => 'Mention sous le prix',
);
?>
<div class="page-header">
  <h1>Simulateur auto</h1>
  <?php if (!empty($preview_url)): ?><a class="btn btn-secondary" href="<?= $h($preview_url) ?>" target="_blank" rel="noopener">Voir sur le site ↗</a><?php endif; ?>
</div>
<p style="color:var(--color-muted);margin:-.5rem 0 1rem">Ces réglages s'appliquent à tous les blocs « Simulateur auto » du site. Dans une page, le bloc ne gère que son titre, son intro et son fond.</p>
<?php if ($source === 'legacy'): ?>
<div class="flash-success">Les réglages d'un bloc existant ont été repris. Vérifiez-les puis cliquez sur « Enregistrer ».</div>
<?php elseif ($source === 'default'): ?>
<div class="flash-success">Valeurs par défaut chargées : complétez la grille de prix puis enregistrez.</div>
<?php endif; ?>

<form method="POST" action="/admin/car-advisor" id="ca-form">
  <input type="hidden" name="csrf_token" value="<?= $h($csrf_token) ?>">
  <input type="hidden" name="tab" value="formules" data-tabfield>

  <nav class="ca-tabs" role="tablist">
    <?php foreach (array('formules' => 'Formules', 'prix' => 'Grille de prix', 'supplements' => 'Suppléments', 'parcours' => 'États, soucis & recommandations', 'textes' => 'Textes') as $tk => $tl): ?>
    <button type="button" role="tab" class="ca-tab" data-tab="<?= $tk ?>"><?= $h($tl) ?></button>
    <?php endforeach; ?>
  </nav>

  <section class="ca-panel" data-panel="formules">
    <p class="ca-help">L'ordre des formules va de la plus simple à la plus complète : la recommandation s'en sert pour monter en gamme quand un souci l'exige.</p>
    <div data-list="formulas"><?php foreach ($cfg['formulas'] as $i => $f) echo $formulaCard((string) $i, $f); ?></div>
    <button type="button" class="btn btn-secondary" data-add="formulas">+ Ajouter une formule</button>
  </section>

  <section class="ca-panel" data-panel="prix" hidden>
    <p class="ca-help">Prix « à partir de » en euros. Case vide = « Sur devis ». Un prix atelier vide reprend le prix à domicile.</p>
    <div class="ca-table-wrap"><table class="ca-table">
      <thead>
        <tr><th rowspan="2">Formule</th><?php foreach ($sizes as $sl): ?><th colspan="2"><?= $h($sl) ?></th><?php endforeach; ?></tr>
        <tr><?php foreach ($sizes as $sl): foreach ($places as $pl): ?><th class="ca-sub"><?= $h($pl) ?></th><?php endforeach; endforeach; ?></tr>
      </thead>
      <tbody data-list="prices"><?php foreach ($cfg['formulas'] as $i => $f) echo $priceRow((string) $i, $f); ?></tbody>
    </table></div>
  </section>

  <section class="ca-panel" data-panel="supplements" hidden>
    <div data-list="extras"><?php foreach ($cfg['extras'] as $i => $e) echo $extraCard((string) $i, $e); ?></div>
    <button type="button" class="btn btn-secondary" data-add="extras">+ Ajouter un supplément</button>
  </section>

  <section class="ca-panel" data-panel="parcours" hidden>
    <div class="card">
      <div class="card-title">Étape « état du véhicule »</div>
      <?php foreach ($cfg['states'] as $i => $s): ?>
      <div class="form-row">
        <input type="hidden" name="states[<?= $i ?>][key]" value="<?= $h($s['key']) ?>">
        <div class="form-group"><label>Libellé</label><input type="text" name="states[<?= $i ?>][label]" value="<?= $h($s['label']) ?>" maxlength="60"></div>
        <div class="form-group"><label>Précision</label><input type="text" name="states[<?= $i ?>][sub]" value="<?= $h($s['sub']) ?>" maxlength="80"></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <div class="card-title">Étape « qu'est-ce qui vous gêne ? » (plusieurs choix)</div>
      <p class="ca-help">Un souci peut faire monter la recommandation (« nécessite ») et précocher des suppléments. Après l'ajout d'un supplément ou d'une formule, enregistrez pour pouvoir l'associer ici.</p>
      <div data-list="concerns"><?php foreach ($cfg['concerns'] as $i => $c) echo $concernCard((string) $i, $c); ?></div>
      <button type="button" class="btn btn-secondary" data-add="concerns">+ Ajouter un souci</button>
    </div>
    <div class="card">
      <div class="card-title">Étape « objectif »</div>
      <?php foreach ($cfg['goals'] as $i => $g): ?>
      <div class="form-row">
        <input type="hidden" name="goals[<?= $i ?>][key]" value="<?= $h($g['key']) ?>">
        <div class="form-group"><label>Libellé</label><input type="text" name="goals[<?= $i ?>][label]" value="<?= $h($g['label']) ?>" maxlength="60"></div>
        <div class="form-group"><label>Précision</label><input type="text" name="goals[<?= $i ?>][sub]" value="<?= $h($g['sub']) ?>" maxlength="80"></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <div class="card-title">Formule recommandée selon l'objectif et l'état</div>
      <p class="ca-help">Point de départ de la recommandation. Si le client coche des soucis que cette formule ne couvre pas, le simulateur propose la formule suivante qui les couvre, et garde celle-ci comme alternative « plus économique ».</p>
      <div class="ca-table-wrap"><table class="ca-table ca-matrix">
        <thead><tr><th>Objectif \ État</th><?php foreach ($cfg['states'] as $s): ?><th><?= $h($s['label']) ?></th><?php endforeach; ?></tr></thead>
        <tbody>
        <?php foreach ($cfg['goals'] as $g): ?>
          <tr><th><?= $h($g['label']) ?></th>
          <?php foreach ($cfg['states'] as $s): $cur = $cfg['matrix'][$g['key']][$s['key']]; ?>
            <td><select name="matrix[<?= $h($g['key']) ?>][<?= $h($s['key']) ?>]">
              <?php foreach ($formulaKeys as $fk): ?><option value="<?= $h($fk[0]) ?>"<?= $fk[0] === $cur ? ' selected' : '' ?>><?= $h($fk[1]) ?></option><?php endforeach; ?>
            </select></td>
          <?php endforeach; ?></tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </section>

  <section class="ca-panel" data-panel="textes" hidden>
    <div class="card">
      <?php foreach ($txtLabels as $k => $l): ?>
      <div class="form-group"><label><?= $h($l) ?></label>
        <?php if ($k === 'note'): ?><textarea name="txt[<?= $k ?>]" rows="2"><?= $h($cfg['txt'][$k]) ?></textarea>
        <?php else: ?><input type="text" name="txt[<?= $k ?>]" value="<?= $h($cfg['txt'][$k]) ?>" maxlength="300"><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="ca-savebar"><button type="submit" class="btn btn-primary">Enregistrer</button></div>
</form>

<template id="ca-tpl-formulas"><?= $formulaCard('__I__', $emptyF) ?></template>
<template id="ca-tpl-prices"><?= $priceRow('__I__', $emptyF) ?></template>
<template id="ca-tpl-extras"><?= $extraCard('__I__', $emptyE) ?></template>
<template id="ca-tpl-concerns"><?= $concernCard('__I__', $emptyC) ?></template>

<style>
.ca-tabs { display: flex; flex-wrap: wrap; gap: .35rem; margin-bottom: 1rem; border-bottom: 1px solid var(--color-border); }
.ca-tab { appearance: none; border: 0; background: none; font: inherit; font-weight: 600; padding: .6rem .9rem; border-bottom: 3px solid transparent; cursor: pointer; color: var(--color-muted); }
.ca-tab.is-active { color: inherit; border-bottom-color: currentColor; }
.ca-help { color: var(--color-muted); font-size: .875rem; margin: 0 0 1rem; }
.ca-item__bar { display: flex; justify-content: space-between; align-items: center; gap: .5rem; margin-bottom: .75rem; }
.ca-item__tools { display: inline-flex; gap: .25rem; }
.ca-checks { display: flex; flex-wrap: wrap; gap: .35rem 1.1rem; }
.ca-checks label { display: inline-flex; align-items: center; gap: .35rem; font-weight: 400; margin: 0; }
.ca-table-wrap { overflow-x: auto; }
.ca-table { border-collapse: collapse; width: 100%; min-width: 760px; font-size: .875rem; }
.ca-table th, .ca-table td { border: 1px solid var(--color-border); padding: .4rem .5rem; text-align: left; }
.ca-table thead th { background: rgba(0,0,0,.03); white-space: nowrap; }
.ca-table .ca-sub { font-weight: 500; font-size: .78rem; }
.ca-table td input { width: 5.5rem; }
.ca-matrix td select { width: 100%; min-width: 10rem; }
.ca-savebar { position: sticky; bottom: 0; padding: .9rem 0; background: var(--color-bg, #fff); border-top: 1px solid var(--color-border); margin-top: 1rem; z-index: 5; }
</style>
<script>
(function () {
  var form = document.getElementById('ca-form');
  var tabs = form.querySelectorAll('[data-tab]'), panels = form.querySelectorAll('[data-panel]'), tabField = form.querySelector('[data-tabfield]');
  function show(name) {
    var ok = false; tabs.forEach(function (t) { if (t.dataset.tab === name) ok = true; });
    if (!ok) name = 'formules';
    tabs.forEach(function (t) { var on = t.dataset.tab === name; t.classList.toggle('is-active', on); t.setAttribute('aria-selected', on ? 'true' : 'false'); });
    panels.forEach(function (p) { p.hidden = p.dataset.panel !== name; });
    tabField.value = name;
  }
  tabs.forEach(function (t) { t.addEventListener('click', function () { show(t.dataset.tab); history.replaceState(null, '', '#' + t.dataset.tab); }); });
  show((location.hash || '').slice(1));

  var seq = Date.now();
  function wire(item, list) {
    var name = item.querySelector('[data-name]');
    if (name) name.addEventListener('input', function () {
      var label = name.value || (list === 'formulas' ? 'Nouvelle formule' : 'Nouvel élément');
      item.querySelector('[data-title]').textContent = label;
      if (list === 'formulas') { var row = form.querySelector('[data-list="prices"] [data-i="' + item.dataset.i + '"] [data-title]'); if (row) row.textContent = label; }
    });
    var up = item.querySelector('[data-up]'), down = item.querySelector('[data-down]'), rm = item.querySelector('[data-remove]');
    if (up) up.addEventListener('click', function () { var p = item.previousElementSibling; if (p) item.parentNode.insertBefore(item, p); syncPrices(); });
    if (down) down.addEventListener('click', function () { var n = item.nextElementSibling; if (n) item.parentNode.insertBefore(n, item); syncPrices(); });
    if (rm) rm.addEventListener('click', function () {
      if (list === 'formulas' && form.querySelectorAll('[data-list="formulas"] .ca-item').length <= 1) { alert('Il faut au moins une formule.'); return; }
      if (!confirm('Supprimer cet élément ?')) return;
      if (list === 'formulas') { var row = form.querySelector('[data-list="prices"] [data-i="' + item.dataset.i + '"]'); if (row) row.remove(); }
      item.remove();
    });
  }
  // La grille de prix suit l'ordre des formules
  function syncPrices() {
    var body = form.querySelector('[data-list="prices"]');
    form.querySelectorAll('[data-list="formulas"] .ca-item').forEach(function (it) { var r = body.querySelector('[data-i="' + it.dataset.i + '"]'); if (r) body.appendChild(r); });
  }
  ['formulas', 'extras', 'concerns'].forEach(function (list) {
    form.querySelectorAll('[data-list="' + list + '"] .ca-item').forEach(function (it) { wire(it, list); });
    var add = form.querySelector('[data-add="' + list + '"]');
    add.addEventListener('click', function () {
      var i = 'n' + (seq++);
      var tpl = document.getElementById('ca-tpl-' + list).innerHTML.replace(/__I__/g, i);
      var box = form.querySelector('[data-list="' + list + '"]');
      box.insertAdjacentHTML('beforeend', tpl);
      var it = box.lastElementChild; wire(it, list);
      if (list === 'formulas') form.querySelector('[data-list="prices"]').insertAdjacentHTML('beforeend', document.getElementById('ca-tpl-prices').innerHTML.replace(/__I__/g, i));
      var first = it.querySelector('[data-name]'); if (first) first.focus();
    });
  });
})();
</script>
