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

const KN_ICONS = [
  'sofa','armchair','bed','car','truck','home','factory','sparkles','wrench',
  'spray','droplets','wind','leaf','calendar','clock','check','check-big',
  'star','shield','award','heart','zap','pin','phone','arrow',
];

const BLOCK_TYPES = {
  heading:   'Titre',
  text:      'Texte',
  image:     'Image',
  cta:       'Bouton CTA',
  html:      'HTML libre',
  video:     'Vidéo',
  file:      'Fichier',
  accordion: 'FAQ',
  quote:     'Citation',
  'hero':      'Hero',
  'services':  'Grille services',
  'two-col':   'Deux colonnes',
  'how':       'Comment ça marche',
  'reviews':   'Avis clients',
  'zone':      'Zone d\'intervention',
  'cta-final':    'CTA final',
  'pricing':      'Grille de tarifs',
  'before-after': 'Avant / Apres',
  'logos':        'Logos partenaires',
  'seo-content':  'Contenu SEO',
  'domicile-vs-atelier': 'Domicile vs Atelier',
  'sofa-simulator':      'Simulateur canapé',
  'prestation':          'Prestation détaillée',
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
  function iconInp(name, val, placeholder) {
    val = val || '';
    return '<div class="input-row icon-field">'
      + '<input type="text" list="kn-icon-list" data-field="' + name + '" value="' + esc(val) + '" placeholder="' + esc(placeholder || 'canape') + '" style="flex:1">'
      + '<button type="button" class="btn btn-secondary btn-sm icon-pick-btn">Choisir</button>'
      + '<span class="icon-field__preview"></span>'
      + '</div>';
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
      + '<input type="text" data-field="' + targetField + '" value="" placeholder="/uploads/mon-image.jpg" style="flex:1">'
      + '<button type="button" class="btn btn-secondary btn-sm media-pick-btn" data-target="' + targetField + '" data-filter="image">' + labelText + '</button>'
      + '</div>';
  }

  function mediaBtnVideo(targetField, labelText) {
    return '<div class="input-row">'
      + '<input type="text" data-field="' + targetField + '" value="" placeholder="/uploads/ma-video.mp4" style="flex:1">'
      + '<button type="button" class="btn btn-secondary btn-sm media-pick-btn" data-target="' + targetField + '" data-filter="video">' + labelText + '</button>'
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
        fg('Texte <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>',
          ta('text', data.text, 'Ex : Nettoyage de <span class="kn-tape">canapés</span> à domicile', 2) +
          '<div style="font-size:.75rem;color:#888;margin:.5rem 0 0;display:flex;flex-wrap:wrap;gap:.4rem;align-items:center">'
          + '<span>Copier :</span>'
          + '<button type="button" class="kn-copy-snippet btn btn-secondary btn-sm" data-snippet=\'&lt;span class=&quot;kn-tape&quot;&gt;mot&lt;/span&gt;\' style="font-size:.72rem;padding:.2em .55em">kn-tape <small>souligné jaune</small></button>'
          + '<button type="button" class="kn-copy-snippet btn btn-secondary btn-sm" data-snippet=\'&lt;span class=&quot;kn-highlight&quot;&gt;mot&lt;/span&gt;\' style="font-size:.72rem;padding:.2em .55em">kn-highlight <small>fond bleu</small></button>'
          + '<button type="button" class="kn-copy-snippet btn btn-secondary btn-sm" data-snippet=\'&lt;span class=&quot;kn-marker&quot;&gt;mot&lt;/span&gt;\' style="font-size:.72rem;padding:.2em .55em">kn-marker <small>surligné</small></button>'
          + '</div>') +
        fg('Paragraphe <small style="font-weight:400;opacity:.6">(optionnel)</small>', ta('body', data.body, 'Description courte sous le titre…', 3)) +
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
        fg('Image', mediaBtn('image_url', 'Médiathèque')) +
        fg('Alt', inp('alt', data.alt, 'Description de l\'image')) +
        fg('Légende', inp('caption', data.caption, 'Légende optionnelle')) +
        bgField(data.bg) +
        visibilityField(data.visible);
      if (data.image_url || data.src) {
        var f = d.querySelector('[data-field="image_url"]');
        if (f) f.value = data.image_url || data.src || '';
      }
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
        fg('Fichier', mediaBtn('file_url', 'Médiathèque')) +
        fg('Label du lien', inp('label', data.label, 'Télécharger le document')) +
        visibilityField(data.visible);
      if (data.file_url || data.url) {
        var f = d.querySelector('[data-field="file_url"]');
        if (f) f.value = data.file_url || data.url || '';
      }
      break;

    case 'accordion':
      d.innerHTML = bgField(data.bg) +
        visibilityField(data.visible) +
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(optionnel)</small>', inp('eyebrow', data.eyebrow, 'FAQ')) +
        fg('Titre <small style="font-weight:400;opacity:.6">(optionnel)</small>', inp('h2', data.h2, 'Ex : Vos questions fréquentes')) +
        fg('Intro <small style="font-weight:400;opacity:.6">(optionnel)</small>', ta('intro', data.intro, 'Courte introduction avant les questions…', 2)) +
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
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('eyebrow', data.eyebrow, 'CANAPÉ · VOITURE · MATELAS — À DOMICILE', 2)) +
        fg('H1 <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('h1', data.h1, 'Nettoyage à domicile de canapé, matelas et voitures.', 3)) +
        '<div class="form-group"><div style="display:flex;flex-wrap:wrap;gap:.5rem;padding:.6rem .75rem;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);font-size:.8rem">' +
          '<span style="display:block;width:100%;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--color-muted);margin-bottom:.25rem">Surbrillances — cliquer pour insérer</span>' +
          '<button type="button" class="btn btn-sm hl-insert-btn" data-hl="hl"         style="background:transparent;border:1px solid #E4DECF;color:#1B2A4A" title=".hl — fond crème/blanc"><span style="background:linear-gradient(transparent 55%,#EFC6CB 55%,#EFC6CB 92%,transparent 92%);padding:0 .08em">.hl</span></button>' +
          '<button type="button" class="btn btn-sm hl-insert-btn" data-hl="hl-strong"  style="background:transparent;border:1px solid #E4DECF;color:#1B2A4A" title=".hl-strong — plus de punch"><span style="background:linear-gradient(transparent 55%,#E4AEB7 55%,#E4AEB7 92%,transparent 92%);padding:0 .08em">.hl-strong</span></button>' +
          '<button type="button" class="btn btn-sm hl-insert-btn" data-hl="hl-inverse" style="background:#24355C;border:1px solid #24355C;color:#F7F4EC"              title=".hl-inverse — sur fond navy"><span style="background:linear-gradient(transparent 55%,#A15D69 55%,#A15D69 92%,transparent 92%);padding:0 .08em">.hl-inverse</span></button>' +
          '<button type="button" class="btn btn-sm hl-insert-btn" data-hl="hl-on-rose" style="background:#EFC6CB;border:1px solid #E4AEB7;color:#1B2A4A"              title=".hl-on-rose — sur fond rose"><span style="background:linear-gradient(transparent 55%,#F7F4EC 55%,#F7F4EC 92%,transparent 92%);padding:0 .08em">.hl-on-rose</span></button>' +
        '</div></div>' +
        fg('Corps de texte (HTML autorisé)', ta('body', data.body, 'Keepnew nettoie vos <strong>canapés</strong>...')) +
        fg('CTA — Eyebrow', inp('cta_eyebrow', data.cta_eyebrow, 'RÉSERVATION EN LIGNE')) +
        fg('CTA — Titre', inp('cta_title', data.cta_title, 'Prendre RDV en 2 min')) +
        fg('Preuve sociale', inp('social_proof', data.social_proof, '4,9/5 · +110 avis · +400 canapés · +250 voitures')) +
        fg('Visuel — Type', sel('visual_type', [['photo','Photo (image)'],['video','Video depuis médiathèque'],['video_bg','Video en arrière-plan']], data.visual_type || 'photo')) +
        '<div data-visual-section="photo">' +
          fg('Image (depuis médiathèque)', mediaBtn('image_url', 'Choisir une image')) +
          fg('Alt texte image', inp('image_alt', data.image_alt, 'Description de l\'image')) +
        '</div>' +
        '<div data-visual-section="video">' +
          fg('Vidéo (depuis médiathèque)', mediaBtnVideo('video_url', 'Choisir une vidéo')) +
          fg('', '<label style="display:flex;align-items:center;gap:.4rem;font-weight:400"><input type="checkbox" data-field="video_autoplay"> Lecture auto</label>' +
             '<label style="display:flex;align-items:center;gap:.4rem;font-weight:400;margin-top:.25rem"><input type="checkbox" data-field="video_loop" checked> Boucle</label>' +
             '<label style="display:flex;align-items:center;gap:.4rem;font-weight:400;margin-top:.25rem"><input type="checkbox" data-field="video_muted" checked> Muet</label>' +
             '<label style="display:flex;align-items:center;gap:.4rem;font-weight:400;margin-top:.25rem"><input type="checkbox" data-field="video_controls"> Contrôles</label>') +
        '</div>' +
        '<div data-visual-section="video_bg">' +
          fg('Vidéo arrière-plan (depuis médiathèque)', mediaBtnVideo('video_bg_url', 'Choisir une vidéo')) +
          '<p style="margin:.25rem 0 0;font-size:.8rem;color:var(--color-muted)">La vidéo jouera en boucle, muette, en plein fond de section.</p>' +
        '</div>' +
        '<div class="form-group"><label>Pills <small style="font-weight:400;opacity:.6">(badges sous le H1)</small></label><div class="block-repeater" data-repeater="pills"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="pills">+ Ajouter un badge</button></div>';
      // Pre-fill image field if already set
      if (data.image_url) {
        var heroImgField = d.querySelector('[data-field="image_url"]');
        if (heroImgField) heroImgField.value = data.image_url;
      }
      if (data.video_url) {
        var heroVidField = d.querySelector('[data-field="video_url"]');
        if (heroVidField) heroVidField.value = data.video_url;
      }
      if (data.video_bg_url) {
        var heroVidBgField = d.querySelector('[data-field="video_bg_url"]');
        if (heroVidBgField) heroVidBgField.value = data.video_bg_url;
      }
      // Restore checkboxes
      ['video_autoplay','video_loop','video_muted','video_controls'].forEach(function(f) {
        var cb = d.querySelector('[data-field="' + f + '"]');
        if (cb && data[f] !== undefined) cb.checked = !!data[f];
      });
      // Surbrillance insert buttons
      d.querySelectorAll('.hl-insert-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var cls = btn.dataset.hl;
          var ta  = d.querySelector('[data-field="h1"]');
          var snippet = '<span class="' + cls + '">mot</span>';
          var start = ta.selectionStart, end = ta.selectionEnd;
          var selected = ta.value.slice(start, end);
          if (selected) snippet = '<span class="' + cls + '">' + selected + '</span>';
          ta.setRangeText(snippet, start, end, 'end');
          ta.focus();
        });
      });
      // Show/hide visual sections based on visual_type
      function updateHeroVisualSections() {
        var vt = d.querySelector('[data-field="visual_type"]').value;
        d.querySelectorAll('[data-visual-section]').forEach(function(sec) {
          sec.style.display = (sec.dataset.visualSection === vt) ? '' : 'none';
        });
      }
      updateHeroVisualSections();
      d.querySelector('[data-field="visual_type"]').addEventListener('change', updateHeroVisualSections);
      // Load existing pills
      (data.pills || []).forEach(function(pill) {
        addHeroPill(d.querySelector('[data-repeater="pills"]'), pill);
      });
      d.querySelector('[data-repeater-add="pills"]').addEventListener('click', function() {
        addHeroPill(d.querySelector('[data-repeater="pills"]'), {});
      });
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
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['name','desc','icon'], ['Nom','Description','Icône'], item);
      });
      d.querySelector('[data-repeater-add="items"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['name','desc','icon'], ['Nom','Description','Icône'], {});
      });
      break;

    case 'prestation':
      d.innerHTML =
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(optionnel)</small>', inp('eyebrow', data.eyebrow, 'CE QUI EST INCLUS')) +
        fg('H2 <small style="font-weight:400;opacity:.6">(HTML autorisé — surbrillance possible)</small>',
          ta('h2', data.h2, 'Ex : Tout est <span class="hl">compris</span> dans la prestation.', 2)) +
        fg('Intro <small style="font-weight:400;opacity:.6">(optionnel, HTML autorisé)</small>',
          ta('intro', data.intro, 'Une phrase qui pose le cadre de la prestation…', 3)) +
        bgField(data.bg) +
        layoutField(data.layout || '2-3') +
        visibilityField(data.visible) +
        '<hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb">' +
        '<div class="form-group"><label>Bandeau info <small style="font-weight:400;opacity:.6">(durée, garantie, produits…)</small></label>'
        + '<div class="block-repeater" data-repeater="metas"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="metas">+ Ajouter une info</button></div>' +
        '<hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb">' +
        fg('Titre de la liste <small style="font-weight:400;opacity:.6">(optionnel)</small>',
          inp('list_title', data.list_title, 'Ce que comprend la prestation')) +
        fg('Colonnes de la liste', sel('columns', [['1','1 colonne (liste dense)'],['2','2 colonnes'],['3','3 colonnes']], data.columns || '2')) +
        '<div class="form-group"><label>Points inclus</label><div class="block-repeater" data-repeater="items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="items">+ Ajouter un point</button></div>' +
        fg('Note bas de liste <small style="font-weight:400;opacity:.6">(optionnel)</small>',
          ta('note', data.note, 'Ex : Devis gratuit, sans engagement. Satisfait ou on revient.', 2)) +
        '<hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb">' +
        fg('Image <small style="font-weight:400;opacity:.6">(optionnel — la liste passe pleine largeur si vide)</small>',
          mediaBtn('image_url', 'Médiathèque')) +
        fg('Alt de l\'image', inp('image_alt', data.image_alt, 'Nettoyage d\'un canapé en tissu')) +
        fg('Label CTA <small style="font-weight:400;opacity:.6">(optionnel)</small>', inp('cta_label', data.cta_label, 'Réserver ma prestation')) +
        fg('URL CTA', inp('cta_url', data.cta_url, '/reservation'));

      if (data.image_url) {
        var prestaImg = d.querySelector('[data-field="image_url"]');
        if (prestaImg) prestaImg.value = data.image_url;
      }
      (data.metas || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="metas"]'), 'metas', ['icon','label','value'], ['Icône','Libellé','Valeur'], item);
      });
      d.querySelector('[data-repeater-add="metas"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="metas"]'), 'metas', ['icon','label','value'], ['Icône','Libellé','Valeur'], {});
      });
      (data.items || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['title','desc'], ['Intitulé','Description (optionnel)'], item);
      });
      d.querySelector('[data-repeater-add="items"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['title','desc'], ['Intitulé','Description (optionnel)'], {});
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
        fg('Label CTA gauche <small style="font-weight:400;opacity:.6">(optionnel)</small>', inp('left_cta_label', data.left_cta_label, 'En savoir plus')) +
        fg('URL CTA gauche', inp('left_cta_url', data.left_cta_url, '/contact')) +
        fg('Titre droite', inp('right_title', data.right_title, 'Atelier à Visé')) +
        fg('Corps droite', ta('right_body', data.right_body, 'Description…', 3)) +
        fg('Image (colonne droite)', mediaBtn('image_url', 'Choisir une image')) +
        fg('Alt texte image', inp('image_alt', data.image_alt, 'Description de l\'image'));
      if (data.image_url) {
        var f = d.querySelector('[data-field="image_url"]');
        if (f) f.value = data.image_url;
      }
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

    case 'pricing':
      d.innerHTML =
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('eyebrow', data.eyebrow, 'NOS TARIFS', 1)) +
        fg('H2 <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('h2', data.h2, 'Choisissez votre formule', 2)) +
        fg('Paragraphe intro <small style="font-weight:400;opacity:.6">(optionnel)</small>', ta('intro', data.intro, 'Courte description sous le titre…', 2)) +
        fg('Mention <small style="font-weight:400;opacity:.6">(optionnel — sous la grille)</small>', ta('note', data.note, 'Ex : * Prix HTVA. Déplacement inclus dans un rayon de 30 km.', 2)) +
        bgField(data.bg) +
        visibilityField(data.visible) +
        '<div class="form-group"><label>Offres</label><div class="block-repeater" data-repeater="items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="items">+ Ajouter une offre</button></div>';
      (data.items || []).forEach(function(item) {
        addPricingItem(d.querySelector('[data-repeater="items"]'), item);
      });
      d.querySelector('[data-repeater-add="items"]').addEventListener('click', function() {
        addPricingItem(d.querySelector('[data-repeater="items"]'), {});
      });
      break;

    case 'before-after':
      d.innerHTML =
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('eyebrow', data.eyebrow, 'AVANT / APRES', 1)) +
        fg('H2 <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('h2', data.h2, 'Voyez la différence', 2)) +
        bgField(data.bg || 'alt') +
        visibilityField(data.visible) +
        '<div class="form-group"><label>Paires d\'images</label><div class="block-repeater" data-repeater="items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="items">+ Ajouter une paire</button></div>';
      (data.items || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['image_before_url','image_after_url','caption'], ['Image Avant (/uploads/…)','Image Apres (/uploads/…)','Légende'], item);
      });
      d.querySelector('[data-repeater-add="items"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['image_before_url','image_after_url','caption'], ['Image Avant (/uploads/…)','Image Apres (/uploads/…)','Légende'], {});
      });
      break;

    case 'logos':
      d.innerHTML =
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('eyebrow', data.eyebrow, 'ILS NOUS FONT CONFIANCE', 1)) +
        fg('H2 <small style="font-weight:400;opacity:.6">(optionnel, HTML autorisé)</small>', ta('h2', data.h2, '', 2)) +
        bgField(data.bg || 'alt') +
        visibilityField(data.visible) +
        '<div class="form-group"><label>Logos</label><div class="block-repeater" data-repeater="items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="items">+ Ajouter un logo</button></div>';
      (data.items || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['image_url','alt','url'], ['Image (/uploads/…)','Texte alternatif','URL (optionnel)'], item);
      });
      d.querySelector('[data-repeater-add="items"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="items"]'), 'items', ['image_url','alt','url'], ['Image (/uploads/…)','Texte alternatif','URL (optionnel)'], {});
      });
      break;

    case 'domicile-vs-atelier':
      d.innerHTML =
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(optionnel)</small>', inp('eyebrow', data.eyebrow, 'KEEPNEW')) +
        fg('H2 <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('h2', data.h2, 'Réservez rapidement en ligne…', 2)) +
        fg('Intro <small style="font-weight:400;opacity:.6">(optionnel)</small>', ta('intro', data.intro, 'Courte intro sous le titre…', 2)) +
        bgField(data.bg) +
        visibilityField(data.visible) +
        '<div class="form-group"><label>Étapes (pills)</label><div class="block-repeater" data-repeater="steps"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="steps">+ Ajouter une étape</button></div>' +
        '<hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb">' +
        '<h4 style="margin:.5rem 0 1rem">Carte « À domicile »</h4>' +
        fg('Icône', iconInp('home_icon', data.home_icon, 'domicile')) +
        fg('Titre', inp('home_title', data.home_title, 'À domicile')) +
        fg('Description', ta('home_desc', data.home_desc, 'Pour un service pratique…', 2)) +
        '<div class="form-group"><label>Prestations</label><div class="block-repeater" data-repeater="home_items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="home_items">+ Ajouter une prestation</button></div>' +
        fg('Note bas de carte', inp('home_note', data.home_note, 'RDV en ligne disponible.')) +
        fg('CTA — Eyebrow', inp('home_cta_eyebrow', data.home_cta_eyebrow, '📅 RÉSERVATION EN LIGNE')) +
        fg('CTA — Titre', inp('home_cta_title', data.home_cta_title, 'Prendre RDV en 2 min')) +
        fg('CTA — URL', inp('home_cta_url', data.home_cta_url, '/reservation')) +
        '<hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb">' +
        '<h4 style="margin:.5rem 0 1rem">Carte « En atelier »</h4>' +
        fg('Icône', iconInp('workshop_icon', data.workshop_icon, 'atelier')) +
        fg('Titre', inp('workshop_title', data.workshop_title, 'En atelier Keepnew')) +
        fg('Description', ta('workshop_desc', data.workshop_desc, 'Idéal si vous n\'avez pas d\'espace…', 2)) +
        fg('Adresse', inp('workshop_address', data.workshop_address, 'Rue des Cyclistes Frontières 24, 4600 Visé')) +
        '<div class="form-group"><label>Prestations</label><div class="block-repeater" data-repeater="workshop_items"></div>'
        + '<button type="button" class="btn btn-secondary btn-sm block-repeater-add" data-repeater-add="workshop_items">+ Ajouter une prestation</button></div>' +
        fg('Note bas de carte', inp('workshop_note', data.workshop_note, 'RDV en ligne disponible…')) +
        fg('CTA — Eyebrow', inp('workshop_cta_eyebrow', data.workshop_cta_eyebrow, '📅 RÉSERVATION EN LIGNE')) +
        fg('CTA — Titre', inp('workshop_cta_title', data.workshop_cta_title, 'Prendre RDV à l\'atelier')) +
        fg('CTA — URL', inp('workshop_cta_url', data.workshop_cta_url, '/atelier'));
      (data.steps || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="steps"]'), 'steps', ['icon','label'], ['Icône','Libellé'], item);
      });
      d.querySelector('[data-repeater-add="steps"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="steps"]'), 'steps', ['icon','label'], ['Icône','Libellé'], {});
      });
      (data.home_items || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="home_items"]'), 'home_items', ['icon','label'], ['Icône','Libellé'], item);
      });
      d.querySelector('[data-repeater-add="home_items"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="home_items"]'), 'home_items', ['icon','label'], ['Icône','Libellé'], {});
      });
      (data.workshop_items || []).forEach(function(item) {
        addRepeaterRow(d.querySelector('[data-repeater="workshop_items"]'), 'workshop_items', ['icon','label'], ['Icône','Libellé'], item);
      });
      d.querySelector('[data-repeater-add="workshop_items"]').addEventListener('click', function() {
        addRepeaterRow(d.querySelector('[data-repeater="workshop_items"]'), 'workshop_items', ['icon','label'], ['Icône','Libellé'], {});
      });
      break;

    case 'sofa-simulator':
      d.innerHTML =
        fg('Eyebrow <small style="font-weight:400;opacity:.6">(optionnel)</small>', inp('eyebrow', data.eyebrow, 'SIMULATEUR')) +
        fg('H2', inp('h2', data.h2, 'Estimez le nettoyage de votre canapé')) +
        fg('Intro <small style="font-weight:400;opacity:.6">(optionnel)</small>', ta('intro', data.intro, 'Sélectionnez la forme, le nombre de places et de méridiennes pour obtenir une estimation.', 2)) +
        bgField(data.bg || 'cream') +
        visibilityField(data.visible);
      break;

    case 'seo-content':
      d.innerHTML =
        fg('H2 <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('h2', data.h2, 'Titre de la section', 2)) +
        fg('Contenu <small style="font-weight:400;opacity:.6">(HTML autorisé)</small>', ta('body', data.body, '<p>Votre contenu…</p>', 8)) +
        fg('Disposition', sel('layout', [['text-only','Texte seul (pleine largeur)'],['image-right','Image à droite'],['image-left','Image à gauche']], data.layout || 'text-only')) +
        fg('Image', mediaBtn('image_url', 'Choisir une image')) +
        fg('Alt texte image', inp('image_alt', data.image_alt, 'Description de l\'image')) +
        bgField(data.bg) +
        visibilityField(data.visible);
      if (data.image_url) {
        var scImgField = d.querySelector('[data-field="image_url"]');
        if (scImgField) scImgField.value = data.image_url;
      }
      break;
  }

  // Copy snippet buttons (heading block)
  d.querySelectorAll('.kn-copy-snippet').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var snippet = btn.dataset.snippet;
      navigator.clipboard.writeText(snippet).then(function() {
        var orig = btn.innerHTML;
        btn.textContent = '✓ Copié !';
        setTimeout(function() { btn.innerHTML = orig; }, 1500);
      });
    });
  });

  // Init media pick buttons
  d.querySelectorAll('.media-pick-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var mediaFilter = btn.dataset.filter || null;
      openMediaModal(function(media) {
        var target = btn.dataset.target;
        var hiddenField = d.querySelector('[data-field="' + target + '"]');
        var displayField = d.querySelector('[data-field="' + target + '_display"]');
        var isUrlField = target.endsWith('_url') || target.endsWith('url');
        if (hiddenField) hiddenField.value = isUrlField ? (media.webp_path || media.path) : media.id;
        if (displayField) displayField.value = media.original_name || media.filename;
      }, mediaFilter);
    });
  });

  // Aperçu des champs icône déjà renseignés
  d.querySelectorAll('.icon-field input').forEach(function(f) { refreshIconPreview(f); });

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
  var html = '<div class="block-repeater-row__fields">';
  for (var i = 0; i < fields.length; i++) {
    var f = fields[i];
    var l = labels[i] || f;
    if (f === 'icon') {
      html += '<label class="block-repeater-row__label">' + esc(l)
        + '<span class="input-row icon-field">'
        +   '<input type="text" list="kn-icon-list" data-rfield="icon" placeholder="canape, voiture…" value="' + esc(data[f] || '') + '" style="flex:1;min-width:0">'
        +   '<button type="button" class="btn btn-secondary btn-sm icon-pick-btn">Choisir</button>'
        +   '<span class="icon-field__preview"></span>'
        + '</span></label>';
    } else {
      html += '<label class="block-repeater-row__label">' + esc(l) + '<input type="text" data-rfield="' + esc(f) + '" placeholder="' + esc(l) + '" value="' + esc(data[f] || '') + '"></label>';
    }
  }
  html += '</div><button type="button" class="btn btn-danger btn-sm remove-repeater-row" title="Supprimer">×</button>';
  row.innerHTML = html;
  row.querySelector('.remove-repeater-row').addEventListener('click', function() {
    row.remove();
  });
  container.appendChild(row);
}

