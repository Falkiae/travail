<div class="page-header">
  <h1>Nouveau menu</h1>
  <a href="/admin/menus" class="btn btn-secondary">← Retour</a>
</div>

<div class="card" style="max-width:480px;">
  <form method="post" action="/admin/menus/new">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <div class="form-group">
      <label for="name">Nom du menu</label>
      <input type="text" id="name" name="name" class="form-control" required placeholder="Ex: Navigation principale">
    </div>

    <div class="form-group">
      <label for="location">Emplacement</label>
      <select id="location" name="location" class="form-control">
        <option value="header">Header</option>
        <option value="footer">Footer</option>
      </select>
    </div>

    <div class="form-group">
      <label for="lang">Langue</label>
      <select id="lang" name="lang" class="form-control">
        <option value="fr">Français (fr)</option>
        <option value="nl">Nederlands (nl)</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Créer le menu</button>
  </form>
</div>
