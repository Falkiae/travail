/* ============================================================
   Keepnew Admin JS — Vanilla, no dependencies
   ============================================================ */

'use strict';

/* ── Slugify ── */
function slugify(str) {
  return str.toLowerCase()
    .normalize('NFD').replace(/[̀-ͯ]/g, '')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim().replace(/\s+/g, '-')
    .replace(/-+/g, '-');
}

/* ── Auto-slug from title ── */
(function initSlugAuto() {
  const titleInput = document.getElementById('title');
  const slugInput  = document.getElementById('slug');
  if (!titleInput || !slugInput) return;

  let slugManuallyEdited = slugInput.value.length > 0 && (slugInput.dataset.existing === '1');

  slugInput.addEventListener('input', () => {
    slugManuallyEdited = slugInput.value.length > 0;
  });

  titleInput.addEventListener('input', () => {
    if (!slugManuallyEdited) {
      slugInput.value = slugify(titleInput.value);
    }
  });

  // Unlock slug button
  const unlockBtn = document.getElementById('slug-unlock');
  if (unlockBtn) {
    unlockBtn.addEventListener('click', () => {
      slugInput.removeAttribute('readonly');
      slugInput.focus();
      slugManuallyEdited = true;
    });
  }
})();

/* ── Character counters ── */
(function initCharCounters() {
  document.querySelectorAll('[data-maxlength]').forEach(input => {
    const max = parseInt(input.dataset.maxlength, 10);
    const counter = document.createElement('div');
    counter.className = 'char-counter';
    input.parentNode.appendChild(counter);

    function update() {
      const len = input.value.length;
      counter.textContent = len + ' / ' + max;
      counter.className = 'char-counter';
      if (len > max) counter.classList.add('char-counter--danger');
      else if (len > max * 0.8) counter.classList.add('char-counter--warning');
    }

    input.addEventListener('input', update);
    update();
  });
})();

/* ============================================================
   Block Editor
   ============================================================ */

const BLOCK_TYPES = {
  heading:   'Titre',
  text:      'Texte',
  image:     'Image',
  cta:       'Bouton CTA',
  html:      'HTML libre',
  video:     'Vidéo',
  file:      'Fichier',
  accordion: 'Accordéon',
  quote:     'Citation',
  'hero':      'Hero',
  'services':  'Grille services',
  'two-col':   'Deux colonnes',
  'how':       'Comment ça marche',
  'reviews':   'Avis clients',
  'zone':      'Zone d\'intervention',
  'cta-final': 'CTA final',
};

let dragSrcEl = null;

