<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5/lib/codemirror.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5/theme/dracula.min.css">

<div class="page-header">
  <h1>Bloc : <code><?= htmlspecialchars($type) ?></code></h1>
  <a href="/admin/blocks" class="btn btn-secondary">← Retour</a>
</div>

<div class="tabs" style="display:flex;gap:.5rem;margin-bottom:1rem;">
  <button class="btn tab-btn active" data-tab="html" id="tab-btn-html">HTML / PHP</button>
  <button class="btn btn-secondary tab-btn" data-tab="css" id="tab-btn-css">CSS</button>
</div>

<form method="POST" action="/admin/blocks/<?= htmlspecialchars($type) ?>/edit" id="block-edit-form">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div id="tab-html" class="tab-pane">
    <div class="card">
      <p style="color:var(--color-muted);font-size:.85rem;margin-bottom:.75rem;">
        Variables disponibles : <code>$block</code>, <code>$booking_url</code>, <code>$reviews</code>. La syntaxe PHP est validée avant sauvegarde.
      </p>
      <textarea id="php-textarea" name="php_content"><?= htmlspecialchars($content) ?></textarea>
      <div id="php-cm" style="border:1px solid #e5e7eb;border-radius:6px;"></div>
    </div>
  </div>

  <div id="tab-css" class="tab-pane" style="display:none;">
    <div class="card">
      <p style="color:var(--color-muted);font-size:.85rem;margin-bottom:.75rem;">
        CSS injecté sur toutes les pages publiques pour ce bloc. Utilise les classes existantes ou ajoute les tiennes.
      </p>
      <textarea id="css-textarea" name="css"><?= htmlspecialchars($css) ?></textarea>
      <div id="css-cm" style="border:1px solid #e5e7eb;border-radius:6px;"></div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Enregistrer</button>
  </div>
</form>

<?php if ($hasBackup): ?>
<form method="POST" action="/admin/blocks/<?= htmlspecialchars($type) ?>/restore" class="confirm-delete" style="margin-top:.5rem;">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <button type="submit" class="btn btn-secondary btn-sm">↩ Restaurer la version précédente</button>
</form>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/codemirror@5/lib/codemirror.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5/mode/xml/xml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5/mode/javascript/javascript.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5/mode/css/css.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5/mode/clike/clike.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5/mode/php/php.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5/addon/edit/matchbrackets.min.js"></script>
<script>
(function() {
  var phpTA  = document.getElementById('php-textarea');
  var cssTA  = document.getElementById('css-textarea');
  var phpCM  = CodeMirror(document.getElementById('php-cm'), {
    value: phpTA.value,
    mode: 'application/x-httpd-php',
    theme: 'dracula',
    lineNumbers: true,
    matchBrackets: true,
    indentUnit: 4,
    tabSize: 4,
    lineWrapping: true,
    extraKeys: { 'Tab': function(cm) { cm.replaceSelection('    '); } },
  });
  var cssCM = CodeMirror(document.getElementById('css-cm'), {
    value: cssTA.value,
    mode: 'css',
    theme: 'dracula',
    lineNumbers: true,
    matchBrackets: true,
    indentUnit: 2,
    lineWrapping: true,
  });
  phpTA.style.display = 'none';
  cssTA.style.display = 'none';

  // Set editor heights
  phpCM.setSize(null, 520);
  cssCM.setSize(null, 400);

  // Tabs
  document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var tab = btn.dataset.tab;
      document.querySelectorAll('.tab-pane').forEach(function(p) { p.style.display = 'none'; });
      document.querySelectorAll('.tab-btn').forEach(function(b) {
        b.classList.remove('active');
        b.classList.add('btn-secondary');
      });
      document.getElementById('tab-' + tab).style.display = '';
      btn.classList.add('active');
      btn.classList.remove('btn-secondary');
      if (tab === 'html') phpCM.refresh();
      if (tab === 'css') cssCM.refresh();
    });
  });

  // Sync on submit
  document.getElementById('block-edit-form').addEventListener('submit', function() {
    phpTA.value = phpCM.getValue();
    cssTA.value = cssCM.getValue();
  });
})();
</script>