function addPricingItem(container, data) {
  data = data || {};
  var row = document.createElement('div');
  row.className = 'block-repeater-row';
  row.innerHTML =
    '<div class="block-repeater-row__fields">'
    + '<label class="block-repeater-row__label">Label<input type="text" data-rfield="label" value="' + esc(data.label || '') + '" placeholder="Ex: 1 à 3 places"></label>'
    + '<label class="block-repeater-row__label">Prix<input type="text" data-rfield="price" value="' + esc(data.price || '') + '" placeholder="99 €"></label>'
    + '<label class="block-repeater-row__label">URL CTA<input type="text" data-rfield="cta_url" value="' + esc(data.cta_url || '') + '" placeholder="#"></label>'
    + '<label class="block-repeater-row__label">Texte CTA<input type="text" data-rfield="cta_text" value="' + esc(data.cta_text || '') + '" placeholder="Réserver"></label>'
    + '<label class="block-repeater-row__label">Image'
    +   '<div class="input-row" style="gap:.35rem">'
    +     '<input type="text" data-rfield="image_url" value="' + esc(data.image_url || '') + '" placeholder="/uploads/…" style="flex:1;min-width:0">'
    +     '<button type="button" class="btn btn-secondary btn-sm pricing-media-btn">Médiathèque</button>'
    +   '</div>'
    + '</label>'
    + '</div>'
    + '<button type="button" class="btn btn-danger btn-sm remove-repeater-row" title="Supprimer">×</button>';
  row.querySelector('.remove-repeater-row').addEventListener('click', function() { row.remove(); });
  row.querySelector('.pricing-media-btn').addEventListener('click', function() {
    var imgField = row.querySelector('[data-rfield="image_url"]');
    openMediaModal(function(media) {
      imgField.value = media.webp_path || media.path || '';
    });
  });
  container.appendChild(row);
}