function buildBlockForm(type, data) {
  data = data || {};
  const d = document.createElement('div');
  d.className = 'block-body';

  function fg(label, html) {
    return '<div class="form-group"><label>' + label + '</label>' + html + '</div>';
  }
  function inp(name, val, placeholder) {
    val = val || '';
    placeholder = placeholder || '';
    return '<input type="text" data-field="' + name + '" value="' + esc(val) + '" placeholder="' + esc(placeholder) + '">';
  }
  function ta(name, val, placeholder, rows) {
    val = val || '';
    placeholder = placeholder || '';
    rows = rows || 4;
    return '<textarea data-field="' + name + '" rows="' + rows + '" placeholder="' + esc(placeholder) + '">' + esc(val) + '</textarea>';
  }
  function sel(name, options, current) {
    current = current || '';
    let html = '<select data-field="' + name + '">';
    options.forEach(function(o) {
      html += '<option value="' + esc(o[0]) + '"' + (o[0] === current ? ' selected' : '') + '>' + esc(o[1]) + '</option>';
    });
    html += '</select>';
    return html;
  }
  function mediaBtn(targetField, labelText) {
    return '<div class="input-row">'
      + '<input type="text" data-field="' + targetField + '_display" value="" placeholder="Aucun fichier choisi" readonly>'
      + '<input type="hidden" data-field="' + targetField + '" value="">'
      + '<button type="button" class="btn btn-secondary btn-sm media-pick-btn" data-target="' + targetField + '">' + labelText + '</button>'
      + '</div>';
  }

  var BG_OPTIONS = [['white','⬜ Blanc (défaut)'],['alt','🔲 Gris clair'],['blue','🔵 Bleu Keepnew'],['night','⬛ Night Ink (sombre)'],['cream','🟡 Cream (éditorial)'],['rose','🌸 Rose (premium)']];
  function bgField(current) {
    return fg('Ambiance de fond', sel('bg', BG_OPTIONS, current || 'white'));
  }

  var LAYOUT_OPTIONS = [
    ['1-1', '½ + ½  (50/50)'],
    ['2-3', '⅖ + ⅗  (40/60)'],
    ['3-2', '⅗ + ⅖  (60/40)'],
    ['1-2', '⅓ + ⅔  (33/67)'],
    ['2-1', '⅔ + ⅓  (67/33)'],
    ['1-3', '¼ + ¾  (25/75)'],
    ['3-1', '¾ + ¼  (75/25)'],
    ['full','Pleine largeur (texte seul)'],
  ];

  function layoutField(current) {
    return fg('Disposition des colonnes', sel('layout', LAYOUT_OPTIONS, current || '1-1'));
  }

  function visibilityField(current) {
    var vis = current || ['desktop','tablet','mobile'];
    var checks = ['desktop','tablet','mobile'].map(function(v) {
      var labels = {desktop:'🖥 Desktop (>1024px)', tablet:'📱 Tablette (768–1024px)', mobile:'📱 Mobile (<768px)'};
      var checked = vis.indexOf(v) !== -1 ? ' checked' : '';
      return '<label style="display:flex;align-items:center;gap:.4rem;font-weight:normal;margin:.2rem 0;">' +
        '<input type="checkbox" data-vis="' + v + '"' + checked + '> ' + labels[v] + '</label>';
    }).join('');
    return '<div class="form-group"><label>Visibilité</label><div class="vis-checks">' + checks + '</div></div>';
  }

  function reverseFields(data) {
    return fg('Options mobile',
      '<label style="display:flex;align-items:center;gap:.4rem;font-weight:normal;margin:.2rem 0;">' +
      '<input type="checkbox" data-field="reverse" value="1"' + (data.reverse ? ' checked' : '') + '> Inverser l\'ordre des colonnes</label>' +
      '<label style="display:flex;align-items:center;gap:.4rem;font-weight:normal;margin:.2rem 0;">' +
      '<input type="checkbox" data-field="mobile_reverse" value="1"' + (data.mobile_reverse ? ' checked' : '') + '> Inverser sur mobile</label>' +
      '<label style="display:flex;align-items:center;gap:.4rem;font-weight:normal;margin:.2rem 0;">' +
      '<input type="checkbox" data-field="mobile_hide_visual" value="1"' + (data.mobile_hide_visual ? ' checked' : '') + '> Masquer l\'image sur mobile</label>'
    );
  }

  switch (type) {
    case 'heading':
      d.innerHTML =
        fg('Niveau', sel('level', [['h2','H2'],['h3','H3'],['h4','H4']], data.level)) +
        fg('Texte', inp('text', data.text, 'Titre…')) +
        fg('Style', sel('style', [['plain','Plain'],['tape','Tape (jaune)'],['highlight','Highlight (bleu)'],['marker','Marker']], data.style)) +
        bgField(data.bg) +
        visibilityField(data.visible);
      break;

    case 'text':
      d.innerHTML =
        fg('Contenu HTML', ta('text', data.text, '<p>Votre texte…</p>', 6)) +
        bgField(data.bg) +
        visibilityField(data.visible);
      break;

    case 'image':
      d.innerHTML =
        fg('Image', mediaBtn('media_id', 'Médiathèque')) +
        fg('Alt', inp('alt', data.alt, 'Description de l\'image')) +
        fg('Légende', inp('caption', data.caption, 'Légende optionnelle')) +
        bgField(data.bg) +
        visibilityField(data.visible);
      break;

    case 'cta':
      d.innerHTML =
        fg('Label', inp('label', data.label, 'Cliquez ici')) +
        fg('URL', inp('url', data.url, 'https://…')) +
        fg('Ouverture', sel('target', [['_self','Même onglet'],['_blank','Nouvel onglet']], data.target)) +
        fg('Style', sel('style', [['primary','Primaire'],['outline','Contour']], data.style)) +
        bgField(data.bg) +
        visibilityField(data.visible);
      break;

    case 'html':
      d.innerHTML =
        fg('HTML libre', ta('html', data.html, '<div>…</div>', 8)) +
        visibilityField(data.visible);
      break;

    case 'video':
      d.innerHTML =
        fg('URL (YouTube/Vimeo)', inp('url', data.url, 'https://www.youtube.com/watch?v=…')) +
        fg('Légende', inp('caption', data.caption, 'Légende optionnelle')) +
        visibilityField(data.visible);
      break;

    case 'file':
      d.innerHTML =
        fg('Fichier', mediaBtn('media_id', 'Médiathèque')) +
        fg('Label du lien', inp('label', data.label, 'Télécharger le document')) +
        visibilityField(data.visible);
      break;

    case 'accordion':
      d.innerHTML = bgField(data.bg) +
        visibilityField(data.visible) +
        '<div class="accordion-items" data-field="items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm add-accordion-item-btn" style="margin-top:.5rem">+ Ajouter un item</button>';
      // Add existing items
      (data.items || []).forEach(function(item) {
        addAccordionItem(d.querySelector('.accordion-items'), item);
      });
      d.querySelector('.add-accordion-item-btn').addEventListener('click', function() {
        addAccordionItem(d.querySelector('.accordion-items'), {});
      });
      break;

    case 'quote':
      d.innerHTML =
        fg('Citation', ta('text', data.text, 'Texte de la citation…', 3)) +
        fg('Auteur', inp('author', data.author, 'Prénom Nom')) +
        fg('Fonction / Titre', inp('role', data.role, 'CEO, Entreprise')) +
        bgField(data.bg || 'rose') +
        visibilityField(data.visible);
      break;

    case 'hero':
      d.innerHTML =
        fg('Ambiance de fond', sel('bg', [['cream','🟡 Cream (défaut)'],['white','⬜ Blanc'],['alt','🔲 Gris clair'],['blue','🔵 Bleu Keepnew'],['night','⬛ Night Ink (sombre)'],['rose','🌸 Rose (premium)']], data.bg || 'cream')) +
        layoutField(data.layout || '1-1') +
        reverseFields(data) +
        visibilityField(data.visible) +
        fg('Eyebrow', inp('eyebrow', data.eyebrow, 'CANAPÉ · VOITURE · MATELAS — À DOMICILE')) +
        fg('H1', inp('h1', data.h1, 'Nettoyage à domicile de canapé, matelas et voitures.')) +
        fg('Corps de texte (HTML autorisé)', ta('body', data.body, 'Keepnew nettoie vos <strong>canapés</strong>...')) +
        fg('CTA — Eyebrow', inp('cta_eyebrow', data.cta_eyebrow, 'RÉSERVATION EN LIGNE')) +
        fg('CTA — Titre', inp('cta_title', data.cta_title, 'Prendre RDV en 2 min')) +
        fg('Preuve sociale', inp('social_proof', data.social_proof, '4,9/5 · +110 avis · +400 canapés · +250 voitures'));
      break;

    case 'services':
      d.innerHTML =
        fg('Eyebrow', inp('eyebrow', data.eyebrow, 'NOS SERVICES')) +
        fg('H2', inp('h2', data.h2, 'Nettoyage de canapés, matelas & voitures à domicile')) +
        bgField(data.bg) +
        visibilityField(data.visible) +
        '<div class="form-group"><label>Services</label><div class="block-repeater" data-repeater="items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="items">+ Ajouter un service</button></div>';
      (data.items || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['name','desc','icon'], ['Nom','Description','Icône (emoji)'], item);
      });
      d.querySelector('[data-repeater-add="items"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['name','desc','icon'], ['Nom','Description','Icône (emoji)'], {});
      });
      break;

    case 'two-col':
      d.innerHTML =
        bgField(data.bg) +
        layoutField(data.layout || '1-1') +
        reverseFields(data) +
        visibilityField(data.visible) +
        fg('Eyebrow', inp('eyebrow', data.eyebrow, 'DEUX FAÇONS DE TRAVAILLER')) +
        fg('H2', inp('h2', data.h2, 'On vient chez vous — ou vous venez chez nous.')) +
        fg('Titre gauche', inp('left_title', data.left_title, 'À domicile')) +
        fg('Corps gauche', ta('left_body', data.left_body, 'Description…', 3)) +
        fg('Titre droite', inp('right_title', data.right_title, 'Atelier à Visé')) +
        fg('Corps droite', ta('right_body', data.right_body, 'Description…', 3));
      break;

    case 'how':
      d.innerHTML =
        fg('Eyebrow', inp('eyebrow', data.eyebrow, 'RÉSERVATION EN LIGNE')) +
        fg('H2', inp('h2', data.h2, 'Réservez votre nettoyage à domicile en 2 minutes')) +
        bgField(data.bg || 'alt') +
        visibilityField(data.visible) +
        '<div class="form-group"><label>Étapes</label><div class="block-repeater" data-repeater="steps"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="steps">+ Ajouter une étape</button></div>';
      (data.steps || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="steps"]'), 'steps', ['title','body'], ['Titre','Description'], item);
      });
      d.querySelector('[data-repeater-add="steps"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="steps"]'), 'steps', ['title','body'], ['Titre','Description'], {});
      });
      break;

    case 'reviews':
      d.innerHTML =
        fg('Eyebrow', inp('eyebrow', data.eyebrow, 'ILS NOUS FONT CONFIANCE')) +
        fg('H2', inp('h2', data.h2, '4,9/5 · +110 avis · +400 canapés nettoyés')) +
        bgField(data.bg) +
        visibilityField(data.visible) +
        '<p style="color:var(--color-muted);font-size:.85em;margin:.5rem 0">Les avis sont chargés automatiquement depuis Google.</p>';
      break;

    case 'zone':
      d.innerHTML =
        fg('Eyebrow', inp('eyebrow', data.eyebrow, 'OÙ ON INTERVIENT')) +
        fg('H2', inp('h2', data.h2, 'Nettoyage à domicile à Liège, Namur, Bruxelles et Luxembourg')) +
        fg('Corps', ta('body', data.body, 'Description de la zone…', 3)) +
        bgField(data.bg || 'night') +
        visibilityField(data.visible) +
        '<div class="form-group"><label>Zones (pills)</label><div class="block-repeater" data-repeater="pills"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="pills">+ Ajouter une zone</button></div>';
      (data.pills || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="pills"]'), 'pills', ['label'], ['Libellé'], item);
      });
      d.querySelector('[data-repeater-add="pills"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="pills"]'), 'pills', ['label'], ['Libellé'], {});
      });
      break;

    case 'cta-final':
      d.innerHTML =
        fg('Eyebrow', inp('eyebrow', data.eyebrow, '📅 RÉSERVATION EN LIGNE')) +
        fg('Titre', inp('title', data.title, 'Prendre RDV en 2 min')) +
        fg('Téléphone', inp('phone', data.phone, '+32 (0)4 55 13 84 19')) +
        visibilityField(data.visible);
      // cta-final is always blue — no bg selector
      break;
  }

  // Init media pick buttons
  d.querySelectorAll('.media-pick-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      openMediaModal(function(media) {
        var target = btn.dataset.target;
        var hiddenField = d.querySelector('[data-field="' + target + '"]');
        var displayField = d.querySelector('[data-field="' + target + '_display"]');
        if (hiddenField) hiddenField.value = media.id;
        if (displayField) displayField.value = media.original_name || media.filename;
      });
    });
  });

  // Restore media display values
  if (data.media_id) {
    var hf = d.querySelector('[data-field="media_id"]');
    if (hf) hf.value = data.media_id;
  }

  return d;
}

