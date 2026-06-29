<?php
// $menu, $items (array), $csrf_token passed from controller
?>
<div class="page-header">
  <h1><?= htmlspecialchars($title) ?></h1>
  <a href="/admin/menus" class="btn btn-secondary">&larr; Retour</a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
  <?php foreach ($_SESSION['flash'] as $ftype => $fmsg): ?>
  <div class="alert alert-<?= htmlspecialchars($ftype) ?>" style="margin-bottom:1rem;"><?= htmlspecialchars($fmsg) ?></div>
  <?php endforeach; ?>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<datalist id="kn-pages-list">
  <option value="/"></option>
  <?php foreach ($pages as $p): ?>
    <?php $url = $p['slug'] === 'home' ? '/' : '/' . $p['slug']; ?>
    <option value="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($p['title']) ?></option>
  <?php endforeach; ?>
</datalist>

<form method="post" action="/admin/menus/<?= (int)$menu['id'] ?>/edit" id="menu-form">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <textarea name="items_json" id="items_json" style="display:none;"></textarea>

  <div class="card">
    <div class="card-title" style="display:flex;align-items:center;justify-content:space-between;">
      <span>Items du menu</span>
      <small class="text-muted">Si des sous-items ont une colonne &rarr; mega menu. Sinon &rarr; dropdown.</small>
    </div>

    <div class="menu-builder" id="menu-builder"></div>

    <div style="margin-top:.75rem;">
      <button type="button" id="add-top-item" class="btn btn-secondary btn-sm">+ Ajouter un item</button>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="/admin/menus" class="btn btn-secondary">Annuler</a>
  </div>
</form>