function addHeroPill(container, data) {
  data = data || {};
  var styleOptions = [['white','⬜ Blanc'],['yellow','🟡 Jaune'],['night','⬛ Sombre'],['blue','🔵 Bleu'],['cream','🟤 Cream'],['rose','🌸 Rose']];
  var selHtml = '<select data-rfield="style">';
  styleOptions.forEach(function(o) {
    selHtml += '<option value="' + o[0] + '"' + (data.style === o[0] ? ' selected' : '') + '>' + o[1] + '</option>';
  });
  selHtml += '</select>';
  var row = document.createElement('div');
  row.className = 'block-repeater-row';
  row.innerHTML = '<div class="block-repeater-row__fields">'
    + '<label class="block-repeater-row__label">Texte<input type="text" data-rfield="text" value="' + esc(data.text || '') + '" placeholder="✓ À domicile"></label>'
    + '<label class="block-repeater-row__label">Style' + selHtml + '</label>'
    + '</div><button type="button" class="btn btn-danger btn-sm remove-repeater-row" title="Supprimer">×</button>';
  row.querySelector('.remove-repeater-row').addEventListener('click', function() { row.remove(); });
  container.appendChild(row);
}

function addAccordionItem(container, data) {
  data = data || {};
  var item = document.createElement('div');
  item.className = 'accordion-item';
  item.innerHTML =
    '<div class="form-group"><label>Question</label><input type="text" data-subfield="question" value="' + esc(data.question || data.q || '') + '" placeholder="Question…"></div>' +
    '<div class="form-group"><label>Réponse</label><textarea data-subfield="answer" rows="2" placeholder="Réponse…">' + esc(data.answer || data.a || '') + '</textarea></div>' +
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
        question: item.querySelector('[data-subfield="question"]') ? item.querySelector('[data-subfield="question"]').value : '',
        answer:   item.querySelector('[data-subfield="answer"]')   ? item.querySelector('[data-subfield="answer"]').value   : '',
      });
    });
    // also collect bg + visible for accordion (skip the items container div)
    body.querySelectorAll('[data-field]').forEach(function(el) {
      if (el.dataset.field === 'items') return;
      if (el.type === 'checkbox') { if (el.checked) data[el.dataset.field] = true; return; }
      data[el.dataset.field] = el.value;
    });
    var vis = [];
    body.querySelectorAll('.vis-checks input[data-vis]').forEach(function(cb) { if (cb.checked) vis.push(cb.dataset.vis); });
    if (vis.length) data.visible = vis;
    return data;
  }

  // Types with repeater sub-items (string or array of keys)
  var repeaterTypes = {
    'services':     'items',
    'how':          'steps',
    'zone':         'pills',
    'hero':         'pills',
    'pricing':      'items',
    'before-after': 'items',
    'logos':        'items',
    'prestation':   ['items','metas'],
    'domicile-vs-atelier': ['steps','home_items','workshop_items'],
  };

  if (repeaterTypes[type]) {
    var keys = repeaterTypes[type];
    if (typeof keys === 'string') keys = [keys];
    // Collect regular fields first
    body.querySelectorAll('[data-field]').forEach(function(el) {
      if (el.dataset.field.endsWith('_display')) return;
      if (el.type === 'checkbox') { data[el.dataset.field] = el.checked; return; }
      data[el.dataset.field] = el.value;
    });
    // Collect each repeater
    keys.forEach(function(repeaterKey) {
      var rows = [];
      body.querySelectorAll('[data-repeater="' + repeaterKey + '"] .block-repeater-row').forEach(function(row) {
        var obj = {};
        row.querySelectorAll('[data-rfield]').forEach(function(inp) {
          obj[inp.dataset.rfield] = inp.value;
        });
        rows.push(obj);
      });
      data[repeaterKey] = rows;
    });
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
    '<label class="block-checkbox-wrap" title="Sélectionner">' +
      '<input type="checkbox" class="block-checkbox">' +
    '</label>' +
    '<span class="block-handle" title="Déplacer">⠿</span>' +
    '<span class="block-type-label">' + (BLOCK_TYPES[type] || type) + '</span>' +
    '<div class="block-controls">' +
      '<button type="button" class="btn btn-secondary btn-sm toggle-block-btn" title="Ouvrir/Fermer">▾</button>' +
    '</div>';

  var form = buildBlockForm(type, data);
  // Start collapsed
  form.style.display = 'none';
  item.classList.add('collapsed');

  item.appendChild(header);
  item.appendChild(form);

  header.querySelector('.toggle-block-btn').addEventListener('click', function() {
    var collapsed = item.classList.toggle('collapsed');
    form.style.display = collapsed ? 'none' : '';
    this.textContent = collapsed ? '▾' : '▴';
  });

  // Click on header label also toggles
  header.querySelector('.block-type-label').addEventListener('click', function() {
    header.querySelector('.toggle-block-btn').click();
  });

  // Checkbox selection: update bulk bar and highlight
  header.querySelector('.block-checkbox').addEventListener('change', function() {
    item.classList.toggle('selected', this.checked);
    updateBlockBulkBar();
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

  // Bulk delete bar
  var bulkBar = document.createElement('div');
  bulkBar.id = 'block-bulk-bar';
  bulkBar.className = 'block-bulk-bar hidden';
  bulkBar.innerHTML =
    '<span class="block-bulk-count"></span>' +
    '<div style="display:flex;gap:.5rem">' +
      '<button type="button" class="btn btn-secondary btn-sm" id="block-deselect-btn">Désélectionner tout</button>' +
      '<button type="button" class="btn btn-danger btn-sm" id="block-delete-selected-btn">Supprimer la sélection</button>' +
    '</div>';
  editor.appendChild(bulkBar);

  window.updateBlockBulkBar = function() {
    var checked = blockList.querySelectorAll('.block-checkbox:checked');
    if (checked.length > 0) {
      bulkBar.classList.remove('hidden');
      bulkBar.querySelector('.block-bulk-count').textContent = checked.length + ' bloc' + (checked.length > 1 ? 's' : '') + ' sélectionné' + (checked.length > 1 ? 's' : '');
    } else {
      bulkBar.classList.add('hidden');
    }
  };

  bulkBar.querySelector('#block-deselect-btn').addEventListener('click', function() {
    blockList.querySelectorAll('.block-checkbox').forEach(function(cb) { cb.checked = false; });
    window.updateBlockBulkBar();
  });

  bulkBar.querySelector('#block-delete-selected-btn').addEventListener('click', function() {
    var checked = blockList.querySelectorAll('.block-checkbox:checked');
    var n = checked.length;
    if (!n) return;
    if (!confirm('Supprimer ' + n + ' bloc' + (n > 1 ? 's' : '') + ' ? Cette action est irréversible.')) return;
    checked.forEach(function(cb) { cb.closest('.block-item').remove(); });
    window.updateBlockBulkBar();
  });

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
var mediaModalCache    = null;  // cached items array
var mediaModalTotal    = 0;
var mediaModalOffset   = 0;
var mediaModalFilter   = null;

function openMediaModal(callback, filter) {
  mediaModalCallback = callback;
  mediaModalFilter   = filter || null;
  var overlay = document.getElementById('media-modal');
  if (!overlay) return;
  overlay.classList.remove('hidden');

  var grid = overlay.querySelector('#media-modal-grid');

  // Use cache if already loaded for same filter context
  if (mediaModalCache) {
    renderModalItems(grid, mediaModalCache, filter);
    return;
  }

  grid.innerHTML = '<p>Chargement…</p>';
  mediaModalOffset = 0;
  fetchModalPage(grid, 0, filter, true);
}

function fetchModalPage(grid, offset, filter, replace) {
  fetch('/admin/media/json?offset=' + offset)
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (replace) {
        mediaModalCache  = data.items;
        grid.innerHTML   = '';
      } else {
        mediaModalCache  = (mediaModalCache || []).concat(data.items);
      }
      mediaModalTotal  = data.total;
      mediaModalOffset = offset + data.items.length;

      // Remove existing load-more button before re-rendering
      var prev = grid.querySelector('.modal-load-more');
      if (prev) prev.remove();

      renderModalItems(grid, replace ? data.items : data.items, filter);

      if (mediaModalOffset < mediaModalTotal) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-secondary btn-sm modal-load-more';
        btn.style.cssText = 'display:block;margin:1rem auto 0;';
        btn.textContent = 'Charger plus (' + (mediaModalTotal - mediaModalOffset) + ' restants)';
        btn.addEventListener('click', function() {
          btn.textContent = 'Chargement…';
          btn.disabled = true;
          fetchModalPage(grid, mediaModalOffset, filter, false);
        });
        grid.appendChild(btn);
      }
    })
    .catch(function() {
      grid.innerHTML = '<p style="color:var(--color-danger)">Erreur lors du chargement.</p>';
    });
}