function addRepeaterRow(container, repeaterName, fields, labels, data) {
  data = data || {};
  var row = document.createElement('div');
  row.className = 'block-repeater-row';
  var html = '';
  for (var i = 0; i < fields.length; i++) {
    var f = fields[i];
    var l = labels[i] || f;
    html += '<input type="text" data-rfield="' + esc(f) + '" placeholder="' + esc(l) + '" value="' + esc(data[f] || '') + '">';
  }
  html += '<button type="button" class="btn btn-danger btn-sm remove-repeater-row" title="Supprimer">×</button>';
  row.innerHTML = html;
  row.querySelector('.remove-repeater-row').addEventListener('click', function() {
    row.remove();
  });
  container.appendChild(row);
}

function addAccordionItem(container, data) {
  data = data || {};
  var item = document.createElement('div');
  item.className = 'accordion-item';
  item.innerHTML =
    '<div class="form-group"><label>Question</label><input type="text" data-subfield="question" value="' + esc(data.question || '') + '" placeholder="Question…"></div>' +
    '<div class="form-group"><label>Réponse</label><textarea data-subfield="answer" rows="2" placeholder="Réponse…">' + esc(data.answer || '') + '</textarea></div>' +
    '<button type="button" class="remove-accordion-item" title="Supprimer">×</button>';
  item.querySelector('.remove-accordion-item').addEventListener('click', function() {
    item.remove();
  });
  container.appendChild(item);
}

