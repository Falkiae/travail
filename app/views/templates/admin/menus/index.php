<div class="page-header">
  <h1>Menus</h1>
  <a href="/admin/menus/new" class="btn btn-primary">+ Nouveau menu</a>
</div>

<div class="card">
  <?php if (empty($menus)): ?>
    <p class="text-muted">Aucun menu créé. Cliquez sur &laquo;&nbsp;Nouveau menu&nbsp;&raquo; pour commencer.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>Emplacement</th>
        <th>Langue</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($menus as $menu): ?>
      <tr>
        <td><?= htmlspecialchars($menu['name']) ?></td>
        <td><?= htmlspecialchars($menu['location']) ?></td>
        <td><?= htmlspecialchars(strtoupper($menu['lang'])) ?></td>
        <td style="display:flex;gap:.5rem;align-items:center;">
          <a href="/admin/menus/<?= (int)$menu['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
          <form method="post" action="/admin/menus/<?= (int)$menu['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Supprimer ce menu ?');">
            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
