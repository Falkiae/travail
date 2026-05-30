<div class="page-header">
  <h1>Blocs</h1>
</div>
<p style="color:var(--color-muted);margin-bottom:1.5rem;">Modifiez le template HTML/PHP et le CSS de chaque type de bloc.</p>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
  <?php foreach ($blockTypes as $type): ?>
  <div class="card" style="display:flex;flex-direction:column;gap:.75rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <code style="font-size:.875rem;font-weight:600;"><?= htmlspecialchars($type) ?></code>
      <?php if (!empty($blockCss[$type])): ?>
      <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;" title="CSS custom actif"></span>
      <?php endif; ?>
    </div>
    <a href="/admin/blocks/<?= htmlspecialchars($type) ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
  </div>
  <?php endforeach; ?>
</div>