function serializeBlock(blockItem) {
  var type = blockItem.dataset.blockType;
  var body = blockItem.querySelector('.block-body');
  var data = { type: type };

  if (type === 'accordion') {
    data.items = [];
    body.querySelectorAll('.accordion-item').forEach(function(item) {
      data.items.push({
        q: item.querySelector('[data-subfield="q"]') ? item.querySelector('[data-subfield="q"]').value : (item.querySelector('[data-subfield="question"]') ? item.querySelector('[data-subfield="question"]').value : ''),
        a: item.querySelector('[data-subfield="a"]') ? item.querySelector('[data-subfield="a"]').value : (item.querySelector('[data-subfield="answer"]') ? item.querySelector('[data-subfield="answer"]').value : ''),
      });
    });
    // also collect bg + visible for accordion
    body.querySelectorAll('[data-field]').forEach(function(el) {
      if (el.type === 'checkbox') { if (el.checked) data[el.dataset.field] = true; return; }
      data[el.dataset.field] = el.value;
    });
    var vis = [];
    body.querySelectorAll('.vis-checks input[data-vis]').forEach(function(cb) { if (cb.checked) vis.push(cb.dataset.vis); });
    if (vis.length) data.visible = vis;
    return data;
  }

  // Types with repeater sub-items
  var repeaterTypes = {
    'services': 'items',
    'how':      'steps',
    'zone':     'pills',
  };

  if (repeaterTypes[type]) {
    var repeaterKey = repeaterTypes[type];
    // Collect regular fields first
    body.querySelectorAll('[data-field]').forEach(function(el) {
      if (el.dataset.field.endsWith('_display')) return;
      if (el.type === 'checkbox') { data[el.dataset.field] = el.checked; return; }
      data[el.dataset.field] = el.value;
    });
    // Collect repeater rows
    var rows = [];
    body.querySelectorAll('[data-repeater="' + repeaterKey + '"] .block-repeater-row').forEach(function(row) {
      var obj = {};
      row.querySelectorAll('[data-rfield]').forEach(function(inp) {
        obj[inp.dataset.rfield] = inp.value;
      });
      rows.push(obj);
    });
    data[repeaterKey] = rows;
    var vis2 = [];
    body.querySelectorAll('.vis-checks input[data-vis]').forEach(function(cb) { if (cb.checked) vis2.push(cb.dataset.vis); });
    if (vis2.length) data.visible = vis2;
    return data;
  }

  // Default: collect all data-field elements
  body.querySelectorAll('[data-field]').forEach(function(el) {
    if (el.dataset.field.endsWith('_display')) return;
    if (el.type === 'checkbox') { data[el.dataset.field] = el.checked; return; }
    data[el.dataset.field] = el.value;
  });

  // Visibility checkboxes
  var vis3 = [];
  body.querySelectorAll('.vis-checks input[data-vis]').forEach(function(cb) { if (cb.checked) vis3.push(cb.dataset.vis); });
  if (vis3.length) data.visible = vis3;

  return data;
}