function renderModalItems(grid, items, filter) {
  var filtered = items;
  if (filter === 'image') filtered = items.filter(function(m) { return m.mime_type && m.mime_type.startsWith('image/'); });
  else if (filter === 'video') filtered = items.filter(function(m) { return m.mime_type && m.mime_type.startsWith('video/'); });

  if (!filtered.length && !grid.querySelector('.media-card')) {
    grid.innerHTML = '<p style="color:var(--color-muted)">Aucun fichier dans la médiathèque.</p>';
    return;
  }

  // Insert before load-more button
  var loadMoreBtn = grid.querySelector('.modal-load-more');
  filtered.forEach(function(m) {
    var card = document.createElement('div');
    card.className = 'media-card';
    var isImage = m.mime_type && m.mime_type.startsWith('image/');
    var isSvg   = m.mime_type === 'image/svg+xml';
    var isVideo = m.mime_type && m.mime_type.startsWith('video/');
    if (isImage) {
      var mSizes = m.sizes ? JSON.parse(m.sizes) : {};
      var mSrc   = isSvg ? m.path : (mSizes.thumb_webp || mSizes.thumb || m.path);
      card.innerHTML = '<img src="' + esc(mSrc) + '" alt="' + esc(m.alt || '') + '" loading="lazy"' + (isSvg ? ' style="object-fit:contain;background:#f5f5f5"' : '') + '>';
    } else if (isVideo) {
      var mExt = (m.original_name || '').split('.').pop().toUpperCase();
      card.innerHTML = '<div class="media-video-thumb media-video-placeholder" data-src="' + esc(m.path) + '" style="cursor:pointer"><div class="media-video-icon">&#9654;</div><span class="media-video-ext">' + mExt + '</span></div>';
    } else {
      card.innerHTML = '<div class="media-icon">📄</div>';
    }
    card.innerHTML += '<div class="media-info"><div class="media-name">' + esc(m.original_name) + '</div></div>';
    card.addEventListener('click', function() {
      var cb = mediaModalCallback;
      closeMediaModal();
      if (cb) cb(m);
    });
    if (loadMoreBtn) {
      grid.insertBefore(card, loadMoreBtn);
    } else {
      grid.appendChild(card);
    }
  });
}

