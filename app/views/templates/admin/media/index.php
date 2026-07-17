<div class="page-header">
  <h1>Médiathèque</h1>
  <form method="POST" action="/admin/media/reprocess" style="margin:0;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <button type="submit" class="btn btn-secondary" onclick="return confirm('Retraiter toutes les images sans variantes ? Cela peut prendre quelques minutes.');">Retraiter les images</button>
  </form>
</div>

<!-- Upload zone -->
<form id="upload-form" method="POST" action="/admin/media/upload" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <div id="upload-zone" class="drop-zone">
    <label for="upload-file">
      <strong>Glissez un fichier ici</strong> ou <u>cliquez pour choisir</u><br>
      <small>JPEG, PNG, GIF, WebP, SVG, PDF — 5 Mo max &nbsp;|&nbsp; MP4, WebM, OGV — 100 Mo max</small>
    </label>
    <input type="file" id="upload-file" name="file" accept="image/*,.svg,application/pdf,video/mp4,video/webm,video/ogg" style="display:none">
  </div>
</form>

<!-- Hidden CSRF for AJAX -->
<input type="hidden" id="csrf-token-value" value="<?= htmlspecialchars($csrf_token) ?>">

<div class="card">
  <?php if (empty($media)): ?>
    <p class="text-muted">Aucun fichier dans la médiathèque.</p>
  <?php else: ?>
  <!-- Filter tabs -->
  <div class="media-filter-tabs" style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap;">
    <button type="button" class="btn btn-secondary btn-sm media-tab active" data-filter="all">Tous</button>
    <button type="button" class="btn btn-secondary btn-sm media-tab" data-filter="image">Images</button>
    <button type="button" class="btn btn-secondary btn-sm media-tab" data-filter="video">Vidéos</button>
    <button type="button" class="btn btn-secondary btn-sm media-tab" data-filter="document">Documents</button>
  </div>
  <div class="media-grid">
    <?php foreach ($media as $file):
      $mime = $file['mime_type'] ?? '';
      $isImage = (strncmp($mime, 'image/', 6) === 0);
      $isSvg   = ($mime === 'image/svg+xml');
      $isVideo = (strncmp($mime, 'video/', 6) === 0);
      $isPdf   = ($mime === 'application/pdf');
      $filterKey = $isImage ? 'image' : ($isVideo ? 'video' : 'document');
    ?>
    <div class="media-card" data-media-id="<?= (int)$file['id'] ?>" data-filter="<?= $filterKey ?>">
      <?php if ($isSvg): ?>
        <img src="<?= htmlspecialchars($file['path']) ?>" alt="<?= htmlspecialchars($file['alt'] ?? '') ?>" loading="lazy" style="object-fit:contain;background:#f5f5f5">
      <?php elseif ($isImage): ?>
        <img src="<?= htmlspecialchars($file['path']) ?>" alt="<?= htmlspecialchars($file['alt'] ?? '') ?>" loading="lazy">
      <?php elseif ($isVideo): ?>
        <div class="media-video-thumb">
          <video src="<?= htmlspecialchars($file['path']) ?>" preload="metadata" muted playsinline></video>
          <div class="media-video-play">&#9654;</div>
        </div>
        <span class="media-type-badge">VIDEO</span>
      <?php else: ?>
        <div class="media-icon">📄</div>
        <?php if ($isPdf): ?><span class="media-type-badge">PDF</span><?php endif; ?>
      <?php endif; ?>
      <div class="media-info">
        <div class="media-name" title="<?= htmlspecialchars($file['original_name']) ?>"><?= htmlspecialchars($file['original_name']) ?></div>
        <?php if (!$isVideo): ?>
        <input type="text" class="alt-input" value="<?= htmlspecialchars($file['alt'] ?? '') ?>" placeholder="Texte alternatif" style="margin-top:.3rem">
        <?php endif; ?>
        <div class="media-actions" style="margin-top:.3rem">
          <?php if (!$isVideo): ?>
          <button type="button" class="btn btn-secondary btn-sm alt-save-btn">Sauvegarder</button>
          <?php endif; ?>
          <form method="POST" action="/admin/media/delete" class="confirm-delete" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= (int)$file['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">✕</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if (!empty($has_more)): ?>
  <div style="text-align:center;margin-top:1.25rem">
    <button type="button" id="load-more-btn" class="btn btn-secondary"
            data-offset="60" data-total="<?= (int)$total ?>">
      Charger plus <span id="load-more-count">(<?= max(0, (int)$total - 60) ?> restants)</span>
    </button>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<script>
(function() {
  var currentFilter = 'all';

  // Filter tabs
  var tabs = document.querySelectorAll('.media-tab');
  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      tabs.forEach(function(t) { t.classList.remove('active'); });
      tab.classList.add('active');
      currentFilter = tab.dataset.filter;
      document.querySelectorAll('.media-card[data-filter]').forEach(function(card) {
        card.style.display = (currentFilter === 'all' || card.dataset.filter === currentFilter) ? '' : 'none';
      });
    });
  });

  // Load more
  var loadMoreBtn = document.getElementById('load-more-btn');
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
      var offset = parseInt(loadMoreBtn.dataset.offset, 10);
      var total  = parseInt(loadMoreBtn.dataset.total, 10);
      loadMoreBtn.textContent = 'Chargement…';
      loadMoreBtn.disabled = true;
      fetch('/admin/media/json?offset=' + offset)
        .then(function(r) { return r.json(); })
        .then(function(data) {
          var grid = document.querySelector('.media-grid');
          var csrfToken = document.getElementById('csrf-token-value').value;
          data.items.forEach(function(m) {
            var isImage = m.mime_type && m.mime_type.startsWith('image/');
            var isSvg   = m.mime_type === 'image/svg+xml';
            var isVideo = m.mime_type && m.mime_type.startsWith('video/');
            var filterKey = isImage ? 'image' : (isVideo ? 'video' : 'document');
            var card = document.createElement('div');
            card.className = 'media-card';
            card.dataset.mediaId = m.id;
            card.dataset.filter  = filterKey;
            if (currentFilter !== 'all' && filterKey !== currentFilter) card.style.display = 'none';
            var thumb = '';
            if (isSvg || isImage) {
              thumb = '<img src="' + m.path + '" alt="' + (m.alt||'') + '" loading="lazy"' + (isSvg ? ' style="object-fit:contain;background:#f5f5f5"' : '') + '>';
            } else if (isVideo) {
              thumb = '<div class="media-video-thumb"><video src="' + m.path + '" preload="metadata" muted playsinline></video><div class="media-video-play">&#9654;</div></div><span class="media-type-badge">VIDEO</span>';
            } else {
              thumb = '<div class="media-icon">📄</div>' + (m.mime_type === 'application/pdf' ? '<span class="media-type-badge">PDF</span>' : '');
            }
            var altInput = !isVideo ? '<input type="text" class="alt-input" value="' + (m.alt||'').replace(/"/g,'&quot;') + '" placeholder="Texte alternatif" style="margin-top:.3rem">' : '';
            var altBtn   = !isVideo ? '<button type="button" class="btn btn-secondary btn-sm alt-save-btn">Sauvegarder</button>' : '';
            card.innerHTML = thumb + '<div class="media-info"><div class="media-name" title="' + m.original_name + '">' + m.original_name + '</div>' + altInput + '<div class="media-actions" style="margin-top:.3rem">' + altBtn + '<form method="POST" action="/admin/media/delete" class="confirm-delete" style="display:inline"><input type="hidden" name="csrf_token" value="' + csrfToken + '"><input type="hidden" name="id" value="' + m.id + '"><button type="submit" class="btn btn-danger btn-sm">✕</button></form></div></div>';
            grid.appendChild(card);
          });
          var newOffset = offset + data.items.length;
          if (newOffset >= total) {
            loadMoreBtn.parentNode.remove();
          } else {
            loadMoreBtn.dataset.offset = newOffset;
            var remaining = total - newOffset;
            loadMoreBtn.innerHTML = 'Charger plus <span>(' + remaining + ' restants)</span>';
            loadMoreBtn.disabled = false;
          }
        })
        .catch(function() {
          loadMoreBtn.textContent = 'Erreur — réessayer';
          loadMoreBtn.disabled = false;
        });
    });
  }
})();
</script>
