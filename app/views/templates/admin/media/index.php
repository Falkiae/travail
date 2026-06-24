<div class="page-header">
  <h1>Médiathèque</h1>
</div>

<!-- Upload zone -->
<form id="upload-form" method="POST" action="/admin/media/upload" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <div id="upload-zone" class="drop-zone">
    <label for="upload-file">
      <strong>Glissez un fichier ici</strong> ou <u>cliquez pour choisir</u><br>
      <small>JPEG, PNG, GIF, WebP, PDF — 5 Mo max &nbsp;|&nbsp; MP4, WebM, OGV — 100 Mo max</small>
    </label>
    <input type="file" id="upload-file" name="file" accept="image/*,application/pdf,video/mp4,video/webm,video/ogg" style="display:none">
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
      $isVideo = (strncmp($mime, 'video/', 6) === 0);
      $isPdf   = ($mime === 'application/pdf');
      $filterKey = $isImage ? 'image' : ($isVideo ? 'video' : 'document');
    ?>
    <div class="media-card" data-media-id="<?= (int)$file['id'] ?>" data-filter="<?= $filterKey ?>">
      <?php if ($isImage): ?>
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
  <?php endif; ?>
</div>

<script>
(function() {
  // Filter tabs
  var tabs = document.querySelectorAll('.media-tab');
  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      tabs.forEach(function(t) { t.classList.remove('active'); });
      tab.classList.add('active');
      var filter = tab.dataset.filter;
      document.querySelectorAll('.media-card[data-filter]').forEach(function(card) {
        card.style.display = (filter === 'all' || card.dataset.filter === filter) ? '' : 'none';
      });
    });
  });
})();
</script>