function closeMediaModal() {
  var overlay = document.getElementById('media-modal');
  if (overlay) overlay.classList.add('hidden');
  mediaModalCallback = null;
}

function invalidateMediaCache() {
  mediaModalCache  = null;
  mediaModalTotal  = 0;
  mediaModalOffset = 0;
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

/* ── Name + Alt AJAX save (media library) ── */
(function initMetaSave() {
  var csrf = document.getElementById('csrf-token-value');
  if (!csrf) return;
  document.querySelectorAll('.meta-save-btn').forEach(function(btn) {
    if (btn.dataset.bound) return;
    btn.dataset.bound = '1';
    btn.addEventListener('click', function() {
      var card = btn.closest('[data-media-id]');
      var id   = card ? card.dataset.mediaId : null;
      if (!id) return;
      var name = (card.querySelector('.name-input') || {}).value || '';
      var alt  = (card.querySelector('.alt-input')  || {}).value || '';
      btn.textContent = '…'; btn.disabled = true;
      fetch('/admin/media/meta', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id) + '&name=' + encodeURIComponent(name) + '&alt=' + encodeURIComponent(alt) + '&csrf_token=' + encodeURIComponent(csrf.value),
      }).then(function(r) { return r.json(); }).then(function(data) {
        btn.textContent = data.ok ? 'Sauvegardé ✓' : 'Erreur';
        btn.disabled = false;
        setTimeout(function() { btn.textContent = 'Sauvegarder'; }, 2000);
      }).catch(function() { btn.textContent = 'Erreur'; btn.disabled = false; });
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

/* ── Sélecteur d'icônes ─────────────────────────────────────── */
var iconModalCallback = null;

function knIconSvg(name) {
  var lib = window.KN_ICON_LIBRARY || [];
  for (var i = 0; i < lib.length; i++) {
    if (lib[i].name === name) return lib[i].svg;
  }
  return '';
}

/* Aperçu à droite du champ, mis à jour à chaque frappe */
function refreshIconPreview(field) {
  var wrap = field.closest('.icon-field');
  if (!wrap) return;
  var prev = wrap.querySelector('.icon-field__preview');
  if (!prev) return;
  var svg = knIconSvg(field.value.trim());
  prev.innerHTML = svg;
  prev.classList.toggle('is-empty', !svg);
  prev.title = svg ? field.value.trim() : 'Icône inconnue';
}

function openIconModal(callback) {
  iconModalCallback = callback;
  var overlay = document.getElementById('icon-modal');
  if (!overlay) return;
  overlay.classList.remove('hidden');
  var search = document.getElementById('icon-modal-search');
  if (search) { search.value = ''; search.focus(); }
  renderIconGrid('');
}

function renderIconGrid(query) {
  var grid = document.getElementById('icon-modal-grid');
  if (!grid) return;
  var lib = window.KN_ICON_LIBRARY || [];
  query = (query || '').toLowerCase().trim();

  var groups = {};
  lib.forEach(function(ic) {
    if (query && ic.name.toLowerCase().indexOf(query) === -1) return;
    (groups[ic.group] = groups[ic.group] || []).push(ic);
  });

  var html = '';
  Object.keys(groups).forEach(function(g) {
    html += '<p class="icon-grid__group">' + g + '</p><div class="icon-grid__items">';
    groups[g].forEach(function(ic) {
      html += '<button type="button" class="icon-grid__item" data-icon-name="' + esc(ic.name) + '" title="' + esc(ic.name) + '">'
           +    ic.svg + '<span>' + esc(ic.name) + '</span></button>';
    });
    html += '</div>';
  });
  if (html === '') html = '<p style="color:var(--color-muted)">Aucune icône ne correspond.</p>';
  grid.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
  var overlay = document.getElementById('icon-modal');
  if (!overlay) return;

  var search = document.getElementById('icon-modal-search');
  if (search) search.addEventListener('input', function() { renderIconGrid(search.value); });

  overlay.querySelector('.modal-close').addEventListener('click', function() { overlay.classList.add('hidden'); });
  overlay.addEventListener('click', function(e) { if (e.target === overlay) overlay.classList.add('hidden'); });

  overlay.addEventListener('click', function(e) {
    var item = e.target.closest('.icon-grid__item');
    if (!item) return;
    if (iconModalCallback) iconModalCallback(item.dataset.iconName);
    overlay.classList.add('hidden');
  });
});

/* Délégation : vaut pour les champs créés dynamiquement */
document.addEventListener('click', function(e) {
  var btn = e.target.closest('.icon-pick-btn');
  if (!btn) return;
  var wrap  = btn.closest('.icon-field');
  var field = wrap && wrap.querySelector('input');
  if (!field) return;
  openIconModal(function(name) {
    field.value = name;
    refreshIconPreview(field);
  });
});

document.addEventListener('input', function(e) {
  if (e.target.closest('.icon-field')) refreshIconPreview(e.target);
});