function createBlockItem(type, data) {
  var item = document.createElement('div');
  item.className = 'block-item';
  item.dataset.blockType = type;
  item.draggable = true;

  var header = document.createElement('div');
  header.className = 'block-header';
  header.innerHTML =
    '<span class="block-handle" title="Déplacer">⠿</span>' +
    '<span class="block-type-label">' + (BLOCK_TYPES[type] || type) + '</span>' +
    '<div class="block-controls">' +
      '<button type="button" class="btn btn-danger btn-sm remove-block-btn" title="Supprimer">×</button>' +
    '</div>';

  var form = buildBlockForm(type, data);

  item.appendChild(header);
  item.appendChild(form);

  header.querySelector('.remove-block-btn').addEventListener('click', function() {
    item.remove();
  });

  // Drag & drop
  item.addEventListener('dragstart', function(e) {
    dragSrcEl = item;
    item.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
  });
  item.addEventListener('dragend', function() {
    item.classList.remove('dragging');
    document.querySelectorAll('.block-item').forEach(function(el) {
      el.classList.remove('drag-over');
    });
  });
  item.addEventListener('dragover', function(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    if (dragSrcEl && dragSrcEl !== item) {
      item.classList.add('drag-over');
    }
  });
  item.addEventListener('dragleave', function() {
    item.classList.remove('drag-over');
  });
  item.addEventListener('drop', function(e) {
    e.preventDefault();
    item.classList.remove('drag-over');
    if (dragSrcEl && dragSrcEl !== item) {
      var list = item.parentNode;
      var items = Array.from(list.children);
      var srcIdx = items.indexOf(dragSrcEl);
      var dstIdx = items.indexOf(item);
      if (srcIdx < dstIdx) {
        list.insertBefore(dragSrcEl, item.nextSibling);
      } else {
        list.insertBefore(dragSrcEl, item);
      }
    }
  });

  return item;
}

