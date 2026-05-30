<div class="page-header">
  <h1>Médiathèque</h1>
</div>

<!-- Upload zone -->
<form id="upload-form" method="POST" action="/admin/media/upload" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <div id="upload-zone" class="drop-zone">
    <label for="upload-file">
      <strong>Glissez un fichier ici</strong> ou <u>cliquez pour choisir</u><br>
      <small>JPEG, PNG, GIF, WebP, PDF — 5 Mo max</small>
    </label>
    <input type="file" id="upload-file" name="file" accept="image/*,application/pdf" style="display:none">
  </div>
</form>

<!-- Hidden CSRF for AJAX -->
<input type="hidden" id="csrf-token-value" value="<?= htmlspecialchars($csrf_token) ?>">

<div class="card">
  <?php if (empty($media)): ?>
    <p class="text-muted">Aucun fichier dans la médiathèque.</p>
  <?php else: ?>
  <div class="media-grid">
    <?php foreach ($media as $file): ?>
    <div class="media-card" data-media-id="<?= (int)$file['id'] ?>">
      <?php if (str_starts_with($file['mime_type'] ?? '', 'image/')): ?>
        <img src="<?= htmlspecialchars($file['path']) ?>" alt="<?= htmlspecialchars($file['alt'] ?? '') ?>" loading="lazy">
      <?php else: ?>
        <div class="media-icon">📄</div>
      <?php endif; ?>
      <div class="media-info">
        <div class="media-name" title="<?= htmlspecialchars($file['original_name']) ?>"><?= htmlspecialchars($file['original_name']) ?></div>
        <input type="text" class="alt-input" value="<?= htmlspecialchars($file['alt'] ?? '') ?>" placeholder="Texte alternatif" style="margin-top:.3rem">
        <div class="media-actions" style="margin-top:.3rem">
          <button type="button" class="btn btn-secondary btn-sm alt-save-btn">Sauvegarder</button>
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
