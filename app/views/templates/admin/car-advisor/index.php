<?php
$cfg     = isset($config) && is_array($config) ? $config : array();
$sizes   = knCaSizes();
$places  = knCaPlaces();
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$fmt = function ($v) { return $v === null ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$formulaKeys = array_map(function ($f) { return array($f['key'], $f['name']); }, $cfg['formulas']);
$extraKeys   = array_map(function ($e) { return array($e['key'], $e['label']); }, $cfg['extras']);

$formulaCard = function ($i, $f) use ($h) {
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
        <label><input type="checkbox" name="extras[<?= $h($i) ?>][needs_exterior]" value="1"<?= !empty($e['needs_exterior']) ? ' checked' : '' ?>> Proposé seulement si l'extérieur est demandé</label>
      </div>
      <div class="form-group"><label>Déjà inclus dans <small>(le supplément n'est alors pas proposé)</small></label>
        <div class="ca-checks"><?php foreach ($formulaKeys as $fk): ?>
          <label><input type="checkbox" name="extras[<?= $h($i) ?>][included_in][]" value="<?= $h($fk[0]) ?>"<?= in_array($fk[0], $e['included_in'], true) ? ' checked' : '' ?>> <?= $h($fk[1]) ?></label>
        <?php endforeach; ?></div>
      </div>
    </div>
    <?php return ob_get_clean();
};
$emptyF = array('key' => '', 'name' => '', 'badge' => '', 'tagline' => '', 'points' => array(), 'duration' => '', 'cta_url' => '', 'prices' => array());
$emptyE = array('key' => '', 'label' => '', 'desc' => '', 'icon' => 'sparkles', 'price' => null, 'atelier' => false, 'needs_exterior' => false, 'url' => '', 'included_in' => array());
$txtLabels = array(
    'bot' => "Nom de l'assistant", 'q1' => 'Question 1 — véhicule', 'q2' => "Question 2 — état de l'intérieur", 'q3' => "Question 3 — extérieur",
    'q4' => 'Message avant la recommandation', 'cta' => 'Texte du bouton de réservation', 'note' => 'Mention sous le prix',
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
    <?php foreach (array('formules' => 'Formules', 'prix' => 'Grille de prix', 'supplements' => 'Suppléments', 'parcours' => 'Parcours & recommandations', 'textes' => 'Textes') as $tk => $tl): ?>
    <button type="button" role="tab" class="ca-tab" data-tab="<?= $tk ?>"><?= $h($tl) ?></button>
    <?php endforeach; ?>
  </nav>

  <section class="ca-panel" data-panel="formules">
    <p class="ca-help">L'ordre des formules est celui de l'affichage dans les listes. La recommandation se règle dans l'onglet « Parcours & recommandations ».</p>
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
      <div class="card-title">Question 2 — état de l'intérieur</div>
      <p class="ca-help">La phrase « pourquoi » est reprise dans la recommandation, suivie de celle du choix pour l'extérieur. Les options cochées sont présélectionnées pour le client.</p>
      <?php foreach ($cfg['interior'] as $i => $s): ?>
      <div class="ca-sub-card">
        <input type="hidden" name="interior[<?= $i ?>][key]" value="<?= $h($s['key']) ?>">
        <div class="form-row">
          <div class="form-group"><label>Réponse</label><input type="text" name="interior[<?= $i ?>][label]" value="<?= $h($s['label']) ?>" maxlength="60"></div>
          <div class="form-group"><label>Précision</label><input type="text" name="interior[<?= $i ?>][sub]" value="<?= $h($s['sub']) ?>" maxlength="80"></div>
        </div>
        <div class="form-group"><label>Phrase « pourquoi »</label><input type="text" name="interior[<?= $i ?>][why]" value="<?= $h($s['why']) ?>" maxlength="200"></div>
        <div class="form-group"><label>Options présélectionnées</label><div class="ca-checks">
          <input type="hidden" name="interior[<?= $i ?>][extras][]" value="">
          <?php foreach ($extraKeys as $ek): ?><label><input type="checkbox" name="interior[<?= $i ?>][extras][]" value="<?= $h($ek[0]) ?>"<?= in_array($ek[0], $s['extras'], true) ? ' checked' : '' ?>> <?= $h($ek[1]) ?></label><?php endforeach; ?>
        </div></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <div class="card-title">Question 3 — extérieur</div>
      <?php foreach ($cfg['exterior'] as $i => $s): ?>
      <div class="ca-sub-card">
        <input type="hidden" name="exterior[<?= $i ?>][key]" value="<?= $h($s['key']) ?>">
        <div class="form-row">
          <div class="form-group"><label>Réponse</label><input type="text" name="exterior[<?= $i ?>][label]" value="<?= $h($s['label']) ?>" maxlength="60"></div>
          <div class="form-group"><label>Précision</label><input type="text" name="exterior[<?= $i ?>][sub]" value="<?= $h($s['sub']) ?>" maxlength="80"></div>
        </div>
        <div class="form-group"><label>Suite de la phrase « pourquoi »</label><input type="text" name="exterior[<?= $i ?>][why]" value="<?= $h($s['why']) ?>" maxlength="200"></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <div class="card-title">Recommandation selon les réponses</div>
      <p class="ca-help">Pour chaque combinaison : la formule recommandée et l'alternative proposée juste en dessous (« Plus économique » ou « Pour aller plus loin », selon le prix).</p>
      <div class="ca-table-wrap"><table class="ca-table ca-matrix">
        <thead><tr><th>Intérieur \ Extérieur</th><?php foreach ($cfg['exterior'] as $x): ?><th><?= $h($x['label']) ?></th><?php endforeach; ?></tr></thead>
        <tbody>
        <?php foreach ($cfg['interior'] as $in): ?>
          <tr><th><?= $h($in['label']) ?></th>
          <?php foreach ($cfg['exterior'] as $x): $cell = $cfg['grid'][$in['key']][$x['key']]; ?>
            <td>
              <label class="ca-cell-lab">Recommandée</label>
              <select name="grid[<?= $h($in['key']) ?>][<?= $h($x['key']) ?>][rec]">
                <?php foreach ($formulaKeys as $fk): ?><option value="<?= $h($fk[0]) ?>"<?= $fk[0] === $cell['rec'] ? ' selected' : '' ?>><?= $h($fk[1]) ?></option><?php endforeach; ?>
              </select>
              <label class="ca-cell-lab">Alternative</label>
              <select name="grid[<?= $h($in['key']) ?>][<?= $h($x['key']) ?>][alt]">
                <option value=""<?= $cell['alt'] === '' ? ' selected' : '' ?>>— Aucune —</option>
                <?php foreach ($formulaKeys as $fk): ?><option value="<?= $h($fk[0]) ?>"<?= $fk[0] === $cell['alt'] ? ' selected' : '' ?>><?= $h($fk[1]) ?></option><?php endforeach; ?>
              </select>
            </td>
          <?php endforeach; ?></tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
      <p class="ca-help" style="margin-top:.75rem">Après l'ajout d'une formule ou d'une option, enregistrez pour pouvoir la choisir ici.</p>
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
.ca-matrix td select { width: 100%; min-width: 11rem; margin-bottom: .35rem; }
.ca-cell-lab { display: block; font-size: .7rem; font-weight: 600; opacity: .65; margin: 0 0 .15rem; }
.ca-sub-card { padding: .75rem 0; border-top: 1px solid var(--color-border); }
.ca-sub-card:first-of-type { border-top: 0; }
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
  ['formulas', 'extras'].forEach(function (list) {
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