<script>
(function () {
  var ITEMS = <?= json_encode($items, JSON_UNESCAPED_UNICODE) ?>;

  var builder = document.getElementById('menu-builder');
  var addTopBtn = document.getElementById('add-top-item');
  var form = document.getElementById('menu-form');
  var itemsTextarea = document.getElementById('items_json');

  // ── Drag state ──────────────────────────────────────────────
  var dragSrc = null;
  var dragParentIdx = null; // null = top-level, number = child of that index

  // ── Render ──────────────────────────────────────────────────
  function render() {
    builder.innerHTML = '';
    for (var i = 0; i < ITEMS.length; i++) {
      builder.appendChild(makeTopItem(i));
    }
  }

  function makeTopItem(idx) {
    var item = ITEMS[idx];
    var div = document.createElement('div');
    div.className = 'menu-top-item';
    div.setAttribute('draggable', 'true');
    div.dataset.idx = idx;

    // ── Header row ──
    var hdr = document.createElement('div');
    hdr.className = 'menu-item-header';

    var handle = document.createElement('span');
    handle.className = 'drag-handle';
    handle.innerHTML = '&#8943;';
    handle.title = 'Glisser pour réordonner';

    var labelIn = document.createElement('input');
    labelIn.type = 'text';
    labelIn.className = 'form-control';
    labelIn.placeholder = 'Label';
    labelIn.value = item.label || '';
    labelIn.addEventListener('input', function () { ITEMS[idx].label = labelIn.value; });

    var urlIn = document.createElement('input');
    urlIn.type = 'text';
    urlIn.className = 'form-control url-input';
    urlIn.placeholder = 'URL ou choisir une page';
    urlIn.setAttribute('list', 'kn-pages-list');
    urlIn.value = item.url || '';
    urlIn.addEventListener('input', function () { ITEMS[idx].url = urlIn.value; });

    var targetSel = document.createElement('select');
    targetSel.className = 'form-control';
    targetSel.style.width = '130px';
    addOption(targetSel, '_self', 'Même onglet', item.target === '_self' || !item.target);
    addOption(targetSel, '_blank', 'Nouvel onglet', item.target === '_blank');
    targetSel.addEventListener('change', function () { ITEMS[idx].target = targetSel.value; });

    var ctaLabel = document.createElement('label');
    ctaLabel.style.display = 'flex';
    ctaLabel.style.alignItems = 'center';
    ctaLabel.style.gap = '.25rem';
    ctaLabel.style.whiteSpace = 'nowrap';
    ctaLabel.style.cursor = 'pointer';
    var ctaCheck = document.createElement('input');
    ctaCheck.type = 'checkbox';
    ctaCheck.checked = !!item.cta;
    ctaCheck.addEventListener('change', function () { ITEMS[idx].cta = ctaCheck.checked; });
    ctaLabel.appendChild(ctaCheck);
    ctaLabel.appendChild(document.createTextNode(' CTA'));

    var expandBtn = document.createElement('button');
    expandBtn.type = 'button';
    expandBtn.className = 'menu-expand-btn';
    expandBtn.textContent = 'Sous-items';
    expandBtn.addEventListener('click', function () {
      childrenDiv.classList.toggle('open');
    });

    var delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'btn btn-danger btn-sm';
    delBtn.textContent = '×';
    delBtn.addEventListener('click', function () {
      ITEMS.splice(idx, 1);
      render();
    });

    hdr.appendChild(handle);
    hdr.appendChild(labelIn);
    hdr.appendChild(urlIn);
    hdr.appendChild(targetSel);
    hdr.appendChild(ctaLabel);
    hdr.appendChild(expandBtn);
    hdr.appendChild(delBtn);

    // ── Children ──
    var childrenDiv = document.createElement('div');
    childrenDiv.className = 'menu-children';

    var childList = document.createElement('div');
    childList.className = 'menu-child-list';

    var children = item.children || [];
    for (var j = 0; j < children.length; j++) {
      childList.appendChild(makeChildRow(idx, j));
    }

    var addChildBtn = document.createElement('button');
    addChildBtn.type = 'button';
    addChildBtn.className = 'btn btn-secondary btn-sm';
    addChildBtn.textContent = '+ Ajouter un sous-item';
    addChildBtn.addEventListener('click', (function (i) {
      return function () {
        if (!ITEMS[i].children) ITEMS[i].children = [];
        ITEMS[i].children.push({ label: '', url: '', target: '_self', col: null, badge: '' });
        render();
        // Re-open children panel
        var panels = builder.querySelectorAll('.menu-top-item');
        if (panels[i]) {
          var cd = panels[i].querySelector('.menu-children');
          if (cd) cd.classList.add('open');
        }
      };
    })(idx));

    childrenDiv.appendChild(childList);
    childrenDiv.appendChild(addChildBtn);

    div.appendChild(hdr);
    div.appendChild(childrenDiv);

    // ── Drag events (top-level) ──
    div.addEventListener('dragstart', function (e) {
      dragSrc = div;
      dragParentIdx = null;
      e.dataTransfer.effectAllowed = 'move';
    });
    div.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      div.classList.add('drag-over');
    });
    div.addEventListener('dragleave', function () {
      div.classList.remove('drag-over');
    });
    div.addEventListener('drop', function (e) {
      e.preventDefault();
      div.classList.remove('drag-over');
      if (dragSrc === div || dragParentIdx !== null) return;
      var srcIdx = parseInt(dragSrc.dataset.idx, 10);
      var dstIdx = parseInt(div.dataset.idx, 10);
      var moved = ITEMS.splice(srcIdx, 1)[0];
      ITEMS.splice(dstIdx, 0, moved);
      render();
    });

    return div;
  }

  function makeChildRow(parentIdx, childIdx) {
    var child = ITEMS[parentIdx].children[childIdx];
    var row = document.createElement('div');
    row.className = 'menu-child-row';
    row.setAttribute('draggable', 'true');
    row.dataset.parentIdx = parentIdx;
    row.dataset.childIdx = childIdx;

    var handle = document.createElement('span');
    handle.className = 'drag-handle';
    handle.innerHTML = '&#8943;';

    var labelIn = document.createElement('input');
    labelIn.type = 'text';
    labelIn.className = 'form-control';
    labelIn.placeholder = 'Label';
    labelIn.value = child.label || '';
    labelIn.addEventListener('input', function () { ITEMS[parentIdx].children[childIdx].label = labelIn.value; });

    var urlIn = document.createElement('input');
    urlIn.type = 'text';
    urlIn.className = 'form-control';
    urlIn.placeholder = 'URL ou choisir une page';
    urlIn.setAttribute('list', 'kn-pages-list');
    urlIn.value = child.url || '';
    urlIn.addEventListener('input', function () { ITEMS[parentIdx].children[childIdx].url = urlIn.value; });

    var targetSel = document.createElement('select');
    targetSel.className = 'form-control';
    targetSel.style.width = '110px';
    addOption(targetSel, '_self', 'Même onglet', child.target === '_self' || !child.target);
    addOption(targetSel, '_blank', 'Nouvel onglet', child.target === '_blank');
    targetSel.addEventListener('change', function () { ITEMS[parentIdx].children[childIdx].target = targetSel.value; });

    var colSel = document.createElement('select');
    colSel.className = 'form-control col-select';
    addOption(colSel, '', '—', !child.col);
    addOption(colSel, '1', '1', child.col == 1);
    addOption(colSel, '2', '2', child.col == 2);
    addOption(colSel, '3', '3', child.col == 3);
    addOption(colSel, '4', '4', child.col == 4);
    colSel.addEventListener('change', function () {
      ITEMS[parentIdx].children[childIdx].col = colSel.value ? parseInt(colSel.value, 10) : null;
    });

    var badgeIn = document.createElement('input');
    badgeIn.type = 'text';
    badgeIn.className = 'form-control badge-input';
    badgeIn.placeholder = 'Badge';
    badgeIn.value = child.badge || '';
    badgeIn.addEventListener('input', function () { ITEMS[parentIdx].children[childIdx].badge = badgeIn.value; });

    var delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'btn btn-danger btn-sm';
    delBtn.textContent = '×';
    delBtn.addEventListener('click', function () {
      ITEMS[parentIdx].children.splice(childIdx, 1);
      render();
      // Re-open children panel
      var panels = builder.querySelectorAll('.menu-top-item');
      if (panels[parentIdx]) {
        var cd = panels[parentIdx].querySelector('.menu-children');
        if (cd) cd.classList.add('open');
      }
    });

    row.appendChild(handle);
    row.appendChild(labelIn);
    row.appendChild(urlIn);
    row.appendChild(targetSel);
    row.appendChild(colSel);
    row.appendChild(badgeIn);
    row.appendChild(delBtn);

    // ── Drag events (children) ──
    row.addEventListener('dragstart', function (e) {
      dragSrc = row;
      dragParentIdx = parentIdx;
      e.dataTransfer.effectAllowed = 'move';
      e.stopPropagation();
    });
    row.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.stopPropagation();
      e.dataTransfer.dropEffect = 'move';
      row.style.borderColor = 'var(--blue, #2563eb)';
    });
    row.addEventListener('dragleave', function () {
      row.style.borderColor = '';
    });
    row.addEventListener('drop', function (e) {
      e.preventDefault();
      e.stopPropagation();
      row.style.borderColor = '';
      if (dragSrc === row) return;
      if (dragParentIdx !== parentIdx) return; // cross-parent drag not supported
      var srcChild = parseInt(dragSrc.dataset.childIdx, 10);
      var dstChild = parseInt(row.dataset.childIdx, 10);
      var moved = ITEMS[parentIdx].children.splice(srcChild, 1)[0];
      ITEMS[parentIdx].children.splice(dstChild, 0, moved);
      render();
      var panels = builder.querySelectorAll('.menu-top-item');
      if (panels[parentIdx]) {
        var cd = panels[parentIdx].querySelector('.menu-children');
        if (cd) cd.classList.add('open');
      }
    });

    return row;
  }

  function addOption(sel, value, text, selected) {
    var opt = document.createElement('option');
    opt.value = value;
    opt.textContent = text;
    if (selected) opt.selected = true;
    sel.appendChild(opt);
  }

  // ── Add top-level item ───────────────────────────────────────
  addTopBtn.addEventListener('click', function () {
    ITEMS.push({ label: '', url: '', target: '_self', cta: false, children: [] });
    render();
  });

  // ── Serialize on submit ──────────────────────────────────────
  form.addEventListener('submit', function () {
    itemsTextarea.value = JSON.stringify(ITEMS);
  });

  // ── Boot ────────────────────────────────────────────────────
  render();
}());
</script>
