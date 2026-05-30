<div class="page-header">
  <h1><?= htmlspecialchars($title) ?></h1>
  <a href="/admin/menus" class="btn btn-secondary">← Retour</a>
</div>

<form method="POST" action="/admin/menus/<?= (int)$menu['id'] ?>/edit">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div class="card">
    <div class="card-title">Items du menu</div>

    <ul id="menu-items-list" class="menu-items-list">
      <?php foreach ($items as $i => $item): ?>
      <li class="menu-item-row" draggable="true">
        <span class="block-handle" title="Déplacer">⠿</span>
        <input type="hidden" name="items[<?= $i ?>][sort_order]" data-sort-order value="<?= $i ?>">
        <input type="text" name="items[<?= $i ?>][label]" value="<?= htmlspecialchars($item['label']) ?>" placeholder="Label" style="flex:1">
        <input type="text" name="items[<?= $i ?>][url]" value="<?= htmlspecialchars($item['url'] ?? '') ?>" placeholder="URL" style="flex:2">
        <select name="items[<?= $i ?>][target]" style="width:130px">
          <option value="_self" <?= ($item['target'] ?? '_self') === '_self' ? 'selected' : '' ?>>Même onglet</option>
          <option value="_blank" <?= ($item['target'] ?? '_self') === '_blank' ? 'selected' : '' ?>>Nouvel onglet</option>
        </select>
        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('li').remove()">×</button>
      </li>
      <?php endforeach; ?>
    </ul>

    <div style="margin-top:.75rem">
      <button type="button" id="add-menu-item-btn" class="btn btn-secondary btn-sm">+ Ajouter un item</button>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Enregistrer</button>
    <a href="/admin/menus" class="btn btn-secondary">Annuler</a>
  </div>
</form>
