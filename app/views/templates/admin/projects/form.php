<?php
$isEdit = !empty($project);
$action = $isEdit ? '/admin/projects/' . (int)$project['id'] . '/edit' : '/admin/projects/new';
$existingBlocks = $isEdit && !empty($project['content']) ? json_decode($project['content'], true) : [];
?>

<?php if ($isEdit): ?>
<script>window.existingBlocks = <?= json_encode($existingBlocks) ?>;</script>
<?php endif; ?>

<div class="page-header">
  <h1><?= htmlspecialchars($title) ?></h1>
  <a href="/admin/projects" class="btn btn-secondary">← Retour</a>
</div>

<form method="POST" action="<?= htmlspecialchars($action) ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div class="form-row">
    <div>
      <div class="card">
        <div class="form-group">
          <label for="title">Titre <span style="color:var(--color-danger)">*</span></label>
          <input type="text" id="title" name="title" value="<?= htmlspecialchars($project['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label for="slug">Slug</label>
          <div class="input-row">
            <input type="text" id="slug" name="slug"
              value="<?= htmlspecialchars($project['slug'] ?? '') ?>"
              <?= $isEdit ? 'readonly data-existing="1"' : '' ?>>
            <?php if ($isEdit): ?>
            <button type="button" id="slug-unlock" class="btn btn-secondary btn-sm">Modifier</button>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label for="excerpt">Extrait</label>
          <textarea id="excerpt" name="excerpt" rows="3"><?= htmlspecialchars($project['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="lang">Langue</label>
            <select id="lang" name="lang">
              <option value="fr" <?= ($project['lang'] ?? 'fr') === 'fr' ? 'selected' : '' ?>>Français</option>
              <option value="nl" <?= ($project['lang'] ?? 'fr') === 'nl' ? 'selected' : '' ?>>Nederlands</option>
            </select>
          </div>
          <div class="form-group">
            <label for="status">Statut</label>
            <select id="status" name="status">
              <option value="draft" <?= ($project['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
              <option value="published" <?= ($project['status'] ?? 'draft') === 'published' ? 'selected' : '' ?>>Publié</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="tags">Tags <span class="help-text">(séparés par des virgules)</span></label>
            <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($project['tags'] ?? '') ?>" placeholder="design, web, branding">
          </div>
          <div class="form-group">
            <label for="sort_order">Ordre d'affichage</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= (int)($project['sort_order'] ?? 0) ?>" min="0">
          </div>
        </div>

        <div class="form-group">
          <label>Thumbnail</label>
          <div class="input-row">
            <input type="text" id="thumbnail_display" value="" readonly placeholder="Aucune image choisie">
            <input type="hidden" id="thumbnail" name="thumbnail" value="<?= (int)($project['thumbnail'] ?? 0) ?>">
            <button type="button" class="btn btn-secondary btn-sm" onclick="openMediaModal(function(m){ document.getElementById('thumbnail').value=m.id; document.getElementById('thumbnail_display').value=m.original_name; })">Médiathèque</button>
          </div>
        </div>
      </div>

      <!-- Éditeur de blocs -->
      <div class="card">
        <div class="card-title">Contenu</div>
        <div id="block-editor">
          <div class="block-editor-toolbar" id="block-editor-toolbar">
            <div style="position:relative">
              <button type="button" id="add-block-btn" class="btn btn-secondary btn-sm">+ Ajouter un bloc</button>
              <div id="block-add-menu" class="block-add-menu">
                <button type="button" data-type="heading">Titre</button>
                <button type="button" data-type="text">Texte</button>
                <button type="button" data-type="image">Image</button>
                <button type="button" data-type="cta">Bouton CTA</button>
                <button type="button" data-type="html">HTML libre</button>
                <button type="button" data-type="video">Vidéo</button>
                <button type="button" data-type="file">Fichier</button>
                <button type="button" data-type="accordion">Accordéon</button>
                <button type="button" data-type="quote">Citation</button>
                <button type="button" data-type="pricing">Grille de tarifs</button>
                <button type="button" data-type="before-after">Avant / Apres</button>
                <button type="button" data-type="logos">Logos partenaires</button>
                <button type="button" data-type="seo-content">Contenu SEO</button>
              </div>
            </div>
          </div>
          <div id="block-list"></div>
        </div>
        <input type="hidden" name="content" value="<?= htmlspecialchars($project['content'] ?? '') ?>">
      </div>
    </div>

    <div>
      <div class="card">
        <div class="card-title">SEO</div>
        <div class="form-group">
          <label for="meta_title">Meta title</label>
          <input type="text" id="meta_title" name="meta_title"
            value="<?= htmlspecialchars($project['meta_title'] ?? '') ?>"
            data-maxlength="70">
        </div>
        <div class="form-group">
          <label for="meta_description">Meta description</label>
          <textarea id="meta_description" name="meta_description"
            data-maxlength="160" rows="3"><?= htmlspecialchars($project['meta_description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label for="og_image">Image OG (URL)</label>
          <input type="text" id="og_image" name="og_image"
            value="<?= htmlspecialchars($project['og_image'] ?? '') ?>">
        </div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" name="status" value="draft" class="btn btn-secondary">Enregistrer (brouillon)</button>
    <button type="submit" name="publish" value="1" class="btn">Publier</button>
  </div>
</form>