(function initBlockEditor() {
  var editor = document.getElementById('block-editor');
  if (!editor) return;

  var toolbar = document.getElementById('block-editor-toolbar');
  var blockList = document.getElementById('block-list');
  var addBtn = document.getElementById('add-block-btn');
  var addMenu = document.getElementById('block-add-menu');
  var parentForm = editor.closest('form');

  if (!blockList || !addBtn || !addMenu) return;

  // Toggle dropdown
  addBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    addMenu.classList.toggle('open');
  });

  document.addEventListener('click', function() {
    addMenu.classList.remove('open');
  });

  // Type buttons in dropdown
  addMenu.querySelectorAll('[data-type]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var type = btn.dataset.type;
      var item = createBlockItem(type, {});
      blockList.appendChild(item);
      addMenu.classList.remove('open');
    });
  });

  // Load existing blocks
  if (window.existingBlocks && Array.isArray(window.existingBlocks)) {
    window.existingBlocks.forEach(function(blockData) {
      var item = createBlockItem(blockData.type, blockData);
      blockList.appendChild(item);
    });
  }

  // Serialize on form submit
  if (parentForm) {
    parentForm.addEventListener('submit', function() {
      var blocks = [];
      blockList.querySelectorAll('.block-item').forEach(function(item) {
        blocks.push(serializeBlock(item));
      });
      var hidden = parentForm.querySelector('input[name="content"]');
      if (!hidden) {
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'content';
        parentForm.appendChild(hidden);
      }
      hidden.value = JSON.stringify(blocks);
    });
  }
})();

