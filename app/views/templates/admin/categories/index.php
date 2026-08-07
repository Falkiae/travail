<div class="page-header">
  <h1>Catégories du blog</h1>
  <a href="/admin/posts" class="btn btn-secondary">← Retour aux articles</a>
</div>

<div class="card">
  <div class="card-title">Nouvelle catégorie</div>
  <form method="POST" action="/admin/categories" class="form-row" style="align-items:flex-end">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <div class="form-group">
      <label for="cat-name">Nom</label>
      <input type="text" id="cat-name" name="name" placeholder="Ex : Canapé" required>
    </div>
    <div class="form-group">
      <label for="cat-lang">Langue</label>
      <select id="cat-lang" name="lang">
        <option value="fr">Français</option>
        <option value="nl">Nederlands</option>
      </select>
    </div>
    <div class="form-group">
      <button type="submit" class="btn">+ Ajouter</button>
    </div>
  </form>
</div>

<div class="card">
  <?php if (empty($categories)): ?>
    <p class="text-muted">Aucune catégorie pour l'instant. Créez-en une ci-dessus pour pouvoir classer vos articles.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Slug</th>
          <th>Langue</th>
          <th>Articles</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
        <tr>
          <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
          <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
          <td><?= htmlspecialchars(strtoupper($cat['lang'])) ?></td>
          <td><?= (int) $cat['post_count'] ?></td>
          <td>
            <form method="POST" action="/admin/categories/<?= (int) $cat['id'] ?>/delete" class="confirm-delete">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
