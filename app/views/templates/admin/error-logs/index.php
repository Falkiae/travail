<div class="page-header">
  <h1>Erreurs 404</h1>
</div>

<div class="card">
  <?php if (empty($logs)): ?>
    <p class="text-muted">Aucune erreur 404 enregistrée.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>URL</th>
          <th>Occurrences</th>
          <th>Dernière vue</th>
          <th>Referer</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
          <td><code><?= htmlspecialchars($log['url']) ?></code></td>
          <td><?= (int)$log['count'] ?></td>
          <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($log['last_seen']))) ?></td>
          <td><?= $log['referer'] ? '<small>' . htmlspecialchars($log['referer']) . '</small>' : '—' ?></td>
          <td>
            <a href="/admin/redirections?from=<?= urlencode($log['url']) ?>" class="btn btn-secondary btn-sm">Créer redirection</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