/* ============================================================
   Media Modal
   ============================================================ */

var mediaModalCallback = null;

function openMediaModal(callback) {
  mediaModalCallback = callback;
  var overlay = document.getElementById('media-modal');
  if (!overlay) return;
  overlay.classList.remove('hidden');

  var grid = overlay.querySelector('#media-modal-grid');
  grid.innerHTML = '<p>Chargement…</p>';

  fetch('/admin/media/json')
    .then(function(r) { return r.json(); })
    .then(function(media) {
      grid.innerHTML = '';
      if (!media.length) {
        grid.innerHTML = '<p style="color:var(--color-muted)">Aucun fichier dans la médiathèque.</p>';
        return;
      }
      media.forEach(function(m) {
        var card = document.createElement('div');
        card.className = 'media-card';
        var isImage = m.mime_type && m.mime_type.startsWith('image/');
        card.innerHTML = isImage
          ? '<img src="' + esc(m.path) + '" alt="' + esc(m.alt || '') + '" loading="lazy">'
          : '<div class="media-icon">📄</div>';
        card.innerHTML += '<div class="media-info"><div class="media-name">' + esc(m.original_name) + '</div></div>';
        card.addEventListener('click', function() {
          closeMediaModal();
          if (mediaModalCallback) mediaModalCallback(m);
        });
        grid.appendChild(card);
      });
    })
    .catch(function() {
      grid.innerHTML = '<p style="color:var(--color-danger)">Erreur lors du chargement.</p>';
    });
}

function closeMediaModal() {
  var overlay = document.getElementById('media-modal');
  if (overlay) overlay.classList.add('hidden');
  mediaModalCallback = null;
}

(function initMediaModal() {
  var overlay = document.getElementById('media-modal');
  if (!overlay) return;

  var closeBtn = overlay.querySelector('.modal-close');
  if (closeBtn) closeBtn.addEventListener('click', closeMediaModal);

  overlay.addEventListener('click', function(e) {
    if (e.target === overlay) closeMediaModal();
  });
})();

