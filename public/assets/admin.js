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

  switch (type) {
    case 'heading':
      d.innerHTML =
        fg('Niveau', sel('level', [['h2','H2'],['h3','H3'],['h4','H4']], data.level)) +
        fg('Texte', inp('text', data.text, 'Titre…'));
      break;

    case 'text':
      d.innerHTML = fg('Contenu HTML', ta('text', data.text, '<p>Votre texte…</p>', 6));
      break;

    case 'image':
      d.innerHTML =
        fg('Image', mediaBtn('media_id', 'Médiathèque')) +
        fg('Alt', inp('alt', data.alt, 'Description de l\'image')) +
        fg('Légende', inp('caption', data.caption, 'Légende optionnelle'));
      break;

    case 'cta':
      d.innerHTML =
        fg('Label', inp('label', data.label, 'Cliquez ici')) +
        fg('URL', inp('url', data.url, 'https://…')) +
        fg('Ouverture', sel('target', [['_self','Même onglet'],['_blank','Nouvel onglet']], data.target));
      break;

    case 'html':
      d.innerHTML = fg('HTML libre', ta('html', data.html, '<div>…</div>', 8));
      break;

    case 'video':
      d.innerHTML =
        fg('URL (YouTube/Vimeo)', inp('url', data.url, 'https://www.youtube.com/watch?v=…')) +
        fg('Légende', inp('caption', data.caption, 'Légende optionnelle'));
      break;

    case 'file':
      d.innerHTML =
        fg('Fichier', mediaBtn('media_id', 'Médiathèque')) +
        fg('Label du lien', inp('label', data.label, 'Télécharger le document'));
      break;

    case 'accordion':
      d.innerHTML = '<div class="accordion-items" data-field="items"></div>'
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
        fg('Fonction / Titre', inp('role', data.role, 'CEO, Entreprise'));
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
        question: item.querySelector('[data-subfield="question"]').value,
        answer:   item.querySelector('[data-subfield="answer"]').value,
      });
    });
    return data;
  }

  body.querySelectorAll('[data-field]').forEach(function(el) {
    var field = el.dataset.field;
    if (field.endsWith('_display')) return; // skip display-only fields
    data[field] = el.value;
  });
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
