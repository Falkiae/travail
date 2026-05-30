<div class="page-header">
  <h1>Portfolio</h1>
  <a href="/admin/projects/new" class="btn">+ Nouveau projet</a>
</div>

<div class="card">
  <?php if (empty($projects)): ?>
    <p class="text-muted">Aucun projet pour l'instant.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Langue</th>
          <th>Tags</th>
          <th>Ordre</th>
          <th>Statut</th>
          <th>Modifié le</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($projects as $project): ?>
        <tr>
          <td><strong><?= htmlspecialchars($project['title']) ?></strong></td>
          <td><?= htmlspecialchars(strtoupper($project['lang'])) ?></td>
          <td><?= htmlspecialchars($project['tags'] ?? '') ?></td>
          <td><?= (int)($project['sort_order'] ?? 0) ?></td>
          <td><span class="badge badge-<?= htmlspecialchars($project['status']) ?>"><?= htmlspecialchars($project['status']) ?></span></td>
          <td><?= $project['updated_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($project['updated_at']))) : '—' ?></td>
          <td>
            <div class="d-flex gap-sm">
              <a href="/admin/projects/<?= (int)$project['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
              <form method="POST" action="/admin/projects/<?= (int)$project['id'] ?>/delete" class="confirm-delete">
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