/* ── Menu drag & drop ── */
(function initMenuDrag() {
  var list = document.getElementById('menu-items-list');
  if (!list) return;

  var menuDragSrc = null;

  function bindDrag(row) {
    row.draggable = true;
    row.addEventListener('dragstart', function(e) {
      menuDragSrc = row;
      row.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
    });
    row.addEventListener('dragend', function() {
      row.classList.remove('dragging');
      list.querySelectorAll('.menu-item-row').forEach(function(r) { r.classList.remove('drag-over'); });
    });
    row.addEventListener('dragover', function(e) {
      e.preventDefault();
      if (menuDragSrc !== row) row.classList.add('drag-over');
    });
    row.addEventListener('dragleave', function() { row.classList.remove('drag-over'); });
    row.addEventListener('drop', function(e) {
      e.preventDefault();
      row.classList.remove('drag-over');
      if (menuDragSrc && menuDragSrc !== row) {
        var rows = Array.from(list.children);
        var si = rows.indexOf(menuDragSrc);
        var di = rows.indexOf(row);
        if (si < di) list.insertBefore(menuDragSrc, row.nextSibling);
        else list.insertBefore(menuDragSrc, row);
        updateMenuOrder();
      }
    });
  }

  function updateMenuOrder() {
    list.querySelectorAll('.menu-item-row').forEach(function(row, i) {
      var orderInput = row.querySelector('[data-sort-order]');
      if (orderInput) orderInput.value = i;
    });
  }

  list.querySelectorAll('.menu-item-row').forEach(bindDrag);

  // Add item button
  var addItemBtn = document.getElementById('add-menu-item-btn');
  if (addItemBtn) {
    addItemBtn.addEventListener('click', function() {
      var row = buildMenuItemRow({ label: '', url: '', target: '_self' });
      list.appendChild(row);
      bindDrag(row);
      updateMenuOrder();
    });
  }

  function buildMenuItemRow(data) {
    var row = document.createElement('li');
    row.className = 'menu-item-row';
    var order = list.children.length;
    row.innerHTML =
      '<span class="block-handle" title="Déplacer">⠿</span>' +
      '<input type="hidden" data-sort-order name="items[' + order + '][sort_order]" value="' + order + '">' +
      '<input type="text" name="items[' + order + '][label]" value="' + esc(data.label || '') + '" placeholder="Label" style="flex:1">' +
      '<input type="text" name="items[' + order + '][url]" value="' + esc(data.url || '') + '" placeholder="URL" style="flex:2">' +
      '<select name="items[' + order + '][target]" style="width:130px">' +
        '<option value="_self"' + (data.target === '_self' ? ' selected' : '') + '>Même onglet</option>' +
        '<option value="_blank"' + (data.target === '_blank' ? ' selected' : '') + '>Nouvel onglet</option>' +
      '</select>' +
      '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'li\').remove()">×</button>';
    return row;
  }
})();

/* ── Escape helper ── */
function esc(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

/* ── Upload drag & drop on media index ── */
(function initUploadZone() {
  var zone = document.getElementById('upload-zone');
  var fileInput = document.getElementById('upload-file');
  var uploadForm = document.getElementById('upload-form');
  if (!zone || !fileInput || !uploadForm) return;

  zone.addEventListener('dragover', function(e) {
    e.preventDefault();
    zone.classList.add('drag-over');
  });
  zone.addEventListener('dragleave', function() { zone.classList.remove('drag-over'); });
  zone.addEventListener('drop', function(e) {
    e.preventDefault();
    zone.classList.remove('drag-over');
    if (e.dataTransfer.files.length) {
      fileInput.files = e.dataTransfer.files;
      uploadForm.submit();
    }
  });
  fileInput.addEventListener('change', function() {
    if (fileInput.files.length) uploadForm.submit();
  });
})();

/* ── Alt text AJAX save ── */
(function initAltSave() {
  document.querySelectorAll('.alt-save-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var card = btn.closest('[data-media-id]');
      var id = card.dataset.mediaId;
      var altInput = card.querySelector('.alt-input');
      var csrf = document.getElementById('csrf-token-value');
      if (!altInput || !csrf) return;

      fetch('/admin/media/alt', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id) + '&alt=' + encodeURIComponent(altInput.value) + '&csrf_token=' + encodeURIComponent(csrf.value),
      }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.ok) btn.textContent = 'Sauvegardé ✓';
        setTimeout(function() { btn.textContent = 'Sauvegarder'; }, 2000);
      });
    });
  });
})();

/* ── Delete confirmation ── */
document.querySelectorAll('.confirm-delete').forEach(function(form) {
  form.addEventListener('submit', function(e) {
    if (!confirm('Confirmer la suppression ?')) e.preventDefault();
  });
});

/* ── Highlight active sidebar link ── */
(function highlightSidebarLink() {
  var path = window.location.pathname;
  document.querySelectorAll('.sidebar nav a').forEach(function(a) {
    if (a.getAttribute('href') === path) {
      a.classList.add('active');
    }
  });
})();
