<div class="page-header">
  <h1>Menus</h1>
</div>

<div class="card">
  <?php if (empty($menus)): ?>
    <p class="text-muted">Aucun menu. Créez-en depuis la base de données.</p>
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
        <td>
          <a href="/admin/menus/<?= (int)$menu['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier les items</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
