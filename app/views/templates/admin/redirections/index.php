<div class="page-header">
  <h1>Redirections 301</h1>
</div>

<div class="card">
  <div class="card-title">Ajouter une redirection</div>
  <form method="POST" action="/admin/redirections">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <div class="form-row">
      <div class="form-group">
        <label for="from_url">URL source</label>
        <input type="text" id="from_url" name="from_url" value="<?= htmlspecialchars($fromPrefill) ?>" placeholder="/ancien-chemin" required>
      </div>
      <div class="form-group">
        <label for="to_url">URL destination</label>
        <input type="text" id="to_url" name="to_url" placeholder="/nouveau-chemin" required>
      </div>
    </div>
    <button type="submit" class="btn">Ajouter</button>
  </form>
</div>

<div class="card">
  <?php if (empty($redirections)): ?>
    <p class="text-muted">Aucune redirection.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>URL source</th>
          <th>URL destination</th>
          <th>Créée le</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($redirections as $r): ?>
        <tr>
          <td><code><?= htmlspecialchars($r['from_url']) ?></code></td>
          <td><code><?= htmlspecialchars($r['to_url']) ?></code></td>
          <td><?= htmlspecialchars(date('d/m/Y', strtotime($r['created_at']))) ?></td>
          <td>
            <form method="POST" action="/admin/redirections/<?= (int)$r['id'] ?>/delete" class="confirm-delete">
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
