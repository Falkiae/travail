<div class="page-header">
  <h1>Pages</h1>
  <div style="display:flex;gap:.5rem;align-items:center;">
    <form method="POST" action="/admin/pages/seed">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <button type="submit" class="btn btn-secondary btn-sm">↺ Réinitialiser les blocs par défaut</button>
    </form>
    <a href="/admin/pages/new" class="btn">+ Nouvelle page</a>
  </div>
</div>

<div class="card">
  <?php if (empty($pages)): ?>
    <p class="text-muted">Aucune page pour l'instant.</p>
    <form method="POST" action="/admin/pages/seed" style="margin-top:1rem;">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <button type="submit" class="btn">Créer les pages par défaut</button>
    </form>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Slug</th>
          <th>Template</th>
          <th>Langue</th>
          <th>Statut</th>
          <th>Modifié le</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pages as $page): ?>
        <tr>
          <td><strong><?= htmlspecialchars($page['title']) ?></strong></td>
          <td><code><?= htmlspecialchars($page['slug']) ?></code></td>
          <td><?= htmlspecialchars($page['template']) ?></td>
          <td><?= htmlspecialchars(strtoupper($page['lang'])) ?></td>
          <td><span class="badge badge-<?= htmlspecialchars($page['status']) ?>"><?= htmlspecialchars($page['status']) ?></span></td>
          <td><?= $page['updated_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($page['updated_at']))) : '—' ?></td>
          <td>
            <div class="d-flex gap-sm">
              <a href="/admin/pages/<?= (int)$page['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
              <form method="POST" action="/admin/pages/<?= (int)$page['id'] ?>/delete" class="confirm-delete">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
