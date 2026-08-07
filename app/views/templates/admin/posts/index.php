<div class="page-header">
  <h1>Articles</h1>
  <div class="d-flex gap-sm">
    <a href="/admin/categories" class="btn btn-secondary">Catégories</a>
    <a href="/admin/posts/new" class="btn">+ Nouvel article</a>
  </div>
</div>

<div class="card">
  <?php if (empty($posts)): ?>
    <p class="text-muted">Aucun article pour l'instant.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Langue</th>
          <th>Statut</th>
          <th>Date de publication</th>
          <th>Modifié le</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $post): ?>
        <tr>
          <td><strong><?= htmlspecialchars($post['title']) ?></strong></td>
          <td><?= htmlspecialchars(strtoupper($post['lang'])) ?></td>
          <td><span class="badge badge-<?= htmlspecialchars($post['status']) ?>"><?= htmlspecialchars($post['status']) ?></span></td>
          <td><?= $post['published_at'] ? htmlspecialchars(date('d/m/Y', strtotime($post['published_at']))) : '—' ?></td>
          <td><?= $post['updated_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($post['updated_at']))) : '—' ?></td>
          <td>
            <div class="d-flex gap-sm">
              <a href="/admin/posts/<?= (int)$post['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
              <form method="POST" action="/admin/posts/<?= (int)$post['id'] ?>/delete" class="confirm-delete">
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
